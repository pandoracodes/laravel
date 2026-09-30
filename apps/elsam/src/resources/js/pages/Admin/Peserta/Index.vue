<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import debounce from 'lodash/debounce';
import { 
    Plus, 
    FileSpreadsheet, 
    Search, 
    Trash2, 
    PencilLine,
    Eye,
    Globe,
    UserCheck
} from 'lucide-vue-next';

const props = defineProps({
    peserta: Object,
    filters: Object,
});

// --- LOGIC SEARCH & FILTER ---
const search = ref(props.filters.search || '');
const filterMonth = ref(props.filters.month || '');
const filterYear = ref(props.filters.year || new Date().getFullYear());

const updateFilter = debounce(() => {
    router.get('/admin/peserta', { 
        search: search.value, 
        month: filterMonth.value, 
        year: filterYear.value 
    }, { 
        preserveState: true, 
        replace: true 
    });
}, 500);

watch([search, filterMonth, filterYear], () => {
    updateFilter();
});

const months = [
    { id: 1, name: 'Januari' }, { id: 2, name: 'Februari' }, { id: 3, name: 'Maret' },
    { id: 4, name: 'April' }, { id: 5, name: 'Mei' }, { id: 6, name: 'Juni' },
    { id: 7, name: 'Juli' }, { id: 8, name: 'Agustus' }, { id: 9, name: 'September' },
    { id: 10, name: 'Oktober' }, { id: 11, name: 'November' }, { id: 12, name: 'Desember' },
];
const years = Array.from({ length: 5 }, (_, i) => new Date().getFullYear() - i);

const confirmDelete = (id_peserta) => {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        router.delete(`/admin/peserta/${id_peserta}`, { 
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Data Peserta" />

    <AppLayout>
        <div class="w-full p-6 lg:p-8 font-sans">
            
            <!-- Header Section -->
            <div class="mb-8 flex justify-between items-end">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Peserta</h1>
                    <p class="text-sm text-slate-500 mt-1">Manajemen informasi seluruh peserta kursus LKP ELSAM.</p>
                </div>
                <Link href="/admin/peserta/create" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-100 transition-all active:scale-95">
                    <Plus class="w-4 h-4" />
                    Tambah Peserta
                </Link>
            </div>

            <!-- Filter & Search Bar -->
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4 mb-6">
                <div class="relative w-full lg:w-[450px]">
                    <Search class="absolute left-3 top-3 h-4 w-4 text-slate-400" />
                    <input v-model="search" type="text" placeholder="Cari nama, email, atau no daftar..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all outline-none" />
                </div>

                <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                    <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-xl border border-slate-200">
                        <select v-model="filterMonth" class="bg-transparent border-none text-xs font-bold focus:ring-0 cursor-pointer text-slate-600 pr-8">
                            <option value="">Semua Bulan</option>
                            <option v-for="m in months" :key="m.id" :value="m.id">{{ m.name }}</option>
                        </select>
                        <div class="w-px h-4 bg-slate-300"></div>
                        <select v-model="filterYear" class="bg-transparent border-none text-xs font-bold focus:ring-0 cursor-pointer text-slate-600 pr-8">
                            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
                        </select>
                        
                        <a :href="`/admin/peserta/export/excel?month=${filterMonth}&year=${filterYear}&search=${search}`" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold rounded-lg shadow-sm transition-colors">
                            <FileSpreadsheet class="w-3.5 h-3.5" />
                            Export
                        </a>
                    </div>
                </div>
            </div>

            <!-- TABEL DATA -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest">No. Daftar</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Peserta</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-center">Sumber</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest">Program</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-center">Status</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-slate-500 uppercase tracking-widest text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in peserta.data" :key="item.id_peserta" class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg border border-indigo-100">
                                        {{ item.no_pendaftaran }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-800">{{ item.nama_peserta }}</span>
                                        <span class="text-[11px] text-slate-400 font-medium">{{ item.email }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center">
                                        <span v-if="item.input_via === 'user'" 
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-extrabold rounded-md border border-emerald-100 uppercase">
                                            <Globe class="w-3 h-3" /> Website
                                        </span>
                                        <span v-else 
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-600 text-[10px] font-extrabold rounded-md border border-amber-100 uppercase">
                                            <UserCheck class="w-3 h-3" /> Admin
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">
                                            {{ item.paket?.program?.nama_program || 'N/A' }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-medium italic">
                                            {{ item.paket?.nama_paket || '-' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="[
                                        'px-3 py-1 rounded-full text-[10px] font-bold uppercase border',
                                        item.status_peserta === 'Aktif' ? 'bg-green-50 text-green-700 border-green-200' : 
                                        item.status_peserta === 'Lulus' ? 'bg-blue-50 text-blue-700 border-blue-200' :
                                        'bg-yellow-50 text-yellow-700 border-yellow-200'
                                    ]">
                                        {{ item.status_peserta }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <Link :href="`/admin/peserta/${item.id_peserta}`" 
                                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all" title="Detail">
                                            <Eye class="w-4.5 h-4.5" />
                                        </Link>

                                        <Link :href="`/admin/peserta/${item.id_peserta}/edit`" 
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all" title="Edit">
                                            <PencilLine class="w-4.5 h-4.5" />
                                        </Link>

                                        <button @click="confirmDelete(item.id_peserta)" 
                                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all" title="Hapus">
                                            <Trash2 class="w-4.5 h-4.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Empty State -->
                            <tr v-if="peserta.data.length === 0">
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <Search class="w-10 h-10 text-slate-200 mb-3" />
                                        <p class="text-slate-400 text-sm font-medium">Tidak ada data peserta ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination (Jika kamu sudah ada komponen pagination, panggil di sini) -->
        </div>
    </AppLayout>
</template>