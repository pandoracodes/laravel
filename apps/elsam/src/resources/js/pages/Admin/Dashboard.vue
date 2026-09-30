<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import debounce from 'lodash/debounce';
import { 
    Users, BookOpen, FileText, Bell, ArrowRight,
    Search, PencilLine, Trash2
} from 'lucide-vue-next';

const props = defineProps({
    totalProgram: Number,
    totalPeserta: Number,
    totalArtikel: Number,
    recentPeserta: Array as any,
    filters: Object as any,
});

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
    router.get('/admin/dashboard', { search: value }, { 
        preserveState: true, 
        replace: true 
    });
}, 500));

const confirmDelete = (id_peserta: number) => {
    if (confirm('Apakah Anda yakin ingin menghapus pendaftar ini?')) {
       
        router.delete(`/admin/peserta/${id_peserta}`, {
            preserveScroll: true,
             onSuccess: () => alert('Data berhasil dihapus')
        });
    }
};
</script>

<template>
    <Head title="Dashboard Admin" />

    <AppLayout>
        <div class="p-6 lg:p-8 transition-all"> 
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Ringkasan Statistik</h1>
                <p class="text-sm text-gray-500">Selamat datang kembali, berikut adalah performa LKP ELSAM.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <Link href="/admin/program" class="group bg-white border border-gray-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Total Program</p>
                            <h3 class="text-3xl font-extrabold text-gray-800">{{ totalProgram }}</h3>
                        </div>
                        <div class="p-3 rounded-xl bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                            <BookOpen class="w-8 h-8" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-blue-600 font-bold">
                        <span>Buka Data Program</span>
                        <ArrowRight class="w-3 h-3 ml-1" />
                    </div>
                </Link>

                <Link href="/admin/peserta" class="group bg-white border border-gray-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Total Peserta</p>
                            <h3 class="text-3xl font-extrabold text-gray-800">{{ totalPeserta }}</h3>
                        </div>
                        <div class="p-3 rounded-xl bg-green-50 text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors duration-300">
                            <Users class="w-8 h-8" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-green-600 font-bold">
                        <span>Buka Data Peserta</span>
                        <ArrowRight class="w-3 h-3 ml-1" />
                    </div>
                </Link>

                <Link href="/admin/artikel" class="group bg-white border border-gray-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Artikel Terbit</p>
                            <h3 class="text-3xl font-extrabold text-gray-800">{{ totalArtikel }}</h3>
                        </div>
                        <div class="p-3 rounded-xl bg-purple-50 text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors duration-300">
                            <FileText class="w-8 h-8" />
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs text-purple-600 font-bold">
                        <span>Buka Data Artikel</span>
                        <ArrowRight class="w-3 h-3 ml-1" />
                    </div>
                </Link>
            </div>

            <!-- Table Section -->
            <div class="w-full bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-white">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <Bell class="w-5 h-5 text-yellow-500" /> Pendaftar Terbaru
                    </h3>

                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <div class="relative w-full md:w-64 group">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors duration-200 pointer-events-none">
                                <Search class="h-4 w-4" />
                            </div>
                            <input 
                                v-model="search" 
                                type="text" 
                                placeholder="Cari pendaftar..." 
                                class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm transition-all duration-200 bg-slate-50 border border-slate-200 text-slate-700 placeholder:text-slate-400 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none" />
                        </div>
                                    
                        <Link href="/admin/peserta/create" 
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition-all duration-200 shadow-sm">
                            <Users class="size-5" />
                            <span>Tambah Peserta</span>
                        </Link>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 text-gray-400 text-[11px] uppercase tracking-widest font-bold">
                            <tr>
                                <th class="px-8 py-4">Nama Peserta</th>
                                <th class="px-8 py-4">Program & Paket</th>
                                <th class="px-8 py-4 text-center">Status</th>
                                <th class="px-8 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            
                            <tr v-for="peserta in recentPeserta" :key="peserta.id_peserta" class="hover:bg-indigo-50/20 transition group">
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900">{{ peserta.nama_peserta }}</span>
                                        <span class="text-[11px] text-gray-400">{{ peserta.no_pendaftaran }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <span class="text-xs font-bold text-gray-600 italic">
                                        {{ peserta.program_info }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span :class="[
                                        'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border',
                                        peserta.status_peserta === 'Aktif' ? 'bg-green-50 text-green-700 border-green-100' : 'bg-yellow-50 text-yellow-700 border-yellow-100'
                                    ]">
                                        {{ peserta.status_peserta }}
                                    </span>
                                </td>
                            
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        
                                        <Link :href="`/admin/peserta/${peserta.id_peserta}/edit`" class="p-2 text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-100 transition"><PencilLine class="w-4 h-4" /></Link>
                                        <button @click="confirmDelete(peserta.id_peserta)" class="p-2 text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition"><Trash2 class="w-4 h-4" /></button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="recentPeserta.length === 0">
                                <td colspan="4" class="p-16 text-center text-gray-400 italic text-sm">Belum ada pendaftar baru.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>