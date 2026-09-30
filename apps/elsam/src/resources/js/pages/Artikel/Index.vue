<script setup>
import { ref, watch } from 'vue';
import Layout from '@/layouts/layout.vue';
import { router, Head, Link } from '@inertiajs/vue3';
import CardArtikel from '@/components/CardArtikel.vue'; 
import debounce from 'lodash/debounce';

const props = defineProps({
    artikels: Object,
    kategoris: Array,
    filters: {
        type: Object,
        default: () => ({ search: '', category: '' })
    }
});

const search = ref(props.filters?.search || '');
const activeCategory = ref(props.filters?.category || '');

// Fungsi Pencarian
watch([search, activeCategory], debounce(() => {
    router.get('/artikel', { 
        search: search.value, 
        category: activeCategory.value 
    }, {
        preserveState: true,
        replace: true,
        // Tambahkan ini agar tidak scroll ke atas otomatis saat ngetik
        preserveScroll: true 
    });
}, 500));

// Fungsi Search & Filter dengan Debounce agar tidak terlalu berat ke server
watch([search, activeCategory], debounce(() => {
    router.get('/artikel', { 
        search: search.value, 
        category: activeCategory.value 
    }, {
        preserveState: true,
        replace: true
    });
}, 500));

const setCategory = (slug) => {
    activeCategory.value = slug;
};
</script>

<template>
    <Layout>
    <Head title="Tips & Artikel Otomotif" />

    <div class="min-h-screen bg-gray-50">
        <!-- HERO SECTION (Mirip Gambar 1) -->
        <header class="bg-[#0f172a] pt-20 pb-32 px-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-blue-600/20 blur-[120px] rounded-full"></div>
            
            <div class="max-w-4xl mx-auto text-center relative z-10">
                <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-6">
                    Artikel & Tips
                </h1>
                <p class="text-blue-100 text-lg mb-10 max-w-2xl mx-auto">
                    Dapatkan tips terbaik untuk perawatan motor dan informasi terkini seputar dunia otomotif dari para ahli.
                </p>

                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto flex gap-2 p-2 bg-white rounded-2xl shadow-2xl">
                    <div class="flex-1 flex items-center px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Cari artikel atau tips..." 
                            class="w-full border-none focus:ring-0 text-gray-700 placeholder-gray-400 py-3"
                        />
                    </div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-500/30">
                        Cari Artikel
                    </button>
                </div>
            </div>
        </header>

        <div class="max-w-7xl mx-auto px-6 -mt-10 relative z-20">
            <div class="bg-[#1e293b] p-4 rounded-3xl shadow-xl border border-gray-700">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-white font-bold mr-2 ml-2 hidden md:block text-sm uppercase tracking-wider">Kategori Artikel</span>
                    
                    <button 
                        @click="setCategory('')"
                        :class="[activeCategory === '' ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/50' : 'bg-gray-800 text-gray-400 hover:bg-gray-700']"
                        class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300"
                    >
                        Semua Kategori
                    </button>

                    <button 
                        v-for="kat in kategoris" 
                        :key="kat.id_kategori"
                        @click="setCategory(kat.slug)"
                        :class="[activeCategory === kat.slug ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/50' : 'bg-gray-800 text-gray-400 hover:bg-gray-700']"
                        class="px-6 py-2.5 rounded-full text-sm font-semibold transition-all duration-300"
                    >
                        {{ kat.nama_kategori }}
                    </button>
                </div>
            </div>
        </div>

        <!-- ARTIKEL GRID -->
        <main class="max-w-7xl mx-auto px-6 py-20">
            <div v-if="artikels.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <CardArtikel 
                    v-for="artikel in artikels.data" 
                    :key="artikel.id_artikel" 
                    :artikel="artikel" 
                />
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20">
                <div class="bg-blue-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Artikel Tidak Ditemukan</h3>
                <p class="text-gray-500">Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
            </div>

            <!-- Pagination (Sederhana) -->
            <div class="mt-16 flex justify-center gap-2">
                <Link 
                    v-for="link in artikels.links" 
                    :key="link.label"
                    :href="link.url ?? '#'"
                    v-html="link.label"
                    class="px-4 py-2 rounded-lg border text-sm transition-colors"
                    :class="[
                        link.active ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50',
                        !link.url ? 'opacity-50 cursor-not-allowed' : ''
                    ]"
                />
            </div>
        </main>
    </div>
    </Layout>
</template>