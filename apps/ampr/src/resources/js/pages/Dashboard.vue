<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    days: Array,
    bookings: Array,
    stats: Object,
});

const getBooking = (date, hour) => {
    return props.bookings.find((b) => b.date === date && b.hour === hour);
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div class="grid grid-cols-1 gap-8 xl:grid-cols-12">
            <!-- KOLOM KIRI (7/12) -->
            <div class="space-y-8 xl:col-span-7">
                <!-- HERO -->
                <div
                    class="group relative flex h-[320px] flex-col justify-center overflow-hidden rounded-[2.5rem] bg-[#1C1C28] p-8 shadow-2xl"
                >
                    <div class="relative z-10 max-w-[60%]">
                        <div class="mb-3 flex items-center gap-2">
                            <span
                                class="h-3 w-3 animate-pulse rounded-full bg-[#D4F34A]"
                            ></span>
                            <span
                                class="text-xs font-bold tracking-widest text-gray-400 uppercase"
                                >Palace Court Manager</span
                            >
                        </div>
                        <h3
                            class="mb-4 text-4xl leading-tight font-black text-white"
                        >
                            Welcome back!<br /><span
                                class="text-2xl font-bold text-gray-400"
                                >Manage your courts</span
                            >
                        </h3>
                        <button
                            class="rounded-2xl bg-[#88A740] px-6 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-[#7a9639]"
                        >
                            View Full Schedule
                        </button>
                    </div>
                    <img
                        src="https://cdni.iconscout.com/illustration/premium/thumb/tennis-player-hit-the-ball-2974955-2477382.png"
                        class="absolute -right-8 bottom-0 z-10 h-[340px] w-auto object-contain drop-shadow-2xl transition-transform duration-500 group-hover:scale-105"
                    />
                    <div
                        class="absolute right-0 bottom-0 left-0 z-0 h-24 origin-bottom-right skew-y-3 bg-[#EF583D] opacity-90"
                    ></div>
                </div>

                <!-- JADWAL -->
                <div
                    class="rounded-[2.5rem] border border-slate-100 bg-white p-8 shadow-sm"
                >
                    <div class="mb-6 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="h-3 w-3 rounded-full bg-black"></div>
                            <h3 class="text-xl font-bold text-[#1C1C28]">
                                View Full Schedule
                            </h3>
                        </div>
                        <div class="flex gap-2 text-[10px] font-bold uppercase">
                            <span
                                class="rounded-lg bg-[#D4F34A] px-3 py-1 text-[#1C1C28]"
                                >Available</span
                            ><span
                                class="rounded-lg bg-[#FF6B6B] px-3 py-1 text-white"
                                >Booked</span
                            >
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <div class="min-w-[600px]">
                            <div class="mb-4 grid grid-cols-7 text-center">
                                <div
                                    v-for="day in days"
                                    :key="day.date"
                                    class="text-center"
                                >
                                    <p
                                        class="mb-1 text-xs font-bold text-gray-400 uppercase"
                                    >
                                        {{ day.day_name.substring(0, 3) }}
                                    </p>
                                    <p
                                        class="text-sm font-black text-[#1C1C28]"
                                    >
                                        {{ day.formatted.split(' ')[0] }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="custom-scrollbar h-[300px] space-y-2 overflow-y-auto pr-2"
                            >
                                <div
                                    v-for="hour in 14"
                                    :key="hour"
                                    class="grid grid-cols-7 gap-2"
                                >
                                    <div
                                        v-for="day in days"
                                        :key="day.date + hour"
                                        class="group"
                                    >
                                        <div
                                            class="flex h-14 cursor-pointer flex-col items-center justify-center rounded-xl border border-slate-100 text-[10px] font-bold transition-all hover:shadow-md"
                                            :class="
                                                getBooking(day.date, hour + 5)
                                                    ? getBooking(
                                                          day.date,
                                                          hour + 5,
                                                      ).status === 'checked_in'
                                                        ? 'border-[#D4F34A] bg-[#D4F34A]'
                                                        : 'border-[#FF6B6B] bg-[#FF6B6B] text-white'
                                                    : 'bg-white text-gray-300 hover:border-blue-200'
                                            "
                                        >
                                            <span
                                                v-if="
                                                    !getBooking(
                                                        day.date,
                                                        hour + 5,
                                                    )
                                                "
                                                >{{
                                                    String(hour + 5).padStart(
                                                        2,
                                                        '0',
                                                    )
                                                }}:00</span
                                            >
                                            <span v-else class="uppercase">{{
                                                getBooking(day.date, hour + 5)
                                                    .unit
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN (5/12) -->
            <div class="space-y-6 xl:col-span-5">
                <!-- STATS GRID -->
                <div class="grid grid-cols-2 gap-4">
                    <div
                        class="rounded-[2rem] border border-slate-100 bg-white p-5 shadow-sm"
                    >
                        <p
                            class="text-[10px] font-bold text-gray-400 uppercase"
                        >
                            Booking Hari Ini
                        </p>
                        <h3 class="mt-1 text-3xl font-black text-[#1C1C28]">
                            {{ stats.bookings_today }}
                        </h3>
                        <div
                            class="mt-2 flex items-center gap-1 text-[10px] text-gray-400"
                        >
                            <span
                                class="h-2 w-2 animate-pulse rounded-full bg-red-500"
                            ></span>
                            Online
                        </div>
                    </div>
                    <div
                        class="relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-5 shadow-sm"
                    >
                        <p
                            class="text-[10px] font-bold text-gray-400 uppercase"
                        >
                            Quick Stats
                        </p>
                        <div class="mt-3 flex items-center gap-2">
                            <div
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-[#D4F34A] text-[10px]"
                            >
                                ✓
                            </div>
                            <span class="text-sm font-bold text-[#1C1C28]"
                                >All Ready!</span
                            >
                        </div>
                    </div>
                    <div
                        class="rounded-[2rem] border border-slate-100 bg-white p-5 shadow-sm"
                    >
                        <p
                            class="text-[10px] font-bold text-gray-400 uppercase"
                        >
                            Okupansi
                        </p>
                        <div class="mt-3 flex h-10 items-end gap-1">
                            <div
                                class="h-[40%] w-1/5 rounded-t-sm bg-[#88A740]"
                            ></div>
                            <div
                                class="h-[80%] w-1/5 rounded-t-sm bg-[#EF583D]"
                            ></div>
                            <div
                                class="h-[60%] w-1/5 rounded-t-sm bg-[#88A740]"
                            ></div>
                            <div
                                class="h-[30%] w-1/5 rounded-t-sm bg-[#1C1C28]"
                            ></div>
                            <div
                                class="h-[90%] w-1/5 rounded-t-sm bg-[#D4F34A]"
                            ></div>
                        </div>
                    </div>
                    <div
                        class="rounded-[2rem] border border-slate-100 bg-white p-5 shadow-sm"
                    >
                        <div class="flex items-center justify-between">
                            <p
                                class="text-[10px] font-bold text-gray-400 uppercase"
                            >
                                Status
                            </p>
                            <span class="text-xs">↔</span>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-[#D4F34A] text-lg font-bold"
                            >
                                $
                            </div>
                            <div>
                                <p class="text-xs font-bold text-[#1C1C28]">
                                    Active
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- UPCOMING -->
                <div
                    class="relative overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm"
                >
                    <div class="mb-6 flex items-center justify-between">
                        <h4 class="text-sm font-bold text-[#1C1C28]">
                            Upcoming Bookings
                        </h4>
                    </div>
                    <div class="relative z-10 space-y-3">
                        <div
                            class="flex items-center gap-3 rounded-2xl border border-gray-50 bg-[#F9FAFB] p-3"
                        >
                            <img
                                src="https://i.pravatar.cc/100?img=12"
                                class="h-10 w-10 rounded-full border-2 border-white shadow-sm"
                            />
                            <div class="flex-1">
                                <p class="text-xs font-bold text-[#1C1C28]">
                                    Pak Budi (TWR-A)
                                </p>
                                <p
                                    class="text-[10px] font-medium text-gray-400"
                                >
                                    08:00 - 09:00
                                </p>
                            </div>
                            <div
                                class="h-2 w-2 rounded-full bg-green-500"
                            ></div>
                        </div>
                        <div
                            class="flex items-center gap-3 rounded-2xl border border-gray-50 bg-[#F9FAFB] p-3"
                        >
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-white bg-orange-100 font-bold text-orange-500 shadow-sm"
                            >
                                S
                            </div>
                            <div class="flex-1">
                                <p class="text-xs font-bold text-[#1C1C28]">
                                    Ibu Siti (TWR-B)
                                </p>
                                <p
                                    class="text-[10px] font-medium text-gray-400"
                                >
                                    16:00 - 17:00
                                </p>
                            </div>
                            <div
                                class="h-2 w-2 rounded-full bg-orange-500"
                            ></div>
                        </div>
                    </div>
                    <img
                        src="https://cdni.iconscout.com/illustration/premium/thumb/woman-playing-tennis-2974952-2477379.png"
                        class="pointer-events-none absolute -right-2 bottom-0 z-0 h-32 w-auto opacity-100"
                    />
                </div>

                <!-- MAINTENANCE LOG -->
                <div
                    class="rounded-[2rem] border border-slate-100 bg-white p-6 shadow-sm"
                >
                    <h4 class="mb-5 text-sm font-bold text-[#1C1C28]">
                        Maintenance Log
                    </h4>
                    <div class="space-y-4">
                        <div class="group flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-[#1C1C28] text-xs text-white shadow-md"
                                >
                                    M
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-[#1C1C28]">
                                        Pengecatan
                                    </p>
                                    <p class="text-[10px] text-gray-400">
                                        105 Jums
                                    </p>
                                </div>
                            </div>
                            <div
                                class="h-3 w-3 rounded-full border-2 border-green-500 bg-white"
                            ></div>
                        </div>
                        <div class="group flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-[#D4F34A] text-xs font-bold text-[#1C1C28] shadow-md"
                                >
                                    88
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-[#1C1C28]">
                                        Perbaikan Net
                                    </p>
                                    <p class="text-[10px] text-gray-400">
                                        13 Jums
                                    </p>
                                </div>
                            </div>
                            <div
                                class="h-3 w-3 rounded-full border-2 border-green-500 bg-white"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 10px;
}
</style>
