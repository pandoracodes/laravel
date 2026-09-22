<script setup>
/**
 * SecurityScanner.vue — Zero-dependency QR scanner
 *
 * Strategy (in order of preference):
 *   1. BarcodeDetector API  — native, fast, works on Chrome/Edge/Android/Safari 17+
 *   2. jsQR (CDN)           — pure-JS fallback, works everywhere including Safari < 17
 *
 * Camera is driven 100% by native getUserMedia, so there is NO library to fight
 * the DOM, no hidden iframes, no race conditions with Vue lifecycle.
 */
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Shield } from 'lucide-vue-next';
import { onMounted, onUnmounted, ref } from 'vue';

// ─── Refs: UI ────────────────────────────────────────────────────────────────
const manualCode  = ref('');
const scanResult  = ref(null);
const scanStatus  = ref('');
const showModal   = ref(false);
const cameraState = ref('idle');   // idle | requesting | active | error
const cameraMsg   = ref('');
const torchOn     = ref(false);
const hasTorch    = ref(false);

// ─── Refs: DOM ───────────────────────────────────────────────────────────────
const videoEl  = ref(null);
const canvasEl = ref(null);

// ─── Internals ───────────────────────────────────────────────────────────────
let stream        = null;
let rafId         = null;
let jsQR          = null;     // loaded lazily
let barcodeReader = null;     // BarcodeDetector instance
let scanning      = false;    // gate: prevent double-decode
let track         = null;     // active video track (for torch)

// ─── Lifecycle ───────────────────────────────────────────────────────────────
onMounted(startCamera);
onUnmounted(destroyCamera);

// ─── Camera bootstrap ────────────────────────────────────────────────────────
async function startCamera() {
    cameraState.value = 'requesting';
    cameraMsg.value   = '';

    /* getUserMedia is required. If missing (HTTP, old browser) bail early */
    if (!navigator.mediaDevices?.getUserMedia) {
        cameraState.value = 'error';
        cameraMsg.value   = 'Browser Anda tidak mendukung akses kamera. Gunakan Chrome, Safari, atau Firefox versi terbaru. Pastikan halaman dibuka via HTTPS.';
        return;
    }

    try {
        /* Prefer rear camera, fall back to any camera */
        stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: 'environment' },
                width:  { ideal: 1280 },
                height: { ideal: 720 },
            },
            audio: false,
        });
    } catch (err) {
        cameraState.value = 'error';
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            cameraMsg.value = 'Izin kamera ditolak. Buka Pengaturan browser → Izin Situs → aktifkan Kamera untuk halaman ini, lalu refresh.';
        } else if (err.name === 'NotFoundError') {
            cameraMsg.value = 'Tidak ada kamera yang ditemukan pada perangkat ini.';
        } else if (err.name === 'NotReadableError' || err.name === 'AbortError') {
            cameraMsg.value = 'Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi lain yang menggunakan kamera, lalu coba lagi.';
        } else {
            cameraMsg.value = `Gagal mengakses kamera: ${err.message}`;
        }
        return;
    }

    /* Attach stream to <video> */
    const video = videoEl.value;
    video.srcObject = stream;
    video.setAttribute('playsinline', '');   // required on iOS Safari
    video.setAttribute('muted', '');
    video.muted = true;

    try {
        await video.play();
    } catch (playErr) {
        /* Some browsers need a user gesture — show a tap-to-start button */
        cameraState.value = 'error';
        cameraMsg.value   = 'Ketuk "Mulai Kamera" untuk mengaktifkan scanner.';
        return;
    }

    /* Check torch support */
    track = stream.getVideoTracks()[0];
    const caps = track?.getCapabilities?.() ?? {};
    hasTorch.value = !!(caps.torch);

    cameraState.value = 'active';

    /* Init decoder then start scan loop */
    await initDecoder();
    startScanLoop();
}

async function initDecoder() {
    /* Try native BarcodeDetector first */
    if ('BarcodeDetector' in window) {
        try {
            const supported = await BarcodeDetector.getSupportedFormats();
            if (supported.includes('qr_code')) {
                barcodeReader = new BarcodeDetector({ formats: ['qr_code'] });
                return;
            }
        } catch (_) {}
    }

    /* Fall back to jsQR via CDN */
    if (!jsQR) {
        jsQR = await loadJsQR();
    }
}

function loadJsQR() {
    return new Promise((resolve, reject) => {
        if (window.jsQR) { resolve(window.jsQR); return; }
        const s = document.createElement('script');
        s.src   = 'https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js';
        s.onload  = () => resolve(window.jsQR);
        s.onerror = () => reject(new Error('jsQR load failed'));
        document.head.appendChild(s);
    });
}

