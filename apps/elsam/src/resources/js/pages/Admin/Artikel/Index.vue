<script setup>
import { ref, watch } from 'vue';
import { Link, router, Head } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AppLayout from '@/layouts/AppLayout.vue'; // Layout utama kamu

const props = defineProps({
    artikels: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

// Fungsi pencarian
watch(search, debounce((value) => {
    router.get('/admin/artikel', { search: value }, {
        preserveState: true,
        replace: true
    });
}, 500));

// Fungsi hapus
const deleteArtikel = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
        router.delete('/admin/artikel/' + id);
    }
};
</script>

<template>
    <!-- Head untuk judul tab browser -->
    <Head title="Manajemen Artikel" />

    <!-- Bungkus semua dengan AppLayout agar Sidebar muncul -->
    <AppLayout>
        <div class="p-6">
            <!-- Header Section -->
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Manajemen Artikel</h1>
                    <p class="text-gray-500 mt-1">Kelola berita, tips, dan publikasi terbaru LKP ELSAM.</p>
                </div>
                
                <Link href="/admin/artikel/create" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center transition shadow-lg shadow-indigo-200">
                    <span class="mr-2 text-xl">+</span> Tambah Artikel Baru
                </Link>
            </div>

            <!-- Main Card (Tabel) -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Search Bar Section -->
                <div class="p-6 border-b border-gray-50">
                    <div class="relative max-w-md">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input v-model="search" type="text" placeholder="Cari judul artikel..." 
                            class="block w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 focus:bg-white transition text-sm">
                    </div>
                </div>

                <!-- Table Section -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-400 uppercase bg-white border-b border-gray-50">
                            <tr>
                                <th class="px-6 py-5 font-semibold">Info Artikel</th>
                                <th class="px-6 py-5 font-semibold">Penulis</th>
                                <th class="px-6 py-5 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="artikel in artikels.data" :key="artikel.id_artikel" class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-5">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 2v4a2 2 0 002 2h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-base">{{ artikel.judul_artikel }}</div>
                                            <div class="text-indigo-400 text-xs italic mt-0.5">slug: {{ artikel.slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-gray-700">
                                    {{ artikel.penulis }}
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <Link :href="'/admin/artikel/' + artikel.id_artikel + '/edit'" 
                                            class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </Link>
                                        <button @click="deleteArtikel(artikel.id_artikel)" 
                                            class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Empty State jika data kosong -->
                            <tr v-if="artikels.data.length === 0">
                                <td colspan="3" class="px-6 py-10 text-center text-gray-400">Belum ada artikel.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Section -->
                <div class="px-6 py-5 bg-gray-50/50 flex justify-between items-center border-t border-gray-50">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                        Total: {{ artikels.total }} Artikel
                    </p>
                    <nav class="flex space-x-1">
                        <template v-for="(link, k) in artikels.links" :key="k">
                            <Link 
                                v-if="link.url" 
                                :href="link.url" 
                                v-html="link.label"
                                class="px-4 py-2 text-sm rounded-xl transition font-semibold"
                                :class="link.active ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-indigo-600 hover:bg-indigo-50 border border-gray-100'"
                            />
                        </template>
                    </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>