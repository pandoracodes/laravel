<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';

const props = defineProps({
    bannedUnits: Object, // Sekarang Object karena pagination
    filters: Object,
    totalBanned: Number,
});

const search = ref(props.filters.search || '');

// Search Logic
watch(
    search,
    debounce(
        (val) =>
            router.get(
                route('admin.sanctions.index'),
                { search: val },
                { preserveState: true, replace: true },
            ),
        300,
    ),
);

const removeSanction = (id, number) => {
    if (
        confirm(
            `Yakin ingin mencabut sanksi untuk unit ${number}? Unit akan bisa melakukan booking kembali.`,
        )
    ) {
        router.delete(route('admin.sanctions.destroy', id));
    }
};

// Helper Format Tanggal
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Helper Hitung Sisa Hari
const getRemainingDays = (dateString) => {
    const end = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(end - now);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Hari ini';
    return `${diffDays} hari lagi`;
};

// Helper No Urut
const getRowNumber = (index) => {
    return (
        (props.bannedUnits.current_page - 1) * props.bannedUnits.per_page +
        index +
        1
    );
};
</script>

<template>
    <AdminLayout>
        <template #header>Sanction Management</template>
        <Head title="Sanksi & Banned" />

        <!-- HEADER SECTION -->
        <div
            class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-end"
        >
            <div>
                <div
                    class="mb-2 inline-flex items-center gap-2 rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-bold text-rose-600"
                >
                    <span class="relative flex h-2 w-2">
                        <span
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-rose-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex h-2 w-2 rounded-full bg-rose-500"
                        ></span>
                    </span>
                    {{ totalBanned }} Unit Ter-Banned
                </div>
                <!-- <h2 class="text-3xl font-black text-[#2D3A4B]"> -->
                <!-- Daftar Sanksi -->
                <!-- </h2> -->
                <!-- <p class="text-sm font-medium text-slate-500"> -->
                <!-- Kelola unit yang sedang dalam masa hukuman/sanksi. -->
                <!-- </p> -->
            </div>

            <!-- Search Bar -->
            <div class="group relative w-full md:w-72">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari Unit Banned..."
                    class="w-full rounded-[20px] border-none bg-white py-4 pr-4 pl-12 text-sm font-bold text-[#2D3A4B] shadow-sm ring-1 ring-slate-100 transition-all group-hover:shadow-md focus:ring-2 focus:ring-rose-500"
                />
                <svg
                    class="absolute top-3.5 left-4 h-5 w-5 text-slate-400 transition-colors group-focus-within:text-rose-500"
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
        </div>

        <!-- EMPTY STATE -->
        <div
            v-if="bannedUnits.data.length === 0"
            class="flex flex-col items-center justify-center rounded-[2.5rem] border border-white/60 bg-white py-20 text-center shadow-sm"
        >
            <div
                class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-50 shadow-sm"
            >
                <span class="text-5xl">🛡️</span>
            </div>
            <h3 class="text-2xl font-black text-[#2D3A4B]">Semua Aman!</h3>
            <p class="max-w-md text-slate-400">
                Tidak ada unit yang sedang disanksi saat ini. Semua unit dapat
                melakukan booking lapangan.
            </p>
        </div>

        <!-- TABLE CARD -->
        <div
            v-else
            class="overflow-hidden rounded-[2.5rem] border border-white/60 bg-white shadow-sm ring-1 ring-slate-100"
        >
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                No.
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Unit & Pemilik
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Detail Sanksi
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Countdown
                            </th>
                            <th
                                class="px-8 py-6 text-right text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr
                            v-for="(unit, index) in bannedUnits.data"
                            :key="unit.id"
                            class="group transition-colors hover:bg-rose-50/30"
                        >
                            <!-- NO -->
                            <td
                                class="px-8 py-5 text-sm font-bold text-slate-400"
                            >
                                {{
                                    String(getRowNumber(index)).padStart(2, '0')
                                }}
                            </td>

                            <!-- UNIT & PEMILIK -->
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 text-xs font-black text-rose-600 shadow-sm"
                                    >
                                        {{ unit.unit_number.substring(0, 2) }}
                                    </div>
                                    <div>
                                        <p
                                            class="text-base font-black text-[#2D3A4B]"
                                        >
                                            {{ unit.unit_number }}
                                        </p>
                                        <p
                                            class="text-xs font-bold text-slate-500"
                                        >
                                            {{ unit.owner_name || 'No Owner' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- TANGGAL BERAKHIR -->
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] font-bold tracking-wide text-slate-400 uppercase"
                                        >Berakhir Pada</span
                                    >
                                    <span
                                        class="text-sm font-bold text-rose-600"
                                    >
                                        {{ formatDate(unit.is_banned_until) }}
                                    </span>
                                </div>
                            </td>

                            <!-- COUNTDOWN (Sisa Hari) -->
                            <td class="px-8 py-5">
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-orange-100 bg-orange-50 px-3 py-1.5 text-xs font-bold text-orange-600"
                                >
                                    <svg
                                        class="h-3.5 w-3.5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    {{ getRemainingDays(unit.is_banned_until) }}
                                </span>
                            </td>

                            <!-- AKSI -->
                            <td class="px-8 py-5 text-right">
                                <button
                                    @click="
                                        removeSanction(
                                            unit.id,
                                            unit.unit_number,
                                        )
                                    "
                                    class="group/btn relative inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600 shadow-sm transition-all hover:border-emerald-500 hover:bg-emerald-50 hover:text-emerald-700 active:scale-95"
                                >
                                    <svg
                                        class="h-4 w-4 text-slate-400 transition-colors group-hover/btn:text-emerald-600"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    Cabut Sanksi
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PAGINATION -->
        <div
            class="mt-8 flex justify-center gap-2"
            v-if="bannedUnits.links.length > 3"
        >
            <Link
                v-for="(link, k) in bannedUnits.links"
                :key="k"
                :href="link.url ?? '#'"
                v-html="link.label"
                class="flex h-10 min-w-[40px] items-center justify-center rounded-xl text-xs font-bold shadow-sm transition-all"
                :class="
                    link.active
                        ? 'scale-105 bg-[#2D3A4B] text-white shadow-md shadow-[#2D3A4B]/30'
                        : 'border border-slate-100 bg-white text-slate-500 hover:bg-slate-50'
                "
            />
        </div>
    </AdminLayout>
</template>
