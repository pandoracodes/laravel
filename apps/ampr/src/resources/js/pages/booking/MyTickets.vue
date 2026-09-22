<script setup>
import NoFoundIlustration from '@/assets/no-found.png';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

// PROPS
const props = defineProps({
    activeTickets: Object,
    historyTickets: Object,
    userUnit: String,
});

// DATA HANDLER
const activeList = props.activeTickets?.data || [];
const historyList = props.historyTickets?.data || [];
const searchQuery = ref('');

const filteredHistory = computed(() => {
    // Jika kolom pencarian kosong, tampilkan semua history
    if (!searchQuery.value) return historyList;

    const query = searchQuery.value.toLowerCase();

    return historyList.filter((ticket) => {
        // Cocokkan dengan format tanggal dan status yang tampil di layar
        const dateText = formatDate(ticket.start_time).toLowerCase();
        const statusText = getStatusBadge(ticket.status).text.toLowerCase();

        return dateText.includes(query) || statusText.includes(query);
    });
});

const activeTab = ref('active');
const showQrModal = ref(false);
const currentQrCode = ref('');
const currentBookingCode = ref('');

// MODAL OPENER
const openQrModal = (code, base64Image) => {
    currentBookingCode.value = code;
    currentQrCode.value = base64Image;
    showQrModal.value = true;
};

// HELPERS
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-GB', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const formatTime = (start, end) => {
    const s = new Date(start).toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit',
    });
    const e = new Date(end).toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit',
    });
    return `${s} - ${e}`;
};

const getStatusBadge = (status) => {
    switch (status) {
        case 'booked':
            return {
                text: 'Confirmed',
                bg: 'bg-[#E0F2FE]',
                textCol: 'text-[#0EA5E9]',
            };
        case 'checked_in':
            return {
                text: 'Completed',
                bg: 'bg-[#DCFCE7]',
                textCol: 'text-[#166534]',
            };
        case 'no_show':
            return {
                text: 'No Show',
                bg: 'bg-[#FEE2E2]',
                textCol: 'text-[#991B1B]',
            };
        case 'cancelled':
            return {
                text: 'Cancelled',
                bg: 'bg-[#F1F5F9]',
                textCol: 'text-[#64748B]',
            };
        default:
            return {
                text: status,
                bg: 'bg-gray-100',
                textCol: 'text-gray-600',
            };
    }
};
</script>

<template>
    <Head title="My Tickets" />

    <div
        class="flex min-h-screen justify-center bg-[#F3F6F9] font-sans text-[#1A5F7A] antialiased"
    >
        <div
            class="relative flex min-h-screen w-full max-w-[480px] flex-col overflow-hidden bg-[#F8FAFC] shadow-2xl"
        >
            <!-- 1. HEADER (Curved & Clean like Ref 1) -->
            <header
                class="relative z-10 overflow-hidden rounded-b-[2.5rem] bg-[#1A5F7A] px-6 pt-12 pb-8 shadow-xl"
            >
                <!-- <svg
                    class="pointer-events-none absolute -top-6 -right-6 h-48 w-48 rotate-12 text-[#BEF264] opacity-[0.08]"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                > -->
                <!-- Dasar Bola
                    <circle cx="12" cy="12" r="10" /> -->

                <!-- Garis Tenis (Seam) -->
                <!-- Kurva Kiri -->
                <!-- <path
                        d="M2 12 C 7 12 11 8 11 2"
                        fill="none"
                        stroke="#155E75"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        opacity="0.6"
                    /> -->
                <!-- Kurva Kanan -->
                <!-- <path
                        d="M22 12 C 17 12 13 16 13 22"
                        fill="none"
                        stroke="#155E75"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        opacity="0.6"
                    /> -->
                <!-- </svg> -->

                <div class="relative z-10">
                    <!-- JUDUL & UNIT BADGE -->
                    <div class="mb-8 flex items-start justify-between">
                        <!-- Teks Kiri -->
                        <div>
                            <p
                                class="mb-1 text-[10px] font-bold tracking-[0.2em] text-[#BEF264] uppercase drop-shadow-sm"
                            >
                                Mediterania Court
                            </p>
                            <h1
                                class="text-3xl leading-none font-black tracking-tight text-white"
                            >
                                My Tickets
                            </h1>
                        </div>

                        <!-- Unit Badge (Kanan) -->
                        <div
                            class="min-w-[90px] rounded-2xl border border-white/20 bg-white/10 px-4 py-2 text-center shadow-lg backdrop-blur-md"
                        >
                            <span
                                class="block text-[8px] font-bold tracking-wider text-white/70 uppercase"
                                >UNIT</span
                            >
                            <span
                                class="mt-0.5 block text-lg leading-none font-black text-white"
                                >{{ userUnit }}</span
                            >
                        </div>
                    </div>

                    <!-- TAB SWITCHER (Kode Anda) -->
                    <div
                        class="relative flex rounded-2xl bg-[#12465a]/60 p-1 backdrop-blur-sm"
                    >
                        <button
                            @click="activeTab = 'active'"
                            class="relative z-10 flex-1 rounded-xl py-2.5 text-xs font-bold transition-all duration-300"
                            :class="
                                activeTab === 'active'
                                    ? 'bg-white text-[#1A5F7A] shadow-md'
                                    : 'text-slate-300 hover:text-white'
                            "
                        >
                            Upcoming
                            <span
                                v-if="activeList.length"
                                class="ml-1 rounded-full px-1.5 py-0.5 text-[9px]"
                                :class="
                                    activeTab === 'active'
                                        ? 'bg-[#1A5F7A] text-white'
                                        : 'bg-[#BEF264] text-[#1A5F7A]'
                                "
                                >{{ activeList.length }}</span
                            >
                        </button>
                        <button
                            @click="activeTab = 'history'"
                            class="relative z-10 flex-1 rounded-xl py-2.5 text-xs font-bold transition-all duration-300"
                            :class="
                                activeTab === 'history'
                                    ? 'bg-white text-[#1A5F7A] shadow-md'
                                    : 'text-slate-300 hover:text-white'
                            "
                        >
                            History
                        </button>
                    </div>
                </div>
            </header>

            <!-- 2. CONTENT LIST -->
            <main class="no-scrollbar flex-1 overflow-y-auto px-5 pt-6 pb-32">
                <!-- TAB: ACTIVE -->
                <div v-if="activeTab === 'active'" class="space-y-5">
                    <!-- Empty State -->
                    <div
                        v-if="activeList.length === 0"
                        class="flex flex-col items-center justify-center py-16 opacity-60"
                    >
                        <img
                            :src="NoFoundIlustration"
                            class="mb-4 h-30 w-30"
                            alt="ilustration"
                        />
                        <h3 class="text-slate-1000 text-lg font-black">
                            No Booking Found
                        </h3>
                        <Link
                            href="/booking"
                            class="mt-2 text-sm font-bold text-[#1A5F7A] hover:underline"
                            >Let's book a court!</Link
                        >
                    </div>

                    <!-- CARD DESIGN (Inspired by Ref 2) -->
                    <div
                        v-for="ticket in activeList"
                        :key="ticket.id"
                        class="group relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-5 shadow-[0_4px_20px_rgb(0,0,0,0.04)]"
                    >
                        <!-- Top Icon & Status -->
                        <div class="mb-6 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <!-- Icon Container -->
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-[#EAEFF5] text-[#1A5F7A]"
                                >
                                    <svg
                                        class="h-5 w-5"
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
                                        class="text-sm font-black text-[#1A5F7A]"
                                    >
                                        Tennis Court
                                    </h3>
                                    <p
                                        class="text-[10px] font-bold tracking-wide text-slate-400"
                                    >
                                        BOOKING CONFIRMED
                                    </p>
                                </div>
                            </div>
                            <!-- Menu Dots -->
                            <button class="text-slate-300">
                                <svg
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"
                                    />
                                </svg>
                            </button>
                        </div>

                        <!-- Details Grid -->
                        <div class="mb-6 grid grid-cols-2 gap-4">
                            <div>
                                <p
                                    class="mb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase"
                                >
                                    Date
                                </p>
                                <p class="text-sm font-black text-[#1A5F7A]">
                                    {{ formatDate(ticket.start_time) }}
                                </p>
                            </div>
                            <div>
                                <p
                                    class="mb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase"
                                >
                                    Time
                                </p>
                                <p class="text-sm font-black text-[#1A5F7A]">
                                    {{
                                        formatTime(
                                            ticket.start_time,
                                            ticket.end_time,
                                        )
                                    }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons (Ref 2 Style) -->
                        <div class="flex items-center gap-3">
                            <!-- Main Action -->
                            <button
                                @click="
                                    openQrModal(
                                        ticket.booking_code,
                                        ticket.qr_code_image,
                                    )
                                "
                                class="flex h-12 flex-1 items-center justify-center gap-2 rounded-xl bg-[#E0F2FE] text-xs font-bold text-[#0284C7] transition-all duration-300 hover:bg-[#155E75] hover:text-white"
                            >
                                Show QR Code
                            </button>
                            <!-- Secondary Action (Arrow Box) -->
                            <a
                                :href="`/ticket/${ticket.booking_code}/pdf`"
                                target="_blank"
                                class="flex h-12 w-12 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 transition-all hover:border-[#1A5F7A] hover:text-[#1A5F7A]"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <!-- Icon Download (Panah ke Bawah + Garis Tray) -->
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                    />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- TAB: HISTORY -->
                <div v-if="activeTab === 'history'" class="space-y-4">
                    <!-- === TAMBAHAN: KOTAK PENCARIAN (SEARCH BAR) === -->

                    <div class="group relative mb-6">
                        <!-- Icon Kaca Pembesar -->
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-5 transition-colors group-focus-within:text-[#1A5F7A]"
                        >
                            <svg
                                class="h-5 w-5 text-slate-400 transition-colors group-focus-within:text-[#1A5F7A]"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                ></path>
                            </svg>
                        </div>

                        <!-- Input Field -->
                        <input
                            v-model="searchQuery"
                            type="text"
                            class="w-full rounded-[1.5rem] border-0 bg-white py-4 pr-12 pl-12 text-sm font-bold text-[#1A5F7A] placeholder-slate-400 shadow-[0_4px_20px_rgb(0,0,0,0.04)] ring-1 ring-slate-100 transition-all outline-none focus:ring-2 focus:ring-[#1A5F7A]"
                            placeholder="Cari tanggal atau status..."
                        />

                        <!-- Tombol 'X' (Hanya muncul kalau ada teks yang diketik) -->
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute inset-y-0 right-0 flex items-center pr-5 text-slate-300 transition-colors hover:text-red-500"
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
                                    d="M6 18L18 6M6 6l12 12"
                                ></path>
                            </svg>
                        </button>
                    </div>

                    <!-- === BATAS KOTAK PENCARIAN === -->

                    <!-- Empty State (Ubah historyList jadi filteredHistory) -->
                    <div
                        v-if="filteredHistory.length === 0"
                        class="flex flex-col items-center justify-center py-16 opacity-60"
                    >
                        <img
                            :src="NoFoundIlustration"
                            class="mb-4 h-30 w-30"
                            alt="ilustration"
                        />
                        <!-- Teks dinamis: jika lagi nyari tapi ga ketemu, teksnya beda -->
                        <h3
                            class="text-slate-1000 text-center text-lg font-black"
                        >
                            {{
                                searchQuery
                                    ? 'No Results Found'
                                    : 'No History Available'
                            }}
                        </h3>
                        <Link
                            v-if="!searchQuery"
                            href="/booking"
                            class="mt-2 text-sm font-bold text-[#1A5F7A] hover:underline"
                            >Let's book a court!</Link
                        >
                    </div>

                    <!-- List Tiket (Ubah historyList jadi filteredHistory) -->
                    <div
                        v-for="ticket in filteredHistory"
                        :key="ticket.id"
                        class="group flex items-center justify-between rounded-[1.8rem] border border-transparent bg-white p-5 transition-all hover:border-slate-100 hover:shadow-md"
                    >
                        <div class="flex items-center gap-4">
                            <!-- Icon Status -->
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full"
                                :class="
                                    getStatusBadge(ticket.status).bg +
                                    ' ' +
                                    getStatusBadge(ticket.status).textCol
                                "
                            >
                                <svg
                                    v-if="ticket.status === 'checked_in'"
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M5 13l4 4L19 7"
                                    ></path>
                                </svg>
                                <svg
                                    v-else
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                            </div>

                            <div>
                                <h4
                                    class="mb-0.5 text-sm font-black text-slate-700"
                                >
                                    {{ formatDate(ticket.start_time) }}
                                </h4>
                                <p class="text-[10px] font-bold text-slate-400">
                                    {{
                                        formatTime(
                                            ticket.start_time,
                                            ticket.end_time,
                                        )
                                    }}
                                    •
                                    <span
                                        :class="
                                            getStatusBadge(ticket.status)
                                                .textCol
                                        "
                                    >
                                        {{ getStatusBadge(ticket.status).text }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Arrow Mini -->
                        <a
                            :href="`/ticket/${ticket.booking_code}/pdf`"
                            target="_blank"
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-50 text-slate-400 transition-all hover:bg-[#1A5F7A] hover:text-white"
                        >
                            <svg
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5l7 7-7 7"
                                ></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </main>

            <!-- 3. BOTTOM NAV -->
            <div
                class="pointer-events-none fixed right-0 bottom-8 left-0 z-30 flex justify-center"
            >
                <nav
                    class="pointer-events-auto flex w-[85%] max-w-[340px] items-center justify-around rounded-full bg-white px-2 py-2 shadow-[0_8px_30px_rgb(0,0,0,0.12)]"
                >
                    <Link
                        href="/booking"
                        class="group flex flex-1 flex-col items-center gap-1"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full transition-colors"
                        >
                            <svg
                                class="h-6 w-6 text-[#1A5F7A] transition-colors"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                ></path>
                            </svg>
                        </div>
                        <span class="-mt-1 text-[9px] font-bold text-[#1A5F7A]"
                            >Home</span
                        >
                    </Link>
                    <Link
                        href="/my-tickets"
                        class="group flex flex-1 flex-col items-center gap-1"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-[#1A5F7A] shadow-lg shadow-[#1A5F7A]/30 transition-transform group-active:scale-95"
                        >
                            <svg
                                class="h-5 w-5 text-[#BEF264]"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 100 4v2a2 2 0 01-2 2H4a2 2 0 01-2-2v-2a2 2 0 100-4V6z"
                                ></path>
                            </svg>
                        </div>
                        <span class="-mt-1 text-[9px] font-bold text-[#1A5F7A]"
                            >Tickets</span
                        >
                    </Link>
                    <Link
                        href="/info"
                        class="group flex flex-1 flex-col items-center gap-1"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full transition-colors"
                        >
                            <svg
                                class="h-6 w-6 text-[#1A5F7A] transition-colors"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                ></path>
                            </svg>
                        </div>
                        <span class="-mt-1 text-[9px] font-bold text-[#1A5F7A]"
                            >Profile</span
                        >
                    </Link>
                </nav>
            </div>

            <!-- MODAL QR -->
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
                            Show this QR code to the gate keeper
                        </p>
                        <div
                            class="relative mb-6 inline-block overflow-hidden rounded-[1.5rem] border-2 border-[#1A5F7A]/10 bg-white p-4 shadow-inner"
                        >
                            <div
                                class="animate-scan absolute top-0 left-0 h-1 w-full bg-[#BEF264]/80 shadow-[0_0_15px_rgba(190,242,100,0.8)]"
                            ></div>
                            <img
                                :src="currentQrCode"
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
                                {{ currentBookingCode }}
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
</style>
