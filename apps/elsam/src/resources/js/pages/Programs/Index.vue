<script setup lang="ts">
import Layout from '@/layouts/layout.vue';
import { Head } from '@inertiajs/vue3';
import CourseCard from '@/components/CardKursus.vue';

defineProps<{
    programs: Array<any>
}>();
</script>

<template>
    <Head title="Program Kursus" />
    <Layout>
        
        <div class="relative py-40 md:py-52 px-4 overflow-hidden flex items-center justify-center min-h-[450px] md:min-h-[550px]">
            
            <!-- Foto Background -->
            <img 
                src="image/hero-elsam 3.png"
                alt="Background Kursus"
                class="absolute inset-0 w-full h-full object-cover"
            />
            
            <!-- Overlay Gelap (Disamakan dengan tone biru Artikel) -->
            <!-- Opacity diturunkan sedikit ke 70% (bg-[#0a162b]/70) agar foto lebih terlihat jelas -->
            <div class="absolute inset-0 bg-[#0a162b]/75"></div>

            <!-- Konten Teks -->
            <div class="relative z-10 max-w-7xl mx-auto text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 tracking-tight drop-shadow-lg">
                    Program Kursus Kami
                </h1>
                <p class="text-blue-50 text-lg md:text-2xl max-w-3xl mx-auto leading-relaxed drop-shadow-md">
                    Pilih program yang sesuai dengan minat dan kebutuhan karier Anda. 
                    Dapatkan pelatihan intensif dengan kurikulum standar industri.
                </p>
                
                <!-- Opsional: Garis dekorasi bawah agar manis -->
                <div class="mt-8 h-1.5 w-24 bg-orange-500 mx-auto rounded-full shadow-lg"></div>
            </div>
        </div>

        <!-- Section List Kursus -->
        <div class="bg-gray-50 min-h-screen py-16 md:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div v-if="programs.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <CourseCard 
                        v-for="item in programs" 
                        :key="item.id" 
                        :course="item" 
                    />
                </div>
                
                <div v-else class="text-center py-20">
                    <p class="text-gray-500 text-lg">Belum ada program kursus yang tersedia saat ini.</p>
                </div>

            </div>
        </div>
    </Layout>
</template>