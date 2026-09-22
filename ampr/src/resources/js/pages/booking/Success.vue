<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    booking: Object,
    qrCode: String,
});

const showQrModal = ref(false);

const formattedDate = computed(() => {
    return new Date(props.booking.start_time).toLocaleDateString('en-GB', {
        weekday: 'long',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
});

const formattedTime = computed(() => {
    const s = new Date(props.booking.start_time);
    const e = new Date(props.booking.end_time);
    const opt = { hour: '2-digit', minute: '2-digit', hour12: false };
    return `${s.toLocaleTimeString('en-GB', opt)} - ${e.toLocaleTimeString('en-GB', opt)}`;
});
</script>

<template>
    <Head title="Booking Successful" />

    <!-- WRAPPER UTAMA: h-[100dvh] agar pas layar & overflow-hidden -->
    <div
        class="flex h-[100dvh] justify-center overflow-hidden bg-[#EAEFF5] font-sans text-[#1A5F7A] antialiased"
    >
        <div
            class="relative flex h-full w-full max-w-[480px] flex-col bg-[#F8FAFC] p-6 shadow-2xl"
        >
            <!-- 1. HEADER SECTION (Compact) -->
            <div class="mb-4 flex flex-none flex-col items-center pt-4">
                <!-- Icon -->
                <div class="relative mb-3">
                    <div
                        class="absolute inset-0 animate-pulse rounded-full bg-[#BEF264] opacity-40 blur-xl"
                    ></div>
                    <div
                        class="relative z-10 flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-md shadow-[#1A5F7A]/10"
                    >
                        <svg
                            class="h-8 w-8 text-[#1A5F7A]"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="3"
                                d="M5 13l4 4L19 7"
                            ></path>
                        </svg>
                    </div>
                </div>

                <h1
                    class="mb-1 text-2xl leading-none font-black tracking-tight text-[#1A5F7A]"
                >
                    Booking Confirmed!
                </h1>
                <p class="text-center text-xs font-medium text-slate-400">
                    Please save your ticket below.
                </p>
            </div>

            <!-- 2. TICKET CARD (Flex Grow / Auto Center) -->
            <div
                class="flex min-h-0 w-full flex-1 flex-col justify-center py-2"
            >
                <div
                    class="relative w-full rounded-[2rem] shadow-xl shadow-slate-200/60"
                >
                    <!-- Top Part -->
                    <div class="relative z-10 rounded-t-[2rem] bg-white p-5">
                        <!-- Header Ticket -->
                        <div class="mb-4 flex items-start justify-between">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-[#EAEFF5] text-[#1A5F7A]"
                                >
                                    <svg
                                        class="h-4 w-4"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div>
                                    <h3
                                        class="text-xs leading-tight font-black tracking-wide text-[#1A5F7A] uppercase"
                                    >
                                        Tennis Court
                                    </h3>
                                    <span
                                        class="rounded-md bg-[#BEF264] px-1.5 py-0.5 text-[9px] font-bold tracking-wider text-[#1A5F7A] uppercase"
                                        >Confirmed</span
                                    >
                                </div>
                            </div>
                            <!-- Mini QR -->
                            <button
                                @click="showQrModal = true"
                                class="rounded-xl bg-[#F1F5F9] p-1.5 transition-all hover:bg-[#E2E8F0] active:scale-95"
                            >
                                <img
                                    :src="qrCode"
                                    class="h-6 w-6 mix-blend-multiply"
                                />
                            </button>
                        </div>

                        <!-- Date & Time (Compact) -->
                        <div class="mb-5 text-center">
                            <h2
                                class="text-xl leading-tight font-black text-[#1A5F7A]"
                            >
                                {{ formattedDate }}
                            </h2>
                            <p class="mt-1 text-sm font-bold text-slate-400">
                                {{ formattedTime }}
                            </p>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-2 gap-3">
                            <div
                                class="rounded-2xl border border-slate-100 bg-[#F8FAFC] p-2.5"
                            >
                                <p
                                    class="mb-0.5 text-[9px] font-bold tracking-wider text-slate-400 uppercase"
                                >
                                    UNIT
                                </p>
                                <p
                                    class="truncate text-sm font-black text-[#1A5F7A]"
                                >
                                    {{ booking.unit?.unit_number }}
                                </p>
                            </div>
                            <div
                                class="rounded-2xl border border-slate-100 bg-[#F8FAFC] p-2.5"
                            >
                                <p
                                    class="mb-0.5 text-[9px] font-bold tracking-wider text-slate-400 uppercase"
                                >
                                    CODE
                                </p>
                                <p
                                    class="truncate font-mono text-sm font-black tracking-tighter text-[#1A5F7A]"
                                >
                                    {{
                                        booking.booking_code.substring(0, 8)
                                    }}...
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Divider (Tighter) -->
                    <div
                        class="relative z-20 -my-[1px] flex h-4 items-center bg-[#EAEFF5]"
                    >
                        <div
                            class="absolute -left-2 h-4 w-4 rounded-full bg-[#F8FAFC]"
                        ></div>
                        <div
                            class="absolute -right-2 h-4 w-4 rounded-full bg-[#F8FAFC]"
                        ></div>
                        <div class="relative mx-2 h-full w-full bg-white">
                            <div
                                class="absolute top-1/2 w-full -translate-y-1/2 border-t-2 border-dashed border-slate-200"
                            ></div>
                        </div>
                    </div>

                    <!-- Bottom Part -->
                    <div
                        class="relative z-10 rounded-b-[2rem] bg-white p-5 pt-3"
                    >
                        <p
                            class="mb-2 text-[9px] font-bold tracking-wider text-slate-400 uppercase"
                        >
                            PLAYERS
                        </p>
                        <div class="flex items-center gap-2">
                            <div
                                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#1A5F7A] text-[10px] font-bold text-white"
                            >
                                {{
                                    (Array.isArray(booking.player_names)
                                        ? booking.player_names[0]
                                        : booking.player_names
                                    ).charAt(0)
                                }}
                            </div>
                            <p
                                class="truncate text-sm font-bold text-[#1A5F7A]"
                            >
                                {{
                                    Array.isArray(booking.player_names)
                                        ? booking.player_names[0]
                                        : booking.player_names
                                }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. FOOTER ACTIONS (Pinned Bottom) -->
            <div class="pb-safe mt-2 w-full flex-none space-y-3">
                <a
                    :href="`/ticket/${booking.booking_code}/pdf`"
                    target="_blank"
                    class="flex w-full items-center justify-center gap-2 rounded-2xl bg-[#1A5F7A] py-3.5 font-bold text-white shadow-xl shadow-[#1A5F7A]/20 transition-all hover:bg-[#164e63] active:scale-[0.98]"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                        ></path>
                    </svg>
                    Download E-Ticket
                </a>

                <Link
                    href="/booking"
                    class="block w-full rounded-2xl border border-slate-200 py-3.5 text-center text-sm font-bold text-slate-400 transition-colors hover:border-[#1A5F7A] hover:text-[#1A5F7A]"
                >
                    Back to Home
                </Link>
            </div>
        </div>

        <!-- MODAL QR (Tetap Sama) -->
        <transition name="modal">
            <div
                v-if="showQrModal"
                class="fixed inset-0 z-[60] flex items-center justify-center p-6"
            >
                <div
                    @click="showQrModal = false"
                    class="absolute inset-0 bg-[#1A5F7A]/80 backdrop-blur-sm"
                ></div>
                <div
                    class="animate-scale-up relative w-full max-w-[320px] rounded-[2rem] bg-white p-8 text-center shadow-2xl"
                >
                    <div
                        class="mx-auto mb-6 h-1 w-12 rounded-full bg-slate-200"
                    ></div>
                    <h3 class="mb-1 text-xl font-black text-[#1A5F7A]">
                        Scan Entry Code
                    </h3>
                    <p class="mb-6 text-xs text-slate-400">
                        Show this to the gate keeper
                    </p>

                    <div
                        class="relative mb-6 inline-block overflow-hidden rounded-[1.5rem] border-2 border-[#1A5F7A]/10 bg-white p-4 shadow-inner"
                    >
                        <div
                            class="animate-scan absolute top-0 left-0 h-1 w-full bg-[#BEF264]/80 shadow-[0_0_15px_rgba(190,242,100,0.8)]"
                        ></div>
                        <img
                            :src="qrCode"
                            class="h-48 w-48 mix-blend-multiply"
                        />
                    </div>

                    <div
                        class="mb-6 rounded-xl border border-slate-200 bg-[#F1F5F9] py-3"
                    >
                        <p
                            class="mb-1 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            BOOKING CODE
                        </p>
                        <p
                            class="font-mono text-xl font-black tracking-widest text-[#1A5F7A]"
                        >
                            {{ booking.booking_code }}
                        </p>
                    </div>

                    <button
                        @click="showQrModal = false"
                        class="w-full rounded-xl bg-[#1A5F7A] py-4 font-bold text-white shadow-lg shadow-[#1A5F7A]/20 transition-colors hover:bg-[#164e63]"
                    >
                        Close
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
@keyframes scale-up {
    from {
        transform: scale(0.9);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}
.animate-scale-up {
    animation: scale-up 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes scan {
    0% {
        top: 0;
        opacity: 0;
    }
    10% {
        opacity: 1;
    }
    90% {
        opacity: 1;
    }
    100% {
        top: 100%;
        opacity: 0;
    }
}
.animate-scan {
    animation: scan 2.5s linear infinite;
}
/* Untuk iPhone Notch */
.pb-safe {
    padding-bottom: env(safe-area-inset-bottom, 20px);
}
</style>