// ─── Scan loop ───────────────────────────────────────────────────────────────
function startScanLoop() {
    const video  = videoEl.value;
    const canvas = canvasEl.value;
    const ctx    = canvas.getContext('2d', { willReadFrequently: true });

    async function tick() {
        if (!video || video.readyState < 2) { rafId = requestAnimationFrame(tick); return; }
        if (scanning) { rafId = requestAnimationFrame(tick); return; }

        /* Match canvas to video resolution */
        if (canvas.width !== video.videoWidth) {
            canvas.width  = video.videoWidth  || 640;
            canvas.height = video.videoHeight || 480;
        }

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        let decoded = null;

        try {
            if (barcodeReader) {
                /* Native BarcodeDetector path */
                const results = await barcodeReader.detect(canvas);
                if (results.length > 0) decoded = results[0].rawValue;
            } else if (jsQR) {
                /* jsQR path */
                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const result  = jsQR(imgData.data, imgData.width, imgData.height, {
                    inversionAttempts: 'dontInvert',
                });
                if (result) decoded = result.data;
            }
        } catch (_) { /* decoder errors are per-frame, ignore */ }

        if (decoded) {
            onQRDetected(decoded);
            return; // stop loop — will resume after modal closes
        }

        rafId = requestAnimationFrame(tick);
    }

    rafId = requestAnimationFrame(tick);
}

function stopScanLoop() {
    if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
}

function resumeScanLoop() {
    scanning = false;
    if (cameraState.value === 'active') startScanLoop();
}

function destroyCamera() {
    stopScanLoop();
    if (stream) {
        stream.getTracks().forEach(t => t.stop());
        stream = null;
    }
    if (videoEl.value) videoEl.value.srcObject = null;
}

// ─── QR detected ─────────────────────────────────────────────────────────────
function onQRDetected(text) {
    stopScanLoop();
    validateTicket(text);
}

// ─── Torch toggle ─────────────────────────────────────────────────────────────
async function toggleTorch() {
    if (!track) return;
    torchOn.value = !torchOn.value;
    try {
        await track.applyConstraints({ advanced: [{ torch: torchOn.value }] });
    } catch (_) {
        torchOn.value = !torchOn.value;
    }
}

// ─── Retry (after error) ─────────────────────────────────────────────────────
async function retryCamera() {
    destroyCamera();
    scanning      = false;
    barcodeReader = null;
    await startCamera();
}

// ─── Business logic ───────────────────────────────────────────────────────────
const validateTicket = async (code) => {
    if (!code?.trim()) return;
    scanning = true;

    try {
        const response = await axios.post(route('security.validate'), {
            booking_code: code.trim(),
        });
        scanResult.value = response.data.data;
        scanStatus.value  = response.data.status;
    } catch (error) {
        scanStatus.value  = 'error';
        scanResult.value  = {
            message: error.response?.data?.message || 'Tiket Tidak Ditemukan',
        };
    }

    showModal.value = true;
};

const processCheckIn = () => {
    router.post(
        route('security.checkin'),
        { booking_code: scanResult.value.booking_code },
        {
            onSuccess: () => {
                showModal.value = false;
                alert('Check-In Berhasil!');
                resumeScanLoop();
            },
            onError: () => {
                alert('Gagal melakukan Check-In. Coba lagi.');
            },
        },
    );
};

const closeModal = () => {
    showModal.value  = false;
    manualCode.value = '';
    resumeScanLoop();
};
</script>

<template>
    <Head title="Security Scanner" />

    <!-- Outer dark shell (desktop) -->
    <div class="flex min-h-screen w-full items-center justify-center bg-gray-900 font-sans">

        <!-- Mobile card -->
        <div class="relative flex h-[100dvh] w-full max-w-[480px] flex-col overflow-hidden bg-[#EAEFF5] text-[#1A5F7A] shadow-2xl">

            <!-- ── HEADER ──────────────────────────────────────────────────── -->
            <div class="relative z-20 flex-none p-6 pb-2">
                <div class="flex w-full items-center justify-between rounded-[2rem] border border-white bg-white p-4 shadow-xl shadow-[#1A5F7A]/5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#1A5F7A] text-white shadow-md">
                            <Shield class="h-6 w-6" />
                        </div>
                        <div>
                            <h1 class="text-lg leading-none font-black tracking-tight text-[#1A5F7A]">Security Access</h1>
                            <p class="mt-1 inline-block rounded-md bg-[#1A5F7A] px-2 py-0.5 text-[10px] font-bold tracking-widest text-[#BEF264] uppercase">
                                Mediterania Court
                            </p>
                        </div>
                    </div>
                    <button
                        @click="router.post('/logout')"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-[#FFE5E5] text-[#EF583D] transition-colors hover:bg-[#ffcccc]"
                        title="Logout"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- ── SCANNER AREA ────────────────────────────────────────────── -->
            <div class="relative flex min-h-0 flex-1 flex-col px-6 pb-2">
                <div class="scanner-shell relative h-full w-full overflow-hidden rounded-[2.5rem] border-4 border-white bg-black shadow-2xl">

                    <!-- Video element (always in DOM, hidden until active) -->
                    <video
                        ref="videoEl"
                        class="absolute inset-0 h-full w-full object-cover"
                        :class="{ 'opacity-0': cameraState !== 'active' }"
                        playsinline
                        muted
                        autoplay
                    />

                    <!-- Hidden canvas for frame capture -->
                    <canvas ref="canvasEl" class="hidden" />

                    <!-- ── State overlays ──────────────────────────────────── -->

                    <!-- REQUESTING -->
                    <div v-if="cameraState === 'requesting'"
                        class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-4 bg-gray-950 p-8 text-center">
                        <div class="relative h-16 w-16">
                            <div class="absolute inset-0 animate-ping rounded-full bg-[#BEF264]/30"></div>
                            <div class="relative flex h-16 w-16 items-center justify-center rounded-full bg-[#1A5F7A]">
                                <svg class="h-8 w-8 text-[#BEF264]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-white/80 leading-relaxed">Meminta izin kamera…</p>
                        <p class="text-xs text-white/40">Ketuk "Izinkan" pada dialog browser</p>
                    </div>

                    <!-- ERROR -->
                    <div v-if="cameraState === 'error'"
                        class="absolute inset-0 z-10 flex flex-col items-center justify-center gap-4 bg-gray-950 p-8 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-red-900/40">
                            <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-white leading-relaxed">{{ cameraMsg }}</p>
                        <button @click="retryCamera"
                            class="mt-2 rounded-2xl bg-[#1A5F7A] px-6 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-[#154c61] active:scale-95">
                            Coba Lagi
                        </button>
                        <p class="text-xs text-white/40">atau gunakan input manual di bawah</p>
                    </div>

                    <!-- ACTIVE: scan overlay + torch button -->
                    <div v-if="cameraState === 'active'"
                        class="pointer-events-none absolute inset-0 z-10 flex flex-col items-center justify-center">

                        <!-- Corner frame -->
                        <div class="relative h-56 w-56">
                            <div class="absolute top-0 left-0  h-9 w-9 rounded-tl-2xl border-t-4 border-l-4 border-[#BEF264]"></div>
                            <div class="absolute top-0 right-0 h-9 w-9 rounded-tr-2xl border-t-4 border-r-4 border-[#BEF264]"></div>
                            <div class="absolute bottom-0 left-0  h-9 w-9 rounded-bl-2xl border-b-4 border-l-4 border-[#BEF264]"></div>
                            <div class="absolute bottom-0 right-0 h-9 w-9 rounded-br-2xl border-b-4 border-r-4 border-[#BEF264]"></div>
                            <!-- Laser scan line -->
                            <div class="animate-scan absolute left-0 h-0.5 w-full bg-[#BEF264] shadow-[0_0_12px_4px_rgba(190,242,100,0.7)]"></div>
                        </div>

                        <p class="mt-5 text-xs font-bold tracking-widest text-white/50 uppercase">Arahkan QR ke dalam kotak</p>
                    </div>

                    <!-- Torch button (pointer-events enabled separately) -->
                    <button v-if="cameraState === 'active' && hasTorch"
                        @click="toggleTorch"
                        class="absolute right-4 top-4 z-20 flex h-10 w-10 items-center justify-center rounded-full shadow-lg transition active:scale-95"
                        :class="torchOn ? 'bg-[#BEF264] text-[#1A5F7A]' : 'bg-black/40 text-white'"
                        title="Senter"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </button>

                </div>
            </div>

            <!-- ── MANUAL INPUT ────────────────────────────────────────────── -->
            <div class="relative z-20 flex-none p-6 pt-2 pb-8">
                <div class="flex w-full gap-2 rounded-[2rem] border border-white bg-white p-2 shadow-xl shadow-[#1A5F7A]/10">
                    <div class="relative flex-1">
                        <div class="absolute top-1/2 left-4 -translate-y-1/2 text-slate-300">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input
                            v-model="manualCode"
                            type="text"
                            placeholder="Input Kode Manual..."
                            class="h-14 w-full rounded-[1.5rem] border-none bg-[#F1F5F9] pr-4 pl-12 font-bold tracking-widest text-[#1A5F7A] placeholder-slate-400 transition-all focus:outline-none focus:ring-2 focus:ring-[#BEF264]"
                            @keyup.enter="validateTicket(manualCode)"
                        />
                    </div>
                    <button
                        @click="validateTicket(manualCode)"
                        class="flex h-14 w-14 items-center justify-center rounded-[1.5rem] bg-[#1A5F7A] text-[#BEF264] shadow-lg transition hover:bg-[#154c61] active:scale-95"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- ── RESULT MODAL ────────────────────────────────────────────── -->
            <transition name="modal">
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-6">

                    <!-- Backdrop -->
                    <div class="absolute inset-0 flex justify-center bg-gray-900/80 backdrop-blur-sm" @click="closeModal">
                        <div class="h-full w-full max-w-[480px]"></div>
                    </div>

                    <!-- Card -->
                    <div class="animate-pop-up relative z-50 w-full max-w-xs overflow-hidden rounded-[2.5rem] bg-white shadow-2xl">

                        <!-- Status header -->
                        <div class="p-8 pb-6 text-center"
                            :class="{
                                'bg-[#F0FDF4]': scanStatus === 'success',
                                'bg-[#FFF7ED]': scanStatus === 'warning',
                                'bg-[#FEF2F2]': scanStatus === 'error',
                            }">
                            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full border-4 border-white bg-white shadow-sm">
                                <svg v-if="scanStatus === 'success'" class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                </svg>
                                <svg v-else-if="scanStatus === 'warning'" class="h-10 w-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <svg v-else class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </div>

                            <h2 class="text-2xl font-black tracking-tight"
                                :class="{
                                    'text-green-700':  scanStatus === 'success',
                                    'text-orange-600': scanStatus === 'warning',
                                    'text-red-600':    scanStatus === 'error',
                                }">
                                {{ scanStatus === 'success' ? 'ACCESS GRANTED' : scanStatus === 'warning' ? 'WARNING' : 'ACCESS DENIED' }}
                            </h2>
                            <p class="mt-1 text-xs font-bold tracking-wide text-slate-500 uppercase">
                                {{ scanResult?.message }}
                            </p>
                        </div>

                        <!-- Detail rows -->
                        <div v-if="scanStatus !== 'error'" class="space-y-3 p-6">
                            <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-[#F8FAFC] p-4">
                                <span class="text-xs font-bold tracking-widest text-slate-400 uppercase">UNIT</span>
                                <span class="text-xl font-black text-[#1A5F7A]">{{ scanResult?.unit }}</span>
                            </div>
                            <div class="flex items-center justify-between rounded-2xl border border-slate-100 bg-[#F8FAFC] p-4">
                                <span class="text-xs font-bold tracking-widest text-slate-400 uppercase">TIME</span>
                                <span class="text-lg font-black text-[#1A5F7A]">{{ scanResult?.time }}</span>
                            </div>
                            <button
                                v-if="scanStatus === 'success'"
                                @click="processCheckIn"
                                class="mt-2 w-full rounded-2xl bg-[#1A5F7A] py-4 text-lg font-bold text-white shadow-xl shadow-[#1A5F7A]/30 transition-all hover:bg-[#154c61] active:scale-95"
                            >
                                CONFIRM CHECK-IN
                            </button>
                        </div>

                        <div class="p-6 pt-0">
                            <button
                                @click="closeModal"
                                class="w-full rounded-2xl border-2 border-slate-100 bg-white py-4 font-bold text-slate-400 transition-colors hover:border-[#1A5F7A] hover:text-[#1A5F7A]"
                            >
                                CLOSE / SCAN NEXT
                            </button>
                        </div>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
/* Scan laser animation */
@keyframes scan {
    0%   { top: 0%;   opacity: 0; }
    8%   { opacity: 1; }
    92%  { opacity: 1; }
    100% { top: 100%; opacity: 0; }
}
.animate-scan {
    animation: scan 2.5s ease-in-out infinite;
    position: absolute;
    left: 0;
    width: 100%;
}

/* Modal fade */
.modal-enter-active,
.modal-leave-active { transition: opacity 0.25s ease; }
.modal-enter-from,
.modal-leave-to     { opacity: 0; }

/* Card pop-up */
@keyframes popUp {
    from { transform: scale(0.88) translateY(8px); opacity: 0; }
    to   { transform: scale(1)    translateY(0);   opacity: 1; }
}
.animate-pop-up {
    animation: popUp 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* Ensure video fills container cleanly */
video {
    transition: opacity 0.4s ease;
}
</style>
