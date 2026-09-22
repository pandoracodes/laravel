<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';

const props = defineProps({
    maintenances: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const today = new Date().toISOString().split('T')[0];

// State untuk Modal Edit
const showEditModal = ref(false);

// Form Tambah
const form = useForm({
    start_date: today,
    end_date: today,
    start_hour: 8,
    end_hour: 10,
    reason: 'Perbaikan Lapangan',
});

// Form Edit
const formEdit = useForm({
    id: null,
    reason: '',
    time_desc: '', // Hanya untuk display di modal
});

// Search Logic
watch(
    search,
    debounce((val) => {
        router.get(
            route('admin.maintenance.index'),
            { search: val },
            { preserveState: true, replace: true },
        );
    }, 300),
);

// Submit Tambah
const submit = () => {
    if (!confirm(`Blokir jadwal dari ${form.start_date} s/d ${form.end_date}?`))
        return;
    form.post(route('admin.maintenance.store'), {
        onSuccess: () => form.reset('reason'),
    });
};

// Hapus
const deleteMaintenance = (id) => {
    if (confirm('Hapus jadwal maintenance ini? Slot akan kembali dibuka.')) {
        router.delete(route('admin.maintenance.destroy', id));
    }
};

// Buka Modal Edit
const openEditModal = (item) => {
    formEdit.id = item.id;
    formEdit.reason = getReason(item.player_names);
    formEdit.time_desc = `${formatDate(item.start_time)}, ${formatTime(item.start_time)} - ${formatTime(item.end_time)}`;
    showEditModal.value = true;
};

// Submit Edit
const submitEdit = () => {
    formEdit.put(route('admin.maintenance.update', formEdit.id), {
        onSuccess: () => {
            showEditModal.value = false;
            formEdit.reset();
        },
    });
};

// Helpers
const formatDate = (dt) =>
    new Date(dt).toLocaleDateString('id-ID', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
const formatTime = (dt) =>
    new Date(dt).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
    });
const getRowNumber = (index) =>
    (props.maintenances.current_page - 1) * props.maintenances.per_page +
    index +
    1;
const getReason = (jsonString) => {
    try {
        return JSON.parse(jsonString)[0] || jsonString;
    } catch (e) {
        return jsonString;
    }
};
</script>

<template>
    <AdminLayout>
        <template #header>Maintenance Mode</template>
        <Head title="Maintenance" />

        <div class="grid grid-cols-1 items-start gap-8 xl:grid-cols-3">
            <!-- FORM BLOKIR (Kiri) -->
            <div class="space-y-6 xl:col-span-1">
                <div
                    class="flex items-center gap-4 rounded-[2rem] border border-amber-100 bg-amber-50 p-6"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-100 text-2xl"
                    >
                        🚧
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-amber-800">
                            Blokir Jadwal
                        </h3>
                        <p class="text-xs font-medium text-amber-600">
                            Tutup lapangan untuk renovasi.
                        </p>
                    </div>
                </div>

                <div
                    class="rounded-[2.5rem] border border-slate-100 bg-white p-8 shadow-sm"
                >
                    <form @submit.prevent="submit" class="space-y-5">
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                    >Dari Tanggal</label
                                >
                                <input
                                    v-model="form.start_date"
                                    type="date"
                                    :min="today"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 font-bold text-slate-700 focus:border-red-500 focus:ring-red-500"
                                />
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                    >Sampai Tanggal</label
                                >
                                <input
                                    v-model="form.end_date"
                                    type="date"
                                    :min="form.start_date"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 font-bold text-slate-700 focus:border-red-500 focus:ring-red-500"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label
                                    class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                    >Jam Mulai</label
                                >
                                <select
                                    v-model="form.start_hour"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-3 py-3 font-bold text-slate-700 focus:border-red-500 focus:ring-red-500"
                                >
                                    <option
                                        v-for="i in 16"
                                        :key="i"
                                        :value="i + 5"
                                    >
                                        {{ String(i + 5).padStart(2, '0') }}:00
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                    >Jam Selesai</label
                                >
                                <select
                                    v-model="form.end_hour"
                                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-3 py-3 font-bold text-slate-700 focus:border-red-500 focus:ring-red-500"
                                >
                                    <option
                                        v-for="i in 17"
                                        :key="i"
                                        :value="i + 5"
                                        :disabled="i + 5 <= form.start_hour"
                                    >
                                        {{ String(i + 5).padStart(2, '0') }}:00
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label
                                class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >Alasan Maintenance</label
                            >
                            <input
                                v-model="form.reason"
                                type="text"
                                placeholder="Cth: Pengecatan Ulang"
                                class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 font-bold text-slate-700 placeholder-slate-400 focus:border-red-500 focus:ring-red-500"
                                required
                            />
                        </div>

                        <div
                            v-if="form.errors.slot"
                            class="rounded-xl bg-red-50 p-3 text-xs font-bold text-red-600"
                        >
                            ⚠️ {{ form.errors.slot }}
                        </div>

                        <button
                            :disabled="form.processing"
                            type="submit"
                            class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-red-600 py-4 font-bold text-white shadow-lg shadow-red-200 transition hover:bg-red-700 hover:shadow-xl active:scale-95 disabled:opacity-70"
                        >
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else>🔒 Kunci Jadwal</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- TABLE LIST (Kanan) -->
            <div class="space-y-6 xl:col-span-2">
                <div
                    class="flex flex-col items-end justify-between gap-4 md:flex-row"
                >
                    <div>
                        <h2 class="text-2xl font-black text-[#2D3A4B]">
                            Riwayat Maintenance
                        </h2>
                        <p class="text-sm text-slate-500">
                            Daftar jadwal yang sedang ditutup.
                        </p>
                    </div>
                    <div class="relative w-full md:w-64">
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari Alasan / Kode..."
                            class="w-full rounded-[20px] border-none bg-white py-3 pr-4 pl-10 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-100 transition focus:ring-2 focus:ring-red-500"
                        />
                        <svg
                            class="absolute top-3 left-3.5 h-5 w-5 text-slate-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </div>
                </div>

                <div
                    class="overflow-hidden rounded-[2.5rem] border border-white/60 bg-white shadow-sm ring-1 ring-slate-100"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead class="bg-[#F8FAFC]">
                                <tr>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >
                                        No.
                                    </th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >
                                        Tanggal & Jam
                                    </th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >
                                        Alasan
                                    </th>
                                    <th
                                        class="px-6 py-5 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >
                                        Kode
                                    </th>
                                    <th
                                        class="px-6 py-5 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                                    >
                                        Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <tr v-if="maintenances.data.length === 0">
                                    <td
                                        colspan="5"
                                        class="px-6 py-12 text-center font-medium text-slate-400"
                                    >
                                        Tidak ada data maintenance.
                                    </td>
                                </tr>
                                <tr
                                    v-for="(item, index) in maintenances.data"
                                    :key="item.id"
                                    class="group transition hover:bg-slate-50/50"
                                >
                                    <td
                                        class="px-6 py-4 text-sm font-bold text-slate-400"
                                    >
                                        {{
                                            String(
                                                getRowNumber(index),
                                            ).padStart(2, '0')
                                        }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-black text-slate-700"
                                                >{{
                                                    formatDate(item.start_time)
                                                }}</span
                                            >
                                            <span
                                                class="text-xs font-bold text-slate-500"
                                                >{{
                                                    formatTime(item.start_time)
                                                }}
                                                -
                                                {{ formatTime(item.end_time) }}
                                                WIB</span
                                            >
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center rounded-lg border border-amber-100 bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-600"
                                        >
                                            {{ getReason(item.player_names) }}
                                        </span>
                                    </td>
                                    <!-- KOLOM KODE DIKEMBALIKAN -->
                                    <td class="px-6 py-4">
                                        <span
                                            class="rounded bg-slate-100 px-2 py-1 font-mono text-xs font-bold text-slate-400"
                                            >{{ item.booking_code }}</span
                                        >
                                    </td>
                                    <!-- AKSI -->
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <!-- Tombol Edit -->
                                            <button
                                                @click="openEditModal(item)"
                                                class="rounded-xl bg-blue-50 p-2 text-blue-600 transition hover:bg-blue-100"
                                                title="Edit Alasan"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                                    />
                                                </svg>
                                            </button>
                                            <!-- Tombol Hapus -->
                                            <button
                                                @click="
                                                    deleteMaintenance(item.id)
                                                "
                                                class="rounded-xl bg-red-50 p-2 text-red-600 transition hover:bg-red-100"
                                                title="Hapus"
                                            >
                                                <svg
                                                    class="h-4 w-4"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    class="flex justify-center gap-2"
                    v-if="maintenances.links.length > 3"
                >
                    <Link
                        v-for="(link, k) in maintenances.links"
                        :key="k"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        class="flex h-10 min-w-[40px] items-center justify-center rounded-xl text-xs font-bold shadow-sm transition-all"
                        :class="
                            link.active
                                ? 'scale-105 bg-[#2D3A4B] text-white'
                                : 'border border-slate-100 bg-white text-slate-500 hover:bg-slate-50'
                        "
                    />
                </div>
            </div>
        </div>

        <!-- MODAL EDIT ALASAN -->
        <div
            v-if="showEditModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
        >
            <div class="w-full max-w-sm rounded-[2rem] bg-white p-8 shadow-2xl">
                <h3 class="mb-2 text-xl font-black text-[#2D3A4B]">
                    Edit Alasan
                </h3>
                <p class="mb-4 text-xs font-bold text-slate-400">
                    {{ formEdit.time_desc }}
                </p>

                <form @submit.prevent="submitEdit" class="space-y-4">
                    <input
                        v-model="formEdit.reason"
                        type="text"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-4 py-3 font-bold text-slate-700 focus:border-blue-500 focus:ring-blue-500"
                        required
                    />

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="showEditModal = false"
                            class="flex-1 rounded-xl py-3 font-bold text-slate-400 hover:text-slate-600"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="flex-1 rounded-xl bg-blue-600 py-3 font-bold text-white shadow-lg shadow-blue-200 hover:bg-blue-700"
                        >
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
