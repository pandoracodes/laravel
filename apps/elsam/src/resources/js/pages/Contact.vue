<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/layout.vue'; 

const contactInfo = {
    alamat: "Jl. Veteran No.8, Kebumen, Bumirejo, Kec.Kebumen, Kabupaten Kebumen, Jawa Tengah 54316",
    telepon: "+6281249899550",
    email: "lkpelsam.kebumen@gmail.com",
    jam: "Senin - Minggu: 08.00 - 16.30 WIB"
};
</script>

<template>
    <Head title="Hubungi Kami" />

    <AppLayout>
        <!-- HERO SECTION -->
        <div class="relative h-[450px] w-full flex items-center justify-center overflow-hidden">
            <!-- Background Image -->
            <!-- Ganti '/image/hero-kontak.jpg' dengan path gambar asli kamu di folder public -->
            <img 
                src="/image/hero-elsam 3.png"
                alt="Background Hubungi Kami" 
                class="absolute inset-0 w-full h-full object-cover"
            />
            
            <!-- Dark Overlay (Agar teks mudah dibaca) -->
            <div class="absolute inset-0 bg-black/60"></div>

            <!-- Hero Content -->
            <div class="relative z-10 text-center px-4">
                <h1 class="text-5xl md:text-6xl font-bold text-white mb-4 tracking-tight">
                    Hubungi Kami
                </h1>
                <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto leading-relaxed">
                    Kami siap membantu menjawab pertanyaan dan melayani pendaftaran Anda.
                </p>
                <div class="w-20 h-1.5 bg-orange-500 mx-auto mt-6 rounded-full"></div>
            </div>
        </div>

        <!-- CONTENT SECTION (Cards & Map) -->
        <div class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4">
                
                <!-- Grid Card Informasi -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-20">
                    <!-- Alamat -->
                    <div class="p-10 border border-gray-100 rounded-[2rem] shadow-sm hover:shadow-lg transition-all duration-300 bg-white group">
                        <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-xl text-gray-800 mb-3 text-center">Alamat</h3>
                        <p class="text-sm text-gray-500 leading-relaxed text-center">{{ contactInfo.alamat }}</p>
                    </div>

                    <!-- Telepon -->
                    <div class="p-10 border border-gray-100 rounded-[2rem] shadow-sm hover:shadow-lg transition-all duration-300 bg-white group">
                        <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-xl text-gray-800 mb-3 text-center">Telepon</h3>
                        <p class="text-sm text-gray-500 text-center">{{ contactInfo.telepon }}</p>
                    </div>

                    <!-- Email -->
                    <div class="p-10 border border-gray-100 rounded-[2rem] shadow-sm hover:shadow-lg transition-all duration-300 bg-white group">
                        <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-xl text-gray-800 mb-3 text-center">Email</h3>
                        <p class="text-sm text-gray-500 text-center">{{ contactInfo.email }}</p>
                    </div>

                    <!-- Jam Operasional -->
                    <div class="p-10 border border-gray-100 rounded-[2rem] shadow-sm hover:shadow-lg transition-all duration-300 bg-white group">
                        <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-xl text-gray-800 mb-3 text-center">Jam Operasional</h3>
                        <p class="text-sm text-gray-500 text-center">{{ contactInfo.jam }}</p>
                    </div>
                </div>

                <!-- Google Maps -->
                <div class="rounded-3xl overflow-hidden shadow-2xl h-[500px] border-8 border-white">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.1592171743587!2d109.65007127411849!3d-7.666025675866393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7aca07ee3695c1%3A0x13714daf29fb4892!2sGenius%20Course%20Kebumen!5e0!3m2!1id!2sid!4v1778080141443!5m2!1sid!2sid" 
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </AppLayout>
</template>