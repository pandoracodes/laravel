<script setup>
import { Link } from '@inertiajs/vue3';
import { Facebook, Instagram } from 'lucide-vue-next';

const socialLinks = {
    facebook: 'https://www.facebook.com/LKP_ELSAM_OFFICIAL', 
    instagram: 'https://www.instagram.com/lkp_elsam?igsh=MWlyZjFzaHZyZW02bg==',        
}; 

const menus = {
    tentangKami: [
        { label: 'Profil Lembaga', url: '/tentang-kami' },
        { label: 'Visi & Misi', url: '/tentang-kami' },
    ],
    informasi: [
        { label: 'Informasi Pendaftaran', url: '#' },
        { label: 'Berita & Artikel', url: '/artikel' },
        { label: 'Galeri Kegiatan', url: '/galeri' },
        { label: 'Program Kursus', url: '/program-kursus' },
    ],
    hubungi: [
        { label: 'WhatsApp Admin', url: 'https://wa.me/6281249899550' },
        { label: 'Instagram', url: 'https://www.instagram.com/lkp_elsam?igsh=MWlyZjFzaHZyZW02bg==' },
        { label: 'TikTok', url: 'https://www.tiktok.com/@lkpelsamkebumen?_r=1&_t=ZS-968d4KfsolB' },
        { label: 'Google Maps', url: 'https://maps.app.goo.gl/xuarFUUwe9xwAvUL6' },
    ]
};
</script>

<template>
    <footer class="bg-[#3b6fa6] text-white pt-12 pb-6">
        <div class="container mx-auto px-6 lg:px-8 max-w-6xl">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1fr] gap-y-10 gap-x-8 mb-12">
                
                <!-- Kolom 1: LOGO & Deskripsi -->
                <div class="flex flex-col gap-4">
                    <!-- Logo Section -->
                    <Link href="/" class="inline-block group">
                        <!-- Jika Logo Anda Transparan, gunakan class ini -->
                        <img 
                            src="/image/Logo Elsam.png" 
                            alt="Logo LKP ELSAM" 
                            class="h-16 w-auto object-contain " 
                        />
                        <!-- 
                          Catatan: 
                          'brightness-0 invert' digunakan jika logo Anda aslinya berwarna gelap 
                          agar berubah menjadi putih di footer biru. 
                          Hapus class tersebut jika logo Anda sudah berwarna (berwarna putih/cerah).
                        -->
                    </Link>

                    <p class="text-sm text-blue-100 leading-relaxed max-w-xs mt-2">
                        LKP ELSAM: Pelatihan Berkualitas Untuk Karier Yang Lebih Baik. Terakreditasi dan terpercaya di Kebumen.
                    </p>
                    
                    <!-- Social Media -->
                    <div class="flex gap-3 mt-2">
                        <a 
                            :href="socialLinks.facebook" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="bg-white text-[#3b6fa6] p-2 rounded-full hover:bg-blue-50 transition-all flex items-center justify-center shadow-md hover:scale-110"
                            title="Kunjungi Facebook Kami"
                        >
                            <Facebook class="w-4 h-4" /> 
                        </a>
                        <a 
                            :href="socialLinks.instagram" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="bg-white text-[#3b6fa6] p-2 rounded-full hover:bg-blue-50 transition-all flex items-center justify-center shadow-md hover:scale-110"
                            title="Kunjungi Instagram Kami"
                        >
                            <Instagram class="w-4 h-4" />
                        </a>
                    </div>
                </div>

                <!-- Kolom 2: Tentang Kami -->
                <div>
                    <h3 class="font-bold text-lg mb-4 border-b-2 border-white/20 pb-2 inline-block">Tentang Kami</h3>
                    <ul class="flex flex-col gap-3 text-sm text-blue-100">
                        <li v-for="(item, index) in menus.tentangKami" :key="index">
                            <Link :href="item.url" class="hover:text-white hover:underline transition-all">
                                {{ item.label }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Kolom 3: Informasi -->
                <div>
                    <h3 class="font-bold text-lg mb-4 border-b-2 border-white/20 pb-2 inline-block">Layanan</h3>
                    <ul class="flex flex-col gap-3 text-sm text-blue-100">
                        <li v-for="(item, index) in menus.informasi" :key="index">
                            <Link :href="item.url" class="hover:text-white hover:underline transition-all">
                                {{ item.label }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <!-- Kolom 4: Hubungi -->
                <div>
                    <h3 class="font-bold text-lg mb-4 border-b-2 border-white/20 pb-2 inline-block">Hubungi</h3>
                    <ul class="flex flex-col gap-3 text-sm text-blue-100">
                        <li v-for="(item, index) in menus.hubungi" :key="index">
                            <a 
                                :href="item.url" 
                                target="_blank" 
                                rel="noopener noreferrer"
                                class="hover:text-white hover:underline transition-all"
                            >
                                {{ item.label }}
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Copyright -->
            <div class="pt-6 border-t border-white/10 text-xs text-blue-200 text-center md:text-left flex flex-col md:flex-row justify-between gap-4">
                <p>&copy; {{ new Date().getFullYear() }} LKP ELSAM KEBUMEN. All rights reserved.</p>
                <p class="opacity-70 italic text-[10px]">Membangun Kompetensi, Mengukir Masa Depan.</p>
            </div>
        </div>
    </footer>
</template>