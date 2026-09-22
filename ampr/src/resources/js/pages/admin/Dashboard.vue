<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    days: Array,
    bookings: Array,
    stats: Object,
    units: Array,
    checkInCount: Number,
});

const showBookingModal = ref(false);

const formBooking = useForm({
    unit_id: '',
    date: '',
    hour: '',
    displayDate: '',
    displayTime: '',
});

// --- HELPER FUNCTIONS ---
const isPast = (dateStr, hour) => {
    const now = new Date();
    const slotDate = new Date(
        `${dateStr}T${String(hour).padStart(2, '0')}:00:00`,
    );
    return slotDate < now;
};

const getBooking = (date, hour) => {
    return props.bookings.find((b) => b.date === date && b.hour === hour);
};

const getSlotStyle = (booking) => {
    if (!booking) return '';
    switch (booking.status) {
        case 'checked_in':
            return 'bg-emerald-100 border-emerald-200 text-emerald-700';
        case 'no_show':
            return 'bg-rose-100 border-rose-200 text-rose-700 opacity-70';
        case 'maintenance':
            return 'bg-slate-100 border-slate-200 text-slate-500 cursor-not-allowed';
        case 'booked':
        default:
            return 'bg-blue-100 border-blue-200 text-blue-700 hover:bg-blue-200 relative group';
    }
};

// --- ACTION HANDLERS ---
const cancelBooking = (bookingId, unitName) => {
    if (
        confirm(
            `Batalkan booking unit ${unitName}? Kuota unit akan dikembalikan.`,
        )
    ) {
        router.post(
            route('admin.booking.cancel', bookingId),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    /* Toast automatic from layout usually */
                },
            },
        );
    }
};

const openBookingModal = (date, hour, formattedDate) => {
    formBooking.unit_id = '';
    formBooking.date = date;
    formBooking.hour = hour;
    formBooking.displayDate = formattedDate;
    formBooking.displayTime = `${String(hour).padStart(2, '0')}:00 - ${String(hour + 1).padStart(2, '0')}:00`;
    formBooking.clearErrors();
    showBookingModal.value = true;
};

const submitBooking = () => {
    formBooking.post(route('admin.dashboard.store'), {
        onSuccess: () => {
            showBookingModal.value = false;
            formBooking.reset();
        },
    });
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <template #header>Dashboard</template>

        <!-- 1. STATS CARDS -->
        <div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-4">
            <!-- TOTAL UNIT -->
            <div
                class="relative overflow-hidden rounded-[2rem] bg-[#1A5F7A] p-6 text-white shadow-xl shadow-[#1A5F7A]/20"
            >
                <p
                    class="mb-1 text-[10px] font-bold tracking-widest uppercase opacity-70"
                >
                    Total Unit
                </p>
                <h3 class="text-4xl font-black">{{ stats.total_units }}</h3>
                <!-- Decor -->
                <svg
                    class="absolute -right-4 -bottom-4 h-24 w-24 opacity-10"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                >
                    <path
                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
                    />
                </svg>
            </div>

            <!-- BOOKING HARI INI -->
            <div
                class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm"
            >
                <p
                    class="mb-1 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                >
                    Booking Hari Ini
                </p>
                <h3 class="text-4xl font-black text-[#1A5F7A]">
                    {{ stats.today_bookings }}
                </h3>
            </div>

            <!-- SUDAH CHECK-IN (Highlight Lime Green) -->
            <div
                class="relative overflow-hidden rounded-[2rem] border-2 border-[#BEF264] bg-white p-6 shadow-md"
            >
                <div class="flex items-start justify-between">
                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-widest text-[#1A5F7A] uppercase"
                        >
                            Sudah Check-In
                        </p>
                        <h3 class="text-4xl font-black text-[#1A5F7A]">
                            {{ checkInCount }}
                        </h3>
                    </div>
                    <div
                        class="rounded-full bg-[#BEF264] p-2 text-[#1A5F7A] shadow-sm"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- UNIT BANNED -->
            <div
                class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm"
            >
                <p
                    class="mb-1 text-[10px] font-bold tracking-widest text-red-400 uppercase"
                >
                    Unit Banned
                </p>
                <h3 class="text-4xl font-black text-red-500">
                    {{ stats.banned_units }}
                </h3>
            </div>
        </div>

        <!-- 2. JADWAL LAPANGAN (SCHEDULE) -->
        <div
            class="flex flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-lg"
        >
            <!-- Legend -->
            <div
                class="z-10 flex flex-col justify-between gap-4 border-b border-slate-100 bg-white p-6 md:flex-row md:items-center"
            >
                <h3 class="text-xl font-black text-[#1A5F7A]">
                    Monthly Schedule
                </h3>
                <div
                    class="flex flex-wrap gap-3 text-[10px] font-bold tracking-wider uppercase"
                >
                    <div class="flex items-center gap-1">
                        <span
                            class="h-2 w-2 rounded-full bg-emerald-500"
                        ></span>
                        Check-in
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        Booked
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                        Maintenance
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-slate-200"></span>
                        Expired
                    </div>
                </div>
            </div>

            <!-- Scrollable Table -->
            <div
                class="custom-scrollbar relative h-[65vh] w-full overflow-auto bg-slate-50/50"
            >
                <div class="min-w-max">
                    <!-- Header Tanggal -->
                    <div class="sticky top-0 z-30 flex shadow-sm">
                        <div
                            class="sticky top-0 left-0 z-40 flex w-24 flex-none items-center justify-center border-r border-b border-slate-200 bg-[#F8FAFC] p-3"
                        >
                            <span
                                class="text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                >JAM</span
                            >
                        </div>
                        <div
                            v-for="day in days"
                            :key="day.date"
                            class="w-32 flex-none border-r border-b border-slate-100 bg-[#F8FAFC] p-3 text-center"
                            :class="day.is_today ? 'bg-blue-50/80' : ''"
                        >
                            <div
                                class="mb-1 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >
                                {{ day.day_name.substring(0, 3) }}
                            </div>
                            <div
                                class="text-sm font-black text-[#1A5F7A]"
                                :class="day.is_today ? 'text-[#00ACC1]' : ''"
                            >
                                {{ day.formatted }}
                            </div>
                        </div>
                    </div>

                    <!-- Body Jadwal -->
                    <div class="divide-y divide-slate-100">
                        <!-- Loop Jam (06:00 - 22:00) -->
                        <div
                            v-for="hour in 17"
                            :key="hour"
                            class="flex transition-colors hover:bg-white"
                        >
                            <!-- Kolom Jam (Sticky Kiri) -->
                            <div
                                class="sticky left-0 z-20 flex w-24 flex-none items-center justify-center border-r border-slate-200 bg-white p-2 text-[10px] font-bold text-slate-500 shadow-[2px_0_5px_rgba(0,0,0,0.02)]"
                            >
                                {{ String(hour + 5).padStart(2, '0') }}:00
                            </div>

                            <!-- Kolom Slot per Hari -->
                            <div
                                v-for="day in days"
                                :key="day.date + hour"
                                class="h-20 w-32 flex-none border-r border-slate-100 bg-white/50 p-1.5"
                            >
                                <!-- CASE 1: ADA BOOKING -->
                                <div
                                    v-if="getBooking(day.date, hour + 5)"
                                    class="relative flex h-full w-full flex-col justify-center rounded-xl border p-2 text-[10px] font-bold shadow-sm transition-all hover:z-10 hover:scale-[1.03] hover:shadow-md"
                                    :class="
                                        getSlotStyle(
                                            getBooking(day.date, hour + 5),
                                        )
                                    "
                                >
                                    <!-- A. Maintenance -->
                                    <div
                                        v-if="
                                            getBooking(day.date, hour + 5)
                                                .status === 'maintenance'
                                        "
                                    >
                                        <div
                                            class="mb-1 flex items-center gap-1 text-slate-400"
                                        >
                                            <svg
                                                class="h-3 w-3"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                                ></path>
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                ></path>
                                            </svg>
                                            <span>MTC</span>
                                        </div>
                                        <p
                                            class="line-clamp-2 text-[9px] leading-tight font-normal italic opacity-80"
                                        >
                                            {{
                                                getBooking(day.date, hour + 5)
                                                    .description
                                            }}
                                        </p>
                                    </div>

                                    <!-- B. Booking User -->
                                    <div v-else>
                                        <div
                                            class="mb-0.5 flex items-center justify-between"
                                        >
                                            <span
                                                class="text-[8px] tracking-wider uppercase opacity-70"
                                                >UNIT</span
                                            >
                                            <span
                                                v-if="
                                                    getBooking(
                                                        day.date,
                                                        hour + 5,
                                                    ).status === 'no_show'
                                                "
                                                class="rounded bg-white/50 px-1 text-[8px]"
                                                >NOSHOW</span
                                            >
                                        </div>
                                        <p class="truncate text-xs font-black">
                                            {{
                                                getBooking(day.date, hour + 5)
                                                    .unit
                                            }}
                                        </p>

                                        <!-- TOMBOL CANCEL (Muncul saat Hover) -->
                                        <button
                                            v-if="
                                                getBooking(day.date, hour + 5)
                                                    .status === 'booked'
                                            "
                                            @click.stop="
                                                cancelBooking(
                                                    getBooking(
                                                        day.date,
                                                        hour + 5,
                                                    ).id,
                                                    getBooking(
                                                        day.date,
                                                        hour + 5,
                                                    ).unit,
                                                )
                                            "
                                            class="absolute -top-2 -right-2 z-20 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white opacity-0 shadow-md transition-all group-hover:opacity-100 hover:scale-110 hover:bg-red-600"
                                            title="Batalkan Booking"
                                        >
                                            <svg
                                                class="h-3 w-3"
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
                                </div>

                                <!-- CASE 2: EXPIRED -->
                                <div
                                    v-else-if="isPast(day.date, hour + 5)"
                                    class="flex h-full w-full cursor-not-allowed flex-col items-center justify-center rounded-xl border border-transparent bg-slate-100/50 opacity-40"
                                >
                                    <span class="text-lg grayscale">🔒</span>
                                </div>

                                <!-- CASE 3: EMPTY (AVAILABLE) -->
                                <div
                                    v-else
                                    @click="
                                        openBookingModal(
                                            day.date,
                                            hour + 5,
                                            day.formatted,
                                        )
                                    "
                                    class="group/slot flex h-full w-full cursor-pointer flex-col items-center justify-center rounded-xl border border-transparent transition-all hover:border-dashed hover:border-[#1A5F7A] hover:bg-blue-50"
                                >
                                    <span
                                        class="mb-1 text-xl font-bold text-[#1A5F7A] opacity-0 group-hover/slot:opacity-100"
                                        >+</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL MANUAL BOOKING -->
        <div
            v-if="showBookingModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-[#1A5F7A]/20 p-4 backdrop-blur-sm transition-opacity"
        >
            <div
                class="animate-slide-up w-full max-w-sm overflow-hidden rounded-[2.5rem] bg-white p-8 shadow-2xl ring-1 ring-slate-100"
            >
                <div class="mb-6 text-center">
                    <h3 class="text-xl font-black text-[#1A5F7A]">
                        Manual Booking
                    </h3>
                    <p class="mt-1 text-sm font-bold text-slate-500">
                        {{ formBooking.displayDate }}
                        <span class="mx-1 text-slate-300">•</span>
                        {{ formBooking.displayTime }}
                    </p>
                </div>

                <form @submit.prevent="submitBooking" class="space-y-5">
                    <div>
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Pilih Unit</label
                        >
                        <div class="relative">
                            <select
                                v-model="formBooking.unit_id"
                                class="w-full appearance-none rounded-2xl border-slate-200 bg-slate-50 px-5 py-3.5 font-bold text-[#1A5F7A] transition-all focus:border-[#1A5F7A] focus:bg-white focus:ring-2 focus:ring-[#1A5F7A]/20"
                                required
                            >
                                <option value="" disabled>
                                    -- Pilih Unit --
                                </option>
                                <option
                                    v-for="unit in units"
                                    :key="unit.id"
                                    :value="unit.id"
                                >
                                    {{ unit.unit_number }} -
                                    {{ unit.owner_name || 'No Name' }}
                                </option>
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-400"
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
                                        d="M19 9l-7 7-7-7"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- ERROR MESSAGE -->
                    <div
                        v-if="formBooking.errors.modal"
                        class="rounded-xl border border-red-100 bg-red-50 p-3 text-center text-xs font-bold text-red-600"
                    >
                        ⚠️ {{ formBooking.errors.modal }}
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button
                            type="button"
                            @click="showBookingModal = false"
                            class="flex-1 rounded-2xl py-3.5 font-bold text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="formBooking.processing"
                            class="flex-1 rounded-2xl bg-[#1A5F7A] py-3.5 font-bold text-white shadow-lg shadow-[#1A5F7A]/30 transition-all hover:bg-[#164e63] active:scale-95 disabled:opacity-70"
                        >
                            {{
                                formBooking.processing
                                    ? 'Menyimpan...'
                                    : 'Booking'
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f8fafc;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
    border: 2px solid #f8fafc;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
@keyframes slide-up {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
.animate-slide-up {
    animation: slide-up 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
