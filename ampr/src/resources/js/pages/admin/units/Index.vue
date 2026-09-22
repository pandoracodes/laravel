<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';

const props = defineProps({ units: Object, filters: Object });
const search = ref(props.filters.search || '');

// Modals State
const showAddModal = ref(false);
const showImportModal = ref(false);
const showEditPinModal = ref(false);

// Forms
const formAdd = useForm({ unit_number: '', owner_name: '', access_code: '' });
const formImport = useForm({ file: null });
const formEditPin = useForm({ id: null, unit_number: '', new_pin: '' });

// Search Logic
watch(
    search,
    debounce(
        (val) =>
            router.get(
                route('admin.units.index'),
                { search: val },
                { preserveState: true, replace: true },
            ),
        300,
    ),
);

// Helper hitung nomor urut
const getRowNumber = (index) => {
    return (props.units.current_page - 1) * props.units.per_page + index + 1;
};

// Actions
const submitAdd = () =>
    formAdd.post(route('admin.units.store'), {
        onSuccess: () => {
            showAddModal.value = false;
            formAdd.reset();
        },
    });
const submitImport = () =>
    formImport.post(route('admin.units.import'), {
        onSuccess: () => {
            showImportModal.value = false;
            formImport.reset();
        },
    });

// Edit PIN Logic
const openEditPin = (unit) => {
    formEditPin.id = unit.id;
    formEditPin.unit_number = unit.unit_number;
    formEditPin.new_pin = '';
    showEditPinModal.value = true;
};

const submitEditPin = () => {
    formEditPin.post(route('admin.units.update-pin', formEditPin.id), {
        onSuccess: () => {
            showEditPinModal.value = false;
            formEditPin.reset();
        },
    });
};

const resetPin = (id, number) =>
    confirm(`Reset PIN unit ${number} ke default (123456)?`) &&
    router.post(route('admin.units.reset-pin', id));

const deleteUnit = (id) =>
    confirm('Hapus unit ini? Data tidak bisa dikembalikan.') &&
    router.delete(route('admin.units.destroy', id));
</script>

<template>
    <AdminLayout>
        <template #header>Unit Management</template>
        <Head title="Admin Units" />

        <!-- TOP ACTION BAR -->
        <div
            class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center"
        >
            <!-- Search Bar -->
            <div class="group relative w-full md:w-80">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari Unit / Pemilik..."
                    class="w-full rounded-[20px] border-none bg-white py-4 pr-4 pl-12 text-sm font-bold text-[#2D3A4B] shadow-sm ring-1 ring-slate-100 transition-all group-hover:shadow-md focus:ring-2 focus:ring-[#1A5F7A]"
                />
                <svg
                    class="absolute top-3.5 left-4 h-5 w-5 text-slate-400 transition-colors group-focus-within:text-[#1A5F7A]"
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

            <!-- Action Buttons -->
            <div class="flex gap-3">
                <button
                    @click="showImportModal = true"
                    class="flex items-center gap-2 rounded-[20px] border border-slate-200 bg-white px-6 py-3 font-bold text-slate-600 shadow-sm transition-all hover:border-slate-300 hover:bg-slate-50 hover:shadow-md active:scale-95"
                >
                    <svg
                        class="h-5 w-5 text-emerald-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                        ></path>
                    </svg>
                    <span>Import Excel</span>
                </button>
                <button
                    @click="showAddModal = true"
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
                            stroke-width="2.5"
                            d="M12 4v16m8-8H4"
                        ></path>
                    </svg>
                    <span>Tambah Unit</span>
                </button>
            </div>
        </div>

        <!-- MAIN TABLE CARD -->
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
                                No.
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Unit Number
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Pemilik
                            </th>
                            <th
                                class="px-8 py-6 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                PIN Akses
                            </th>
                            <!-- INI KOLOM KUOTA YANG TERLEWAT -->
                            <th
                                class="px-8 py-6 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Kuota
                            </th>
                            <th
                                class="px-8 py-6 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-8 py-6 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-if="units.data.length === 0">
                            <td colspan="6" class="px-8 py-12 text-center">
                                <div
                                    class="flex flex-col items-center justify-center"
                                >
                                    <div
                                        class="mb-3 rounded-full bg-slate-50 p-4"
                                    >
                                        <svg
                                            class="h-8 w-8 text-slate-300"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                            ></path>
                                        </svg>
                                    </div>
                                    <p class="font-bold text-slate-500">
                                        Belum ada data unit.
                                    </p>
                                    <p class="text-sm text-slate-400">
                                        Silakan tambah manual atau import excel.
                                    </p>
                                </div>
                            </td>
                        </tr>

                        <tr
                            v-for="(unit, index) in units.data"
                            :key="unit.id"
                            class="group transition-colors hover:bg-slate-50/80"
                        >
                            <!-- NOMOR URUT -->
                            <td
                                class="px-8 py-5 text-sm font-bold text-slate-400"
                            >
                                {{
                                    String(getRowNumber(index)).padStart(2, '0')
                                }}
                            </td>

                            <!-- UNIT NUMBER (Hapus Icon TW) -->
                            <td class="px-8 py-5">
                                <span
                                    class="text-base font-black text-[#2D3A4B] transition-colors group-hover:text-[#1A5F7A]"
                                >
                                    {{ unit.unit_number }}
                                </span>
                            </td>

                            <!-- PEMILIK -->
                            <td class="px-8 py-5">
                                <p class="text-sm font-bold text-slate-600">
                                    {{ unit.owner_name || '-' }}
                                </p>
                                <p
                                    class="text-[10px] font-medium tracking-wide text-slate-400 uppercase"
                                >
                                    Owner
                                </p>
                            </td>

                            <!-- PIN (Hidden for Security) -->
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 font-mono text-sm font-bold tracking-widest text-slate-400"
                                    >
                                        ••••••
                                    </div>
                                    <button
                                        @click="openEditPin(unit)"
                                        class="group flex items-center justify-center rounded-lg p-1.5 text-slate-400 transition-all hover:bg-blue-50 hover:text-[#1A5F7A]"
                                        title="Ubah PIN"
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
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>

                            <!-- === TAMBAHAN: ISI DATA KUOTA === -->
                            <td class="px-8 py-5 text-center">
                                <span
                                    class="text-sm font-black"
                                    :class="
                                        unit.quota_usage >= 2
                                            ? 'text-rose-500'
                                            : 'text-[#1A5F7A]'
                                    "
                                >
                                    {{ unit.quota_usage }}
                                    <span
                                        class="text-xs font-bold text-slate-400"
                                        >/ 2 Jam</span
                                    >
                                </span>
                            </td>
                            <!-- ================================ -->

                            <!-- STATUS (DIPERBAIKI) -->
                            <td class="px-8 py-5">
                                <div class="flex items-center">
                                    <!-- Jika kena Banned -->
                                    <span
                                        v-if="unit.is_banned_until"
                                        class="flex flex-col items-start gap-1"
                                    >
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full border border-rose-100 bg-rose-50 px-3 py-1 text-[10px] font-bold tracking-wide text-rose-600 uppercase"
                                        >
                                            <span
                                                class="h-1.5 w-1.5 animate-pulse rounded-full bg-rose-500"
                                            ></span>
                                            Banned
                                        </span>
                                        <!-- Menampilkan tanggal batas waktu banned -->
                                        <span
                                            class="ml-1 text-[9px] font-bold text-slate-400"
                                        >
                                            s/d
                                            {{
                                                new Date(
                                                    unit.is_banned_until,
                                                ).toLocaleDateString('id-ID', {
                                                    day: 'numeric',
                                                    month: 'short',
                                                    year: 'numeric',
                                                })
                                            }}
                                        </span>
                                    </span>

                                    <!-- Jika Active -->
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1.5 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-[10px] font-bold tracking-wide text-emerald-600 uppercase"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                        ></span>
                                        Active
                                    </span>
                                </div>
                            </td>
                            <!-- ======================= -->
                            <!-- AKSI (Rata Tengah) -->
                            <td class="px-8 py-5">
                                <div class="flex justify-center gap-2">
                                    <button
                                        @click="
                                            resetPin(unit.id, unit.unit_number)
                                        "
                                        class="group/btn relative flex h-9 w-9 items-center justify-center rounded-xl bg-orange-50 text-orange-500 transition-all hover:bg-orange-500 hover:text-white hover:shadow-lg hover:shadow-orange-500/30 active:scale-95"
                                        title="Reset PIN Default"
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
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                            ></path>
                                        </svg>
                                    </button>
                                    <button
                                        @click="deleteUnit(unit.id)"
                                        class="group/btn flex h-9 w-9 items-center justify-center rounded-xl bg-rose-50 text-rose-500 transition-all hover:bg-rose-500 hover:text-white hover:shadow-lg hover:shadow-rose-500/30 active:scale-95"
                                        title="Hapus Unit"
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
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            ></path>
                                        </svg>
                                    </button>
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
            v-if="units.links.length > 3"
        >
            <Link
                v-for="(link, k) in units.links"
                :key="k"
                :href="link.url ?? '#'"
                v-html="link.label"
                class="flex h-10 min-w-[40px] items-center justify-center rounded-xl text-xs font-bold shadow-sm transition-all"
                :class="
                    link.active
                        ? 'scale-105 bg-[#1A5F7A] text-white shadow-md shadow-[#1A5F7A]/30'
                        : 'border border-slate-100 bg-white text-slate-500 hover:bg-slate-50'
                "
            />
        </div>

        <!-- MODAL ADD UNIT -->
        <div
            v-if="showAddModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-[#1A5F7A]/20 p-4 backdrop-blur-sm transition-opacity"
        >
            <div
                class="w-full max-w-md overflow-hidden rounded-[2.5rem] bg-white p-8 shadow-2xl ring-1 ring-slate-100"
            >
                <div class="mb-6 flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-[#1A5F7A]"
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
                                stroke-width="2.5"
                                d="M12 4v16m8-8H4"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-[#1A5F7A]">
                            Tambah Unit
                        </h3>
                        <p class="text-xs font-medium text-slate-400">
                            Masukkan data unit baru
                        </p>
                    </div>
                </div>

                <form @submit.prevent="submitAdd" class="space-y-5">
                    <div>
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Nomor Unit</label
                        >
                        <input
                            v-model="formAdd.unit_number"
                            placeholder="Cth: TWR-A-0101"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-3.5 font-bold text-[#2D3A4B] uppercase placeholder-slate-300 transition-all focus:border-[#1A5F7A] focus:bg-white focus:ring-2 focus:ring-[#1A5F7A]/20"
                            required
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Nama Pemilik</label
                        >
                        <input
                            v-model="formAdd.owner_name"
                            placeholder="Nama Lengkap"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-3.5 font-bold text-[#2D3A4B] placeholder-slate-300 transition-all focus:border-[#1A5F7A] focus:bg-white focus:ring-2 focus:ring-[#1A5F7A]/20"
                        />
                    </div>
                    <div>
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >PIN Akses (Opsional)</label
                        >
                        <input
                            v-model="formAdd.access_code"
                            type="text"
                            placeholder="Default: 123456"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-3.5 font-mono font-bold tracking-wider text-[#2D3A4B] placeholder-slate-300 transition-all focus:border-[#1A5F7A] focus:bg-white focus:ring-2 focus:ring-[#1A5F7A]/20"
                        />
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            @click="showAddModal = false"
                            class="flex-1 rounded-2xl py-4 font-bold text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="flex-1 rounded-2xl bg-[#1A5F7A] py-4 font-bold text-white shadow-lg shadow-[#1A5F7A]/30 transition-all hover:bg-[#164e63] active:scale-95"
                        >
                            Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL IMPORT (Tampilan Baru Sesuai Gambar) -->
        <div
            v-if="showImportModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm transition-opacity"
        >
            <!-- Card Container -->
            <div
                class="w-full max-w-[480px] scale-100 transform overflow-hidden rounded-[2.5rem] bg-white p-8 text-center shadow-2xl transition-all"
            >
                <!-- Icon Header -->
                <div class="mb-5 flex justify-center">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-[1.2rem] bg-emerald-50 shadow-sm"
                    >
                        <span class="text-3xl">📊</span>
                    </div>
                </div>

                <!-- Title -->
                <h3 class="mb-2 text-2xl font-black text-[#2D3A4B]">
                    Import Excel
                </h3>

                <!-- Instructions -->
                <div class="mb-8 px-2 text-sm text-slate-500">
                    <p class="mb-2">Pastikan file .xlsx memiliki kolom:</p>
                    <div
                        class="flex flex-wrap justify-center gap-2 font-mono text-xs"
                    >
                        <span
                            class="rounded bg-slate-100 px-2 py-1 text-slate-600"
                            >unit_number</span
                        >
                        <span
                            class="rounded bg-slate-100 px-2 py-1 text-slate-600"
                            >owner_name</span
                        >
                        <!-- Menambahkan access_code sesuai request -->
                        <span
                            class="rounded bg-slate-100 px-2 py-1 text-slate-600"
                            >access_code</span
                        >
                    </div>
                </div>

                <form @submit.prevent="submitImport">
                    <!-- Drop Zone Area -->
                    <div class="group relative mb-8">
                        <input
                            type="file"
                            @input="formImport.file = $event.target.files[0]"
                            class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
                            accept=".xlsx,.csv"
                            required
                        />
                        <div
                            class="flex h-32 flex-col items-center justify-center rounded-[1.5rem] border-2 border-dashed border-slate-200 bg-slate-50/50 transition-all duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-50/50"
                        >
                            <div v-if="formImport.file" class="px-4">
                                <p class="mb-1 text-2xl">📄</p>
                                <p
                                    class="line-clamp-2 text-sm font-bold break-all text-emerald-600"
                                >
                                    {{ formImport.file.name }}
                                </p>
                            </div>
                            <div v-else>
                                <p
                                    class="font-bold text-slate-400 transition-colors group-hover:text-emerald-500"
                                >
                                    Klik untuk upload file
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between px-4">
                        <button
                            type="button"
                            @click="showImportModal = false"
                            class="rounded-xl px-6 py-3 font-bold text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-600"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="min-w-[140px] rounded-2xl bg-[#00C48C] py-3.5 font-bold text-white shadow-lg shadow-[#00C48C]/30 transition-all hover:bg-[#00b07d] hover:shadow-xl active:scale-95"
                        >
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT PIN -->
        <div
            v-if="showEditPinModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-[#1A5F7A]/20 p-4 backdrop-blur-sm"
        >
            <div
                class="w-full max-w-sm overflow-hidden rounded-[2.5rem] bg-white p-8 shadow-2xl"
            >
                <div class="mb-6 text-center">
                    <h3 class="text-xl font-black text-[#2D3A4B]">
                        Ubah PIN Akses
                    </h3>
                    <p class="mt-1 text-sm font-medium text-slate-500">
                        Unit:
                        <span class="font-bold text-[#1A5F7A]">{{
                            formEditPin.unit_number
                        }}</span>
                    </p>
                </div>

                <form @submit.prevent="submitEditPin" class="space-y-6">
                    <div class="text-center">
                        <input
                            v-model="formEditPin.new_pin"
                            type="text"
                            placeholder="PIN BARU"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-4 text-center text-2xl font-black tracking-[0.5em] text-[#2D3A4B] placeholder-slate-300 transition-all focus:border-[#1A5F7A] focus:bg-white focus:ring-2 focus:ring-[#1A5F7A]/20"
                            required
                            minlength="4"
                            maxlength="8"
                        />
                        <p
                            class="mt-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase"
                        >
                            Min. 4 Digit Angka
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="showEditPinModal = false"
                            class="flex-1 rounded-2xl py-3.5 font-bold text-slate-400 hover:text-slate-600"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="flex-1 rounded-2xl bg-[#1A5F7A] py-3.5 font-bold text-white shadow-lg shadow-[#1A5F7A]/30 hover:bg-[#164e63]"
                        >
                            Update PIN
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
