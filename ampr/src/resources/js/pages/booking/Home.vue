<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// --- PROPS DARI BACKEND ---
const props = defineProps({
    dates: { type: Array, default: () => [] },
    bookedSlots: { type: Array, default: () => [] },
    fullyBookedDates: { type: Array, default: () => [] },
    userUnit: { type: String, default: 'GUEST' },

    // Props Baru untuk Fitur Banned
    isUserBanned: { type: Boolean, default: false },
    banMessage: { type: String, default: '' },
});

const todayStr = new Date().toISOString().split('T')[0];
const selectedDate = ref(
    props.dates.length > 0 ? props.dates[0].full_date : todayStr,
);

// State untuk Modal
const showModal = ref(false); // Modal Form Booking
const showBanModal = ref(false); // Modal Peringatan Banned
const selectedSlot = ref(null);

const form = useForm({
    player_names: '',
    date: '',
    hour: '',
    unit_number: props.userUnit,
    agree_terms: false,
});

const allSlots = [6, 7, 8, 9, 10, 11, 15, 16, 17, 18, 19, 20, 21];

// --- LOGIC STATUS & WARNA ---
const getSlotData = (hour) => {
    // Cari data booking dari props backend
    const slot = props.bookedSlots.find(
        (s) => s.date === selectedDate.value && s.hour === hour,
    );

    const now = new Date();
    const localToday = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
        .toISOString()
        .split('T')[0];
    const currentHour = now.getHours();

    // 1. EXPIRED (MERAH)
    if (
        selectedDate.value < localToday ||
        (selectedDate.value === localToday && hour <= currentHour)
    ) {
        return { type: 'past', label: 'EXPIRED', sub: 'Time passed' };
    }

    if (slot) {
        // 2. MAINTENANCE (ABU-ABU)
        if (slot.status === 'maintenance') {
            return {
                type: 'maintenance',
                label: 'MAINTENANCE',
                sub: slot.info || 'Under repair',
            };
        }

        // 3. BOOKED (HIJAU)
        // Mengambil unit_number dari backend
        const unitName = slot.unit_number || slot.unit || 'Occupied';
        return { type: 'booked', label: 'BOOKED', sub: `By ${unitName}` };
    }

    // 4. AVAILABLE (BIRU)
    return { type: 'available', label: 'AVAILABLE', sub: 'Tap to book' };
};

// --- LOGIC KLIK BOOKING ---
const openBooking = (hour) => {
    const data = getSlotData(hour);

    // Cuma bisa klik yang Available
    if (data.type !== 'available') return;

    // CEK STATUS BANNED
    if (props.isUserBanned) {
        showBanModal.value = true; // Munculkan popup dilarang
        return;
    }

    // Lanjut Buka Form
    const start = String(hour).padStart(2, '0') + '.00';
    const end = String(hour + 1).padStart(2, '0') + '.00';
    selectedSlot.value = { hour, label: `${start} - ${end}` };

    form.date = selectedDate.value;
    form.hour = hour;
    form.clearErrors();
    showModal.value = true;
};

const submitBooking = () => {
    form.post('/booking', {
        onSuccess: () => (showModal.value = false),
        onFinish: () => form.reset('access_code'),
    });
};

const formatDateDisplay = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-GB', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
    });
};
</script>
<template>
    <Head title="Booking Lapangan" />

    <!-- MAIN CONTAINER -->
    <div
        class="flex min-h-screen justify-center bg-[#F3F6F9] font-sans text-[#155E75] antialiased"
    >
        <div
            class="relative flex min-h-screen w-full max-w-[480px] flex-col overflow-hidden bg-[#F8FAFC] shadow-2xl"
        >
            <!-- 1. HEADER (Decorated with Tennis Elements) -->
            <header
                class="relative z-10 overflow-hidden rounded-b-[2.5rem] bg-[#155E75] px-6 pt-12 pb-8 shadow-xl"
            >
                <!-- DEKORASI BACKGROUND (Raket & Bola) -->
                <!-- BOLA 1: Kiri Bawah (Ukuran proporsional & Bentuk Realistis) -->
                <svg
                    class="pointer-events-none absolute -top-4 -left-6 h-32 w-32 rotate-12 text-[#BEF264] opacity-[0.06]"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                >
                    <circle cx="12" cy="12" r="10" />
                    <path
                        d="M2 12 C 7 12 11 8 11 2"
                        fill="none"
                        stroke="#155E75"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        opacity="0.6"
                    />
                    <path
                        d="M22 12 C 17 12 13 16 13 22"
                        fill="none"
                        stroke="#155E75"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        opacity="0.6"
                    />
                </svg>

                <!-- BOLA 2: Tengah Kanan (Di antara Unit Badge & Kalender) -->
                <!-- top-24 menurunkannya agar ada di bawah Badge Unit, tapi di atas Kalender -->
                <svg
                    class="pointer-events-none absolute top-24 -right-4 h-24 w-24 rotate-[-15deg] text-[#BEF264] opacity-[0.08]"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                >
                    <circle cx="12" cy="12" r="10" />
                    <path
                        d="M2 12 C 7 12 11 8 11 2"
                        fill="none"
                        stroke="#155E75"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        opacity="0.6"
                    />
                    <path
                        d="M22 12 C 17 12 13 16 13 22"
                        fill="none"
                        stroke="#155E75"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        opacity="0.6"
                    />
                </svg>

                <div class="relative z-10">
                    <!-- Top Bar -->
                    <div class="mb-8 flex items-start justify-between">
                        <div>
                            <p
                                class="mb-1 text-[11px] font-bold tracking-[0.15em] text-[#BEF264] uppercase drop-shadow-sm"
                            >
                                Mediterania Court
                            </p>
                            <h1
                                class="text-3xl font-black tracking-tight text-white"
                            >
                                Match Schedule
                            </h1>
                            <p
                                class="mt-1 text-xs font-semibold tracking-wide text-[#A5F3FC]"
                            >
                                Play smart. Book faster.
                            </p>
                        </div>
                        <!-- User Badge (Glassmorphism) -->
                        <div
                            class="rounded-2xl border border-white/20 bg-white/10 px-4 py-2 text-center shadow-lg backdrop-blur-md"
                        >
                            <span
                                class="block text-[9px] font-bold tracking-wide text-white/80 uppercase"
                                >UNIT ANDA</span
                            >
                            <span
                                class="mt-0.5 block text-lg leading-none font-black text-white"
                                >{{ userUnit }}</span
                            >
                        </div>
                    </div>

                    <!-- Date Picker (Capsule Style) -->
                    <div
                        class="no-scrollbar flex snap-x gap-3 overflow-x-auto pb-2"
                    >
                        <button
                            v-for="day in dates"
                            :key="day.full_date"
                            @click="selectedDate = day.full_date"
                            class="group relative flex h-[70px] w-[62px] flex-none snap-start flex-col items-center overflow-hidden rounded-[1.4rem] border pt-2 transition-all duration-300"
                            :class="[
                                selectedDate === day.full_date
                                    ? 'border-[#BEF264] bg-[#BEF264] text-[#155E75] shadow-lg'
                                    : 'border-transparent bg-[#1E6F8B] text-white/80 hover:bg-[#257C99]',
                            ]"
                        >
                            <!-- Day -->
                            <span
                                class="text-[9px] leading-none font-bold tracking-widest uppercase"
                                :class="
                                    selectedDate === day.full_date
                                        ? 'opacity-70'
                                        : 'opacity-60'
                                "
                            >
                                {{ day.day_name }}
                            </span>

                            <!-- Spacer kecil -->
                            <div class="h-1"></div>

                            <!-- Tanggal -->
                            <span class="text-[26px] leading-none font-black">
                                {{ day.date_num }}
                            </span>

                            <!-- Dot Indicator -->
                            <div
                                v-if="
                                    fullyBookedDates.includes(day.full_date) &&
                                    selectedDate !== day.full_date
                                "
                                class="absolute top-2.5 right-2.5 h-1.5 w-1.5 rounded-full bg-red-400"
                            ></div>
                        </button>
                    </div>
                </div>
            </header>

            <!-- 2. ALERT BANNER (Muncul jika user dibanned) -->
            <div v-if="isUserBanned" class="relative z-20 mx-5 -mt-6 mb-2">
                <div
                    class="flex items-start gap-3 rounded-2xl bg-[#EF4444] p-4 text-white shadow-lg ring-4 ring-white"
                >
                    <div class="shrink-0 rounded-full bg-white/20 p-1.5">
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
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold tracking-wide uppercase">
                            AKUN DIBEKUKAN
                        </h4>
                        <p
                            class="mt-1 text-[11px] leading-relaxed text-white/90"
                        >
                            {{ banMessage }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- 3. TIMELINE LIST -->
            <main class="flex-1 overflow-y-auto px-5 pt-6 pb-32">
                <div class="mb-6 flex items-end justify-between px-1">
                    <h3 class="text-xl font-black text-[#155E75]">
                        Select Time
                    </h3>
                    <span class="mb-1 text-xs font-medium text-slate-500">{{
                        formatDateDisplay(selectedDate)
                    }}</span>
                </div>

                <div class="space-y-4">
                    <div
                        v-for="hour in allSlots"
                        :key="hour"
                        class="flex items-center gap-4"
                    >
                        <!-- JAM (Vertikal, tanpa strip) -->
                        <div
                            class="flex w-14 shrink-0 flex-col items-center justify-center gap-1"
                        >
                            <span
                                class="text-sm leading-none font-black text-[#155E75]"
                                >{{ String(hour).padStart(2, '0') }}.00</span
                            >
                            <span
                                class="text-sm leading-none font-bold text-slate-400"
                                >{{
                                    String(hour + 1).padStart(2, '0')
                                }}.00</span
                            >
                        </div>

                        <!-- CARD -->
                        <div
                            @click="openBooking(hour)"
                            class="relative flex flex-1 items-center justify-between rounded-[1.5rem] px-5 py-5 transition-all duration-200"
                            :class="{
                                // AVAILABLE (BIRU)
                                'cursor-pointer bg-[#D1E9F6] hover:shadow-md active:scale-[0.98]':
                                    getSlotData(hour).type === 'available',
                                // BOOKED (HIJAU)
                                'cursor-not-allowed bg-[#DCFCE7]':
                                    getSlotData(hour).type === 'booked',
                                // EXPIRED (MERAH)
                                'cursor-not-allowed bg-[#FEE2E2] opacity-80':
                                    getSlotData(hour).type === 'past',
                                // MAINTENANCE (ABU)
                                'cursor-not-allowed border border-slate-100 bg-[#F1F5F9] text-slate-400':
                                    getSlotData(hour).type === 'maintenance',
                            }"
                        >
                            <!-- Text Info -->
                            <div>
                                <h4
                                    class="text-sm font-black tracking-wide uppercase"
                                    :class="{
                                        'text-[#155E75]':
                                            getSlotData(hour).type ===
                                            'available',
                                        'text-[#166534]':
                                            getSlotData(hour).type === 'booked',
                                        'text-[#991B1B]':
                                            getSlotData(hour).type === 'past',
                                        'text-slate-400':
                                            getSlotData(hour).type ===
                                            'maintenance',
                                    }"
                                >
                                    {{ getSlotData(hour).label }}
                                </h4>
                                <p
                                    class="mt-1 text-[10px] font-bold"
                                    :class="{
                                        'text-[#155E75]/70':
                                            getSlotData(hour).type ===
                                            'available',
                                        'text-[#166534]/70':
                                            getSlotData(hour).type === 'booked',
                                        'text-[#991B1B]/60':
                                            getSlotData(hour).type === 'past',
                                        'text-slate-400':
                                            getSlotData(hour).type ===
                                            'maintenance',
                                    }"
                                >
                                    {{ getSlotData(hour).sub }}
                                </p>
                            </div>

                            <!-- Icon -->
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full shadow-sm transition-colors"
                                :class="{
                                    'bg-white text-[#155E75]':
                                        getSlotData(hour).type === 'available',
                                    'bg-white/60 text-[#166534]':
                                        getSlotData(hour).type === 'booked',
                                    'bg-white/50 text-[#991B1B]':
                                        getSlotData(hour).type === 'past',
                                    'bg-slate-200 text-slate-400':
                                        getSlotData(hour).type ===
                                        'maintenance',
                                }"
                            >
                                <svg
                                    v-if="
                                        getSlotData(hour).type === 'available'
                                    "
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="3"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 4v16m8-8H4"
                                    ></path>
                                </svg>
                                <svg
                                    v-else-if="
                                        getSlotData(hour).type === 'booked'
                                    "
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2.5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                                <svg
                                    v-else-if="
                                        getSlotData(hour).type === 'past'
                                    "
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="2.5"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                                <span
                                    v-else
                                    class="text-xl font-bold select-none"
                                    >-</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <!-- 3. BOTTOM NAVIGATION (Floating) -->
            <div
                class="pointer-events-none fixed right-0 bottom-8 left-0 z-30 flex justify-center"
            >
                <nav
                    class="pointer-events-auto flex w-[85%] max-w-[340px] items-center justify-around rounded-full bg-white px-2 py-2 shadow-[0_8px_30px_rgb(0,0,0,0.12)]"
                >
                    <!-- HOME (Active) -->
                    <Link
                        href="/booking"
                        class="group flex flex-col items-center gap-1"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-[#155E75] shadow-lg shadow-[#155E75]/40 transition-transform group-active:scale-95"
                        >
                            <svg
                                class="h-5 w-5 text-[#BEF264]"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                                />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-[#155E75]">
                            Home
                        </span>
                    </Link>

                    <Link
                        href="/my-tickets"
                        class="flex flex-col items-center gap-[2px]"
                    >
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-50"
                        >
                            <svg
                                class="h-5 w-5 text-[#155E75]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"
                                />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-[#155E75]">
                            Tiket
                        </span>
                    </Link>

                    <!-- PROFIL -->
                    <Link
                        href="/info"
                        class="flex flex-col items-center gap-[2px]"
                    >
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-50"
                        >
                            <svg
                                class="h-5 w-5 text-[#155E75]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                />
                            </svg>
                        </div>
                        <span class="text-[10px] font-bold text-[#155E75]">
                            Profil
                        </span>
                    </Link>
                </nav>
            </div>

            <!-- Modal Booking -->
            <transition name="modal">
                <div
                    v-if="showModal"
                    class="fixed inset-0 z-50 flex items-end justify-center sm:items-center"
                >
                    <!-- Backdrop Gelap -->
                    <div
                        @click="showModal = false"
                        class="absolute inset-0 bg-[#155E75]/80 backdrop-blur-sm"
                    ></div>

                    <!-- Card Modal -->
                    <div
                        class="animate-slide-up relative w-full max-w-[480px] rounded-t-[2.5rem] bg-white p-8 shadow-2xl sm:rounded-[2.5rem]"
                    >
                        <!-- Handle (Garis kecil di atas) -->
                        <div
                            class="mx-auto mb-6 h-1.5 w-12 rounded-full bg-slate-200"
                        ></div>

                        <!-- Judul -->
                        <div class="mb-6">
                            <h2 class="text-2xl font-black text-[#155E75]">
                                Confirmation
                            </h2>
                            <p class="text-sm text-slate-500">
                                Booking slot:
                                <span class="font-bold text-[#155E75]">{{
                                    selectedSlot?.label
                                }}</span>
                            </p>
                        </div>

                        <!-- Area Pesan Error (PENTING: Agar user tau kalau gagal) -->
                        <div
                            v-if="Object.keys(form.errors).length > 0"
                            class="mb-5 rounded-xl border border-red-100 bg-red-50 p-4"
                        >
                            <div class="flex items-start gap-3">
                                <svg
                                    class="mt-0.5 h-5 w-5 shrink-0 text-red-500"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                                <div>
                                    <h4
                                        class="text-xs font-bold text-red-600 uppercase"
                                    >
                                        Booking Failed
                                    </h4>
                                    <ul
                                        class="mt-1 list-inside list-disc text-xs text-red-500"
                                    >
                                        <li
                                            v-for="(error, key) in form.errors"
                                            :key="key"
                                        >
                                            {{ error }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Form Input -->
                        <form @submit.prevent="submitBooking" class="space-y-5">
                            <!-- Input Nama -->
                            <div>
                                <label
                                    class="mb-2 ml-1 block text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                                >
                                    Player Name / Group
                                </label>
                                <input
                                    v-model="form.player_names"
                                    type="text"
                                    class="w-full rounded-2xl border-none bg-[#F1F5F9] px-5 py-4 font-bold text-[#155E75] placeholder-slate-400 transition-all focus:ring-2 focus:ring-[#BEF264]"
                                    placeholder="e.g. John & Family"
                                    autofocus
                                />
                            </div>

                            <!-- Checkbox Terms -->
                            <div class="flex items-start gap-3 px-1">
                                <div class="relative flex items-center">
                                    <input
                                        type="checkbox"
                                        id="terms"
                                        v-model="form.agree_terms"
                                        class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border-2 border-slate-300 transition-all checked:border-[#155E75] checked:bg-[#155E75]"
                                    />
                                    <svg
                                        class="pointer-events-none absolute top-1/2 left-1/2 h-3.5 w-3.5 -translate-x-1/2 -translate-y-1/2 text-white opacity-0 peer-checked:opacity-100"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="3"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 13l4 4L19 7"
                                        ></path>
                                    </svg>
                                </div>
                                <label
                                    for="terms"
                                    class="cursor-pointer text-xs leading-relaxed text-slate-500 select-none"
                                >
                                    I agree to the court rules. Cancellations
                                    must be made 2 hours in advance.
                                </label>
                            </div>

                            <!-- Tombol Submit -->
                            <button
                                :disabled="form.processing || !form.agree_terms"
                                class="mt-2 flex w-full items-center justify-center gap-2 rounded-2xl bg-[#155E75] py-4 text-lg font-bold text-white shadow-xl shadow-[#155E75]/30 transition-all hover:bg-[#104a60] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                <svg
                                    v-if="form.processing"
                                    class="h-5 w-5 animate-spin text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                <span>{{
                                    form.processing
                                        ? 'Processing...'
                                        : 'Confirm Booking'
                                }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
@keyframes slide-up {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}
.animate-slide-up {
    animation: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
