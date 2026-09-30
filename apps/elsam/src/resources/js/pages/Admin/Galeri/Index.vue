<script setup lang="ts">
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { 
    Search, 
    Plus, 
    Pencil, 
    Trash2,
    Image as ImageIcon, 
    Film, 
    Calendar, 
    MonitorPlay
} from 'lucide-vue-next';

interface GaleriItem {
    id_galeri: number; 
    judul: string;
    jenis_media: 'FOTO' | 'VIDEO'; 
    file_path: string; 
    tanggal_upload: string;
    created_at: string;
}


const props = defineProps({
    galeries: Object, 
    filters: Object,
});


const search = ref(props.filters?.search || '');
const deleteItem = (id: number) => {
    console.log("ID yang akan dihapus:", id); 
    if (!id) {
        alert("ID tidak ditemukan! Periksa nama kolom di database.");
        return;
    }
    if (confirm('Apakah Anda yakin ingin menghapus media ini? File akan terhapus permanen dari server.')) {
        router.delete(`/admin/galeri/${id}`, {
            onSuccess: () => {
                
            },
        });
    }
};


let timeout: ReturnType<typeof setTimeout>;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(('galeri.index'), { search: value }, { 
            preserveState: true, 
            replace: true 
        });
    }, 400);
});

</script>

<template>
    <Head title="Manajemen Galeri" />

    <AppLayout>
        <template #header>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Data Galeri
            </h2>
        </template>

        <!-- WRAPPER UTAMA (Ubah bg-gray-50 ke bg-white) -->
        <div class="py-12 bg-white min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- HEADER SECTION (Judul & Tombol) -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Manajemen Galeri</h1>
                        <p class="text-gray-500 mt-1">Kelola dokumentasi kegiatan terbaru LKP ELSAM.</p>
                    </div>

                    <Link href="/admin/galeri/create" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-md transition-all active:scale-95 whitespace-nowrap">
                        <Plus class="w-5 h-5" />
                        Tambah Dokumentasi
                    </Link>
                </div>
            
                <!-- CARD CONTAINER -->
                <div class="bg-white border border-gray-200 shadow-sm sm:rounded-xl overflow-hidden">
                    
                    <!-- TOOLBAR AREA -->
                    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-white">
                        
                        <!-- Search Bar Modern -->
                        <div class="relative w-full md:w-1/3">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <Search class="h-5 w-5 text-gray-400" />
                            </div>
                            <input 
                                v-model="search"
                                type="text"
                                placeholder="Cari judul galeri..."
                                class="pl-10 w-full border-gray-300 bg-gray-50 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 transition text-sm"
                            />
                        </div>
                    </div>

                    <!-- TABEL DATA -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Media & Judul</th>
                                    <th scope="col" class="px-6 py-4">Jenis Media</th>
                                    <th scope="col" class="px-6 py-4">Tanggal Upload</th>
                                    <th scope="col" class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="item in (galeries as any).data" :key="item.id_galeri" class="bg-white hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-start gap-4">
                                            <div class="h-16 w-16 rounded-lg bg-gray-100 border overflow-hidden shrink-0 flex items-center justify-center">
                                                <img 
                                                    v-if="item.jenis_media === 'FOTO'" 
                                                    :src="item.file_url" 
                                                    class="h-full w-full object-cover" 
                                                    alt="Thumbnail"
                                                />
                                                <div v-else class="text-gray-400 flex flex-col items-center">
                                                    <MonitorPlay class="w-8 h-8" />
                                                </div>
                                            </div>
                                            
                                            <div class="mt-1">
                                                <div class="text-base font-semibold text-gray-900 line-clamp-1">{{ item.judul }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ item.file_path?.split('/').pop() || 'tidak ada file' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span 
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border"
                                            :class="item.jenis_media === 'FOTO' 
                                                ? 'bg-green-50 text-green-700 border-green-100' 
                                                : 'bg-blue-50 text-blue-700 border-blue-100'"
                                        >
                                            <component :is="item.jenis_media === 'FOTO' ? ImageIcon : Film" class="w-3 h-3 mr-1.5" />
                                            {{ item.jenis_media }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2 text-gray-700">
                                            <Calendar class="w-4 h-4 text-gray-400" />
                                            <span>{{ item.tanggal_upload?.split(' ')[0] || '-' }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2">
                                            <Link 
                                                :href="`/admin/galeri/${item.id_galeri}/edit`" 
                                                class="p-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm group/btn"
                                            >
                                                <Pencil class="w-4 h-4" />
                                            </Link>

                                            <button 
                                                @click="deleteItem(item.id_galeri)" 
                                                class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm group/btn"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr v-if="!(galeries as any).data || (galeries as any).data.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada galeri ditemukan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION (Ubah bg-gray-50 ke bg-white) -->
                    <div class="px-6 py-4 border-t border-gray-100 bg-white flex items-center justify-between">
                        <div class="text-xs text-gray-500">
                            Menampilkan data 
                            <span class="font-medium text-gray-900">{{ (galeries as any).from ?? 0 }}</span> 
                            sampai 
                            <span class="font-medium text-gray-900">{{ (galeries as any).to ?? 0 }}</span>
                        </div>
                        <div class="flex gap-1">
                            <template v-for="(link, key) in (galeries as any).links" :key="key">
                                <div v-if="link.url === null" class="px-3 py-1.5 text-xs text-gray-400 border border-transparent rounded" v-html="link.label" />
                                <Link v-else 
                                    :href="link.url" 
                                    class="px-3 py-1.5 text-xs font-medium border rounded-md transition shadow-sm"
                                    :class="link.active ? 'bg-indigo-600 text-white ' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                                    v-html="link.label" 
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>