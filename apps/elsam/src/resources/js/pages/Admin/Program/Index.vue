<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

import { 
    Search, Plus, Pencil, Trash2, BookOpen, 
    Layers, ChevronRight, Image as ImageIcon,
    AlertCircle
} from 'lucide-vue-next';

const props = defineProps<{
    programs: any; 
    filters: any;
}>();

const search = ref(props.filters?.search || '');



// Logic Hapus - Ganti id ke id_program
const deleteProgram = (id: number) => {
    if (confirm('Menghapus program akan menghapus semua paket di dalamnya. Lanjutkan?')) {
        router.delete(`/admin/program/${id}`);
    }
};

// Format Rupiah
const formatRupiah = (value: any) => {
    const number = typeof value === 'string' ? parseFloat(value) : value;
    return new Intl.NumberFormat('id-ID', { 
        style: 'currency', currency: 'IDR', minimumFractionDigits: 0 
    }).format(number || 0);
};
</script>

<template>
    <Head title="Manajemen Program" />

    <AppLayout>
        <div class="p-6 lg:p-8 transition-all">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Program Kursus</h1>
                    <p class="text-sm text-gray-500">Kelola kurikulum dan paket pelatihan LKP ELSAM.</p>
                </div>
                <Link 
                    href="/admin/program/create" 
                    class="flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-95 text-sm"
                >
                    <Plus class="w-5 h-5" />
                    Tambah Program Baru
                </Link>
            </div>

            <!-- Toolbar & Search -->
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 bg-white">
                    <div class="relative max-w-md group">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" />
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Cari nama program..." 
                            class="w-full pl-12 pr-4 py-3 bg-slate-50 border-transparent rounded-2xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all outline-none"
                        />
                    </div>
                </div>

                <!-- Table Data -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/50 text-slate-400 text-[11px] uppercase tracking-widest font-black">
                            <tr>
                                <th class="px-8 py-5">Info Program</th>
                                <th class="px-8 py-5">Pilihan Paket & Biaya</th>
                                <th class="px-8 py-5 text-center">Status</th>
                                <th class="px-8 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <!-- PERBAIKAN: Gunakan id_program sebagai key -->
                            <tr v-for="item in (programs?.data || [])" :key="item.id_program" class="hover:bg-indigo-50/10 transition group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 overflow-hidden shrink-0">
                                            <img v-if="item.gambar" :src="`/storage/${item.gambar}`" class="w-full h-full object-cover" />
                                            <BookOpen v-else class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">{{ item.nama_program }}</div>
                                            <div class="text-[11px] text-slate-400 italic line-clamp-1 mt-0.5">slug: {{ item.slug }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-2">
                                        <!-- PERBAIKAN: Gunakan id_paket sebagai key -->
                                        <div v-for="paket in (item.pakets || [])" :key="paket.id_paket" class="flex items-center gap-3">
                                            <div class="flex items-center gap-1.5 px-2.5 py-1 bg-white border border-slate-200 rounded-lg shadow-sm">
                                                <Layers class="w-3 h-3 text-indigo-500" />
                                                <span class="text-[11px] font-bold text-slate-700">{{ paket.nama_paket }}</span>
                                            </div>
                                            <span class="text-[11px] text-slate-400 font-medium">{{ paket.total_pertemuan }} Pertemuan</span>
                                            <span class="text-sm font-black text-slate-900">{{ formatRupiah(paket.harga) }}</span>
                                        </div>
                                        <div v-if="!item.pakets || item.pakets.length === 0" class="flex items-center gap-1 text-orange-400 italic text-xs">
                                            <AlertCircle class="w-3 h-3" /> Belum ada paket
                                        </div>
                                    </div>
                                </td>

                                <td class="px-8 py-6 text-center">
                                    <span :class="[
                                        'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border',
                                        item.status === 'Aktif' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100'
                                    ]">
                                        {{ item.status }}
                                    </span>
                                </td>

                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <!-- PERBAIKAN: Perbaiki penulisan URL Template Literal -->
                                        <Link :href="`/admin/program/${item.id_program}/edit`" class="p-2.5 text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                            <Pencil class="w-4 h-4" />
                                        </Link>
                                        <button @click="deleteProgram(item.id_program)" class="p-2.5 text-red-600 bg-red-50 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!programs?.data || programs.data.length === 0">
                                <td colspan="4" class="p-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <ImageIcon class="w-12 h-12 text-slate-200 mb-4" />
                                        <p class="text-slate-400 font-medium">Belum ada data program kursus.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-6 bg-slate-50/50 border-t border-gray-50 flex items-center justify-between">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                        Total: {{ programs?.total ?? 0 }} Program
                    </div>
                    <div class="flex gap-2">
                        <template v-for="(link, key) in (programs?.links || [])" :key="key">
                            <Link v-if="link.url" :href="link.url" 
                                v-html="link.label" 
                                :class="[
                                    'px-4 py-2 rounded-xl text-xs font-bold transition-all border',
                                    link.active ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg shadow-indigo-100' : 'bg-white border-slate-200 text-slate-600 hover:bg-indigo-50'
                                ]" 
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>