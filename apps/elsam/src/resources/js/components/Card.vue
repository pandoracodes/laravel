<script setup>
import { defineProps } from 'vue';

// Mendefinisikan props agar data bisa diisi dari luar komponen
const props = defineProps({
    image: {
        type: String,
        required: true,
        default: 'https://via.placeholder.com/400x250'
    },
    badge: {
        type: String,
        default: null // Kosongkan jika tidak ada badge "Terpopuler"
    },
    category: {
        type: String,
        required: true
    },
    duration: {
        type: String,
        required: true
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        required: true
    },
    features: {
        type: Array,
        default: () => []
    },
    buttonText: {
        type: String,
        default: 'Daftar Sekarang'
    }
});
</script>

<template>
    <!-- Container Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 max-w-sm border border-gray-100 flex flex-col h-full">
        
        <!-- Bagian Gambar -->
        <div class="relative">
            <img :src="image" :alt="title" class="w-full h-48 object-cover object-center">
            
            <!-- Badge Terpopuler (Opsional) -->
            <div v-if="badge" class="absolute top-4 right-4 bg-yellow-500 text-white text-xs font-bold px-3 py-1.5 rounded-md shadow-sm uppercase tracking-wide">
                {{ badge }}
            </div>
        </div>

        <!-- Bagian Konten -->
        <div class="p-5 flex flex-col flex-grow">
            <!-- Header: Kategori & Durasi -->
            <div class="flex justify-between items-center mb-3">
                <span class="bg-blue-100 text-blue-600 text-xs font-semibold px-2.5 py-1 rounded-md">
                    {{ category }}
                </span>
                <div class="flex items-center text-gray-400 text-xs font-medium">
                    <!-- Icon Jam SVG -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ duration }}
                </div>
            </div>

            <!-- Judul -->
            <h3 class="text-gray-800 text-lg font-bold mb-2 leading-tight">
                {{ title }}
            </h3>

            <!-- Deskripsi -->
            <p class="text-gray-500 text-sm mb-4 leading-relaxed line-clamp-3">
                {{ description }}
            </p>

            <!-- Fitur List (Checklist) -->
            <ul class="space-y-2 mb-6 flex-grow">
                <li v-for="(item, index) in features" :key="index" class="flex items-start text-sm text-gray-600">
                    <!-- Icon Centang Hijau SVG -->
                    <svg class="h-5 w-5 text-green-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ item }}</span>
                </li>
            </ul>

            <!-- Tombol -->
            <button class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-300 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-opacity-50 mt-auto">
                {{ buttonText }}
            </button>
        </div>
    </div>
</template>