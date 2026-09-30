<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Course {
    id: number;
    name: string;
    image: string;
    slug: string;
    short_description: string;
    features: string[];
}

defineProps<{
    course: Course;
}>();
</script>

<!-- Di dalam CardKursus.vue -->
<template>
  <div class="bg-white rounded-3xl shadow-lg p-6 flex flex-col h-full">
    <!-- Bagian Gambar -->
    <img :src="course.image" class="rounded-2xl h-48 w-full object-cover mb-4">

    <!-- Judul -->
    <div class="flex justify-between items-start mb-2">
      <h3 class="text-xl font-bold text-blue-900">{{ course.name }}</h3>
      <div class="flex text-orange-400">★★★★★</div>
    </div>

    <!-- TAMBAHKAN DESKRIPSI DI SINI -->
    <p class="text-gray-500 text-sm leading-relaxed mb-6 line-clamp-3">
      {{ course.short_description }}
    </p>

    <!-- Tombol (Pastikan pakai Link ke slug) -->
    <Link :href="'/program-kursus/' + course.slug" 
          class="mt-auto w-full bg-[#4475be] hover:bg-blue-700 text-white text-center py-3 rounded-xl font-bold transition">
      Lihat Paket
    </Link>
  </div>
</template>

