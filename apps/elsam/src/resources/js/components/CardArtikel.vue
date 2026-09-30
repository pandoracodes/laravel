<script setup>
import { Link } from '@inertiajs/vue3';

// Terima data artikel sebagai props
defineProps({
    artikel: Object
});
</script>

<template>
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-blue-100 hover:shadow-md transition-shadow duration-300 group">
        <!-- Bagian Gambar -->
        <div class="relative h-52 overflow-hidden bg-blue-50">
            <!-- Jika ada gambar tampilkan, jika tidak tampilkan placeholder biru -->
            <img v-if="artikel.gambar" :src="'/storage/' + artikel.gambar" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
            <div v-else class="w-full h-full flex items-center justify-center text-blue-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>

            <!-- Badge Kategori (Biru Muda) -->
            <div class="absolute top-4 left-4">
                <span class="px-3 py-1 bg-blue-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm">
                    {{ artikel.kategori?.nama_kategori || 'Umum' }}
                </span>
            </div>
        </div>

        <!-- Bagian Konten -->
        <div class="p-6">
            <div class="flex items-center text-blue-400 text-xs mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002-2z" />
                </svg>
                {{ artikel.tanggal_publish }}
            </div>

            <h3 class="text-lg font-bold text-gray-800 leading-tight mb-3 group-hover:text-blue-600 transition-colors">
                {{ artikel.judul_artikel }}
            </h3>

            <!-- Ringkasan Isi Artikel -->
            <p class="text-gray-500 text-sm line-clamp-2 mb-6">
                {{ artikel.isi_artikel.replace(/<[^>]*>?/gm, '') }}
            </p>

            <!-- Link Baca Selengkapnya -->
            <Link :href="'/artikel/' + artikel.slug" 
                class="inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                Baca Selengkapnya
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </Link>
        </div>
    </div>
</template>