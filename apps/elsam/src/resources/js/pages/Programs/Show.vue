<script setup lang="ts">
import Layout from '@/layouts/layout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    program: any;
    back_url: string;
    register_url: string;
}>();

// Helper untuk format harga (opsional jika data price belum terformat)
const formatPrice = (price: any) => {
    if (isNaN(price)) return price;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price);
};
</script>

<template>
    <Head :title="program.name" />
    <Layout>
        <!-- Header / Navigation -->
        <div class="bg-white border-b border-gray-100 sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
                <Link :href="back_url" class="flex items-center text-sm font-semibold text-gray-500 hover:text-blue-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </Link>
                <div class="hidden md:block text-sm font-medium text-gray-400">
                    Program Kursus / <span class="text-gray-900">{{ program.name }}</span>
                </div>
            </div>
        </div>

        <!-- Section 1: Detail Penjelasan (Atas) -->
        <main class="bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 py-12 md:py-20">
                <div class="grid lg:grid-cols-12 gap-12 items-start">
                    
                    <!-- Kiri: Gambar dengan Aksen -->
                    <div class="lg:col-span-5">
                        <div class="relative">
                            <div class="absolute -top-4 -left-4 w-24 h-24 bg-blue-50 rounded-full z-0"></div>
                            <div class="relative z-10 overflow-hidden rounded-[2rem] shadow-2xl shadow-blue-100 border-8 border-white">
                                <img :src="program.image" :alt="program.name" class="w-full h-[400px] object-cover hover:scale-105 transition duration-700">
                            </div>
                            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-orange-50 rounded-full z-0"></div>
                        </div>
                    </div>

                    <!-- Kanan: Penjelasan -->
                    <div class="lg:col-span-7 lg:pl-8">
                        <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-bold uppercase tracking-wider mb-6">
                            Program Unggulan
                        </div>
                        <h1 class="text-4xl md:text-5xl font-black text-gray-900 leading-tight mb-6">
                            {{ program.name }}
                        </h1>
                        
                        <div class="prose prose-lg prose-slate max-w-none text-gray-600 mb-10 leading-relaxed">
                            <div v-html="program.description"></div>
                        </div>

                        <div class="flex flex-wrap gap-4">
                            <Link :href="register_url" class="inline-flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white px-10 py-4 rounded-2xl font-bold transition-all shadow-lg shadow-orange-200 hover:-translate-y-1">
                                Daftar Sekarang
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </Link>
                            
                            <a href="#paket" class="inline-flex items-center justify-center bg-white border-2 border-gray-100 hover:border-blue-600 text-gray-600 hover:text-blue-600 px-8 py-4 rounded-2xl font-bold transition-all">
                                Lihat Pilihan Paket
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Section 2: List Paket (Bawah) -->
        <section id="paket" class="bg-gray-50 py-24 border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4">
                <div class="max-w-2xl mb-16">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-4 tracking-tight">Pilihan Paket Tersedia</h2>
                    <p class="text-lg text-gray-500 leading-relaxed">Pilih intensitas belajar yang sesuai dengan kebutuhan dan ketersediaan waktu Anda.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="paket in program.pakets" :key="paket.id" 
                         class="group relative bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 hover:border-blue-500 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-100/50 flex flex-col">
                        
                        <!-- Badge kecil -->
                        <div class="flex justify-between items-start mb-6">
                            <div class="bg-blue-50 text-blue-700 px-4 py-1 rounded-full text-xs font-bold uppercase tracking-widest">
                                LKP ELSAM
                            </div>
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">{{ paket.name }}</h3>
                        
                        <div class="flex items-center text-gray-500 text-sm mb-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ paket.meetings }} Sesi Pertemuan
                        </div>
                         <div class="text-gray-500 text-sm leading-relaxed mb-8">
                            {{ paket.description }}
                        </div>
                        
                        <div class="mt-auto pt-8 border-t border-dashed border-gray-100">
                            <div class="text-gray-400 text-xs font-bold uppercase mb-1">Biaya Kursus</div>
                            <div class="text-3xl font-black text-gray-900 mb-8">
                                {{ formatPrice(paket.price) }}
                            </div>

                            <Link :href="register_url + '?id_paket=' + paket.id_paket" 
                                  class="flex items-center justify-center w-full py-4 bg-gray-50 group-hover:bg-blue-600 text-gray-600 group-hover:text-white rounded-2xl font-bold transition-all duration-300 overflow-hidden relative">
                                <span class="relative z-10">Pilih Paket Ini</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </Layout>
</template>

<style scoped>
/* Haluskan rendering font */
.prose {
    font-size: 1.1rem;
    color: #4a5568;
}
</style>