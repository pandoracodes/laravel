<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';

const props = defineProps({
    bookings: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');

// Logika Search Engine (Otomatis nyari pas ngetik)
watch(
    search,
    debounce((value) => {
        router.get(
            route('admin.reports.index'),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 300),
);

// Helpers Format Tanggal & Jam
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

const formatTime = (start, end) => {
    const s = new Date(start).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
    const e = new Date(end).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
    return `${s} - ${e}`;
};

// Helper untuk membersihkan tanda kurung, kutip, dan garis miring
const formatPlayers = (playersString) => {
    if (!playersString) return '-';
    // Menghapus tanda kurung siku [ ], tanda kutip ", dan garis miring \
    return playersString.replace(/[\[\]"\\]/g, '');
};

// Helper Warna Status
const getStatusBadge = (status) => {
    switch (status) {
        case 'booked':
            return 'bg-blue-50 text-blue-600 border-blue-100';
        case 'checked_in':
            return 'bg-emerald-50 text-emerald-600 border-emerald-100';
        case 'no_show':
            return 'bg-rose-50 text-rose-600 border-rose-100';
        case 'maintenance':
            return 'bg-orange-50 text-orange-600 border-orange-100';
        default:
            return 'bg-slate-50 text-slate-600 border-slate-100';
    }
};
</script>

<template>
    <AdminLayout>
        <template #header>Laporan Reservasi</template>
        <Head title="Laporan Reservasi" />

        <!-- TOP ACTION BAR & SEARCH ENGINE -->
        <div
            class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center"
        >
            <!-- Search Bar -->
            <div class="group relative w-full md:w-96">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari Kode Tiket, Unit, Pemain, atau Status..."
                    class="w-full rounded-[20px] border-none bg-white py-4 pr-4 pl-12 text-sm font-bold text-[#2D3A4B] shadow-sm ring-1 ring-slate-100 transition-all focus:ring-2 focus:ring-[#1A5F7A]"
                />
                <svg
                    class="absolute top-3.5 left-4 h-5 w-5 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2.5"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    ></path>
                </svg>
            </div>

            <!-- Tombol Export (Opsional untuk Skripsi) -->
            <!-- Tombol Export Excel -->
            <a
                :href="route('admin.reports.export')"
                target="_blank"
                class="flex items-center gap-2 rounded-[20px] bg-[#1A5F7A] px-6 py-3 font-bold text-white shadow-lg shadow-[#1A5F7A]/30 transition-all hover:bg-[#164e63] hover:shadow-xl hover:shadow-[#1A5F7A]/40 active:scale-95"
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
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    ></path>
                </svg>
                <span>Export Excel</span>
            </a>
        </div>

        <!-- TABEL LAPORAN -->
        <div
            class="overflow-hidden rounded-[2.5rem] border border-white/60 bg-white shadow-sm ring-1 ring-slate-100"
        >
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Unit
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Jadwal Main
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Pemain
                            </th>
                            <th
                                class="px-8 py-6 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-8 py-6 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Kode Tiket
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-if="bookings.data.length === 0">
                            <td
                                colspan="5"
                                class="px-8 py-12 text-center font-bold text-slate-500"
                            >
                                Tidak ada data ditemukan.
                            </td>
                        </tr>
                        <tr
                            v-for="booking in bookings.data"
                            :key="booking.id"
                            class="group transition-colors hover:bg-slate-50/80"
                        >
                            <!-- Unit -->
                            <td class="px-8 py-5">
                                <span
                                    v-if="booking.status === 'maintenance'"
                                    class="text-sm font-black text-orange-500"
                                    >ADMIN</span
                                >
                                <span
                                    v-else
                                    class="text-sm font-black text-[#1A5F7A]"
                                    >{{ booking.unit_number || '-' }}</span
                                >
                            </td>

                            <!-- Jadwal -->
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-slate-700">
                                    {{ formatDate(booking.start_time) }}
                                </p>
                                <p
                                    class="mt-0.5 text-[10px] font-bold text-slate-400"
                                >
                                    {{
                                        formatTime(
                                            booking.start_time,
                                            booking.end_time,
                                        )
                                    }}
                                </p>
                            </td>

                            <!-- Pemain / Alasan Maintenance -->
                            <td class="px-8 py-5">
                                <p
                                    class="max-w-[200px] truncate text-sm font-bold text-slate-600"
                                >
                                    {{ formatPlayers(booking.player_names) }}
                                </p>
                            </td>

                            <!-- Status -->
                            <td class="px-8 py-5 text-center">
                                <span
                                    :class="getStatusBadge(booking.status)"
                                    class="inline-flex rounded-full border px-3 py-1 text-[10px] font-black tracking-wider uppercase"
                                >
                                    {{ booking.status.replace('_', ' ') }}
                                </span>
                            </td>

                            <!-- Kode Tiket -->
                            <td class="px-8 py-5 text-center">
                                <div
                                    class="inline-flex rounded-lg bg-slate-50 px-3 py-1.5 font-mono text-xs font-bold text-slate-400"
                                >
                                    {{
                                        booking.booking_code
                                            ? booking.booking_code.substring(
                                                  0,
                                                  8,
                                              ) + '...'
                                            : '-'
                                    }}
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PAGINATION -->
        <div
            class="mt-8 flex justify-center gap-2"
            v-if="bookings.links && bookings.links.length > 3"
        >
            <Link
                v-for="(link, k) in bookings.links"
                :key="k"
                :href="link.url ?? '#'"
                v-html="link.label"
                class="flex h-10 min-w-[40px] items-center justify-center rounded-xl text-xs font-bold transition-all"
                :class="
                    link.active
                        ? 'bg-[#1A5F7A] text-white shadow-md'
                        : 'border border-slate-100 bg-white text-slate-500'
                "
            />
        </div>
    </AdminLayout>
</template>
