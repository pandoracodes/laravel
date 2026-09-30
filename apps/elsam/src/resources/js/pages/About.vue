<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Layout from '@/layouts/layout.vue';

/** 
 * 1. Logic Hero Slider
 */
const slides = [
    {
        image: '/image/hero-elsam 1.jpeg',
        title: 'Membangun Kompetensi, Mengukir Masa Depan.',
        subtitle: 'LKP ELSAM hadir sebagai mitra terpercaya untuk meningkatkan keahlian profesional Anda di era digital.'
    },
    {
        image: '/image/hero-elsam 2.jpeg',
        title: 'Instruktur Ahli & Kurikulum Industri.',
        subtitle: 'Belajar langsung dari praktisi berpengalaman dengan materi yang selalu diperbarui sesuai kebutuhan industri.'
    }
];

const currentIndex = ref(0);
let timer = null;
const nextSlide = () => { currentIndex.value = (currentIndex.value + 1) % slides.length; };

onMounted(() => { timer = setInterval(nextSlide, 5000); });
onUnmounted(() => { clearInterval(timer); });

const contactInfo = [
    { title: "Alamat", content: "Jl. Veteran No.8, Kebumen, Jawa Tengah", iconPath: "M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z" },
    { title: "Telepon", content: "+6281249899550", iconPath: "M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" },
    { title: "Email", content: "lkpelsam.kebumen@gmail.com", iconPath: "M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" },
    { title: "Jam Kerja", content: "08.00 - 16.30 WIB", iconPath: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" }
];
</script>

<template>
    <Head title="Tentang Kami - LKP ELSAM" />

    <Layout>
        <!-- SECTION 1: HERO SLIDER -->
        <section class="relative h-[500px] md:h-[600px] bg-slate-900 overflow-hidden">
            <div v-for="(slide, index) in slides" :key="index">
                <Transition 
                    enter-active-class="duration-1000 ease-out" enter-from-class="opacity-0 scale-105" enter-to-class="opacity-100 scale-100"
                    leave-active-class="duration-1000 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0"
                >
                    <div v-show="currentIndex === index" class="absolute inset-0">
                        <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/40 to-transparent z-10"></div>
                        <img :src="slide.image" class="w-full h-full object-cover" />
                        
                        <div class="absolute inset-0 z-20 flex items-center px-6 md:px-20">
                            <div class="max-w-3xl">
                                <span class="bg-blue-600 text-white px-4 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest mb-4 inline-block">Profil Lembaga</span>
                                <h1 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight">{{ slide.title }}</h1>
                                <p class="text-base md:text-lg text-slate-200 mb-8 max-w-xl leading-relaxed">{{ slide.subtitle }}</p>
                                <div class="flex gap-4">
                                    <Link href="/hubungi-kami" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-xl shadow-blue-600/30 flex items-center gap-2 text-sm md:text-base">
                                        Hubungi Kami
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 8l4 4m0 0l-4 4m4-4H3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </section>

        <!-- SECTION 2: TENTANG PERUSAHAAN -->
        <section class="py-16 md:py-24 bg-white">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                    <div class="relative">
                        <div class="absolute -top-6 -left-6 w-48 h-48 bg-blue-50 rounded-full blur-3xl opacity-60"></div>
                        <div class="relative bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100 flex items-center justify-center">
                            <img src="/image/Logo Elsam.png" alt="Logo LKP ELSAM" class="w-full h-auto object-contain max-w-[280px]" />
                        </div>
                    </div>
                    <div>
                        <span class="text-blue-600 font-bold uppercase tracking-[0.2em] text-sm mb-3 block">Tentang Kami</span>
                        <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-6 leading-tight">LKP ELSAM KEBUMEN</h2>
                        <div class="space-y-4 text-slate-600 text-base md:text-lg leading-relaxed">
                            <p>LKP ELSAM Kebumen merupakan lembaga pendidikan non formal yang bergerak dalam Bidang Operator Komputer, Mengemudi Mobil dan Teknik Otomotif Sepeda Motor. 
                                Lembaga ini mendapatkan izin penyelenggaraan kursus dari Dinas Tenaga Kerja dan Koperasi, Usaha Kecil dan Menengah Kabupaten Kebumen Nomor Izin Operasional : 563/014/KEP/2015, 
                                selain itu, LKP ELSAM Kebumen juga sudah memiliki nomor VIN (Vocational Training Identification Number) : 330520161</p>
                            <p>Sejak didirikan, kami telah mencetak ribuan lulusan yang kini sukses berkarier di berbagai industri. Dengan fasilitas modern dan instruktur praktisi, kami menjamin kualitas pembelajaran terbaik di Kebumen.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 3: VISI, MISI & TUJUAN -->
<section class="py-16 md:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-6">
        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-3xl md:text-4xl font-black text-slate-900 uppercase">Visi, Misi & Tujuan</h2>
        </div>

        <!-- Grid: 1 Kolom (Mobile), 2 Kolom (Tablet), 3 Kolom (Desktop) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-10">
            
            <!-- 1. Visi -->
            <div class="bg-white p-10 rounded-[2rem] shadow-sm border border-slate-200/60 group hover:border-blue-600 transition-all">
                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-200 group-hover:-translate-y-1 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-4">Visi</h3>
                <p class="text-slate-600 text-base md:text-lg leading-relaxed italic">
                    "Terampil, Profesional, Mandiri, dan Bermartabat"
                </p>
            </div>

            <!-- 2. Misi -->
            <div class="bg-white p-10 rounded-[2rem] shadow-md border border-slate-100 group hover:border-blue-600 transition-all">
                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg group-hover:-translate-y-1 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-4">Misi Utama</h3>
                <ul class="space-y-3 text-slate-600 text-base md:text-lg">
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 font-bold">•</span> Mendidik dan melatih peserta didik agar terampil dalam bidang mengemudi mobil, komputer, dan otomotif sepeda motor.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 font-bold">•</span> Mendidik dan melatih peserta agar profesional dan terstandar.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 font-bold">•</span> Mewujudkan SDM mandiri yang bermanfaat bagi diri sendiri dan lingkungan.
                    </li>
                </ul>
            </div>

            <!-- 3. Tujuan (TAMBAHAN) -->
            <!-- md:col-span-2 artinya di tablet dia akan memanjang memenuhi bawah, tapi di desktop (lg) dia balik jadi 1 kolom -->
            <div class="bg-white p-10 rounded-[2rem] shadow-sm border border-slate-200/60 group hover:border-blue-600 transition-all md:col-span-2 lg:col-span-1">
                <div class="w-14 h-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-orange-200 group-hover:-translate-y-1 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-4">Tujuan</h3>
                <ul class="space-y-3 text-slate-600 text-base md:text-lg">
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 font-bold">✓</span> Menjadi lembaga kursus dan pelatihan yang profesional terakreditasi dan menjadi idola masyarakat.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 font-bold">✓</span> Menjadi lembaga kursus dan pelatihan yang selalu berkembang dan memiliki integritas terhadap masyarakat.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 font-bold">✓</span> Mewujudkan SDM yang kompeten dibidang otomotif, komputer, dan mengemudi mobil.
                    </li>
                </ul>
            </div>

        </div>
    </div>
</section>

        <!-- SECTION 4: MENGAPA MEMILIH LKP ELSAM -->
        <section class="relative py-16 md:py-24 bg-gray-50 overflow-hidden">
            <div class="relative max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">
                    <div class="lg:col-span-4">
                        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight">
                            Mengapa <br class="hidden lg:block" /> memilih LKP <br class="hidden lg:block" /> ELSAM?
                        </h2>
                        <div class="h-1.5 w-16 bg-blue-600 mt-4 rounded-full"></div>
                    </div>

                    <div class="lg:col-span-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
                            <div v-for="i in 4" :key="i" class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white shadow-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900 mb-1">
                                        {{ ['Instruktur Berpengalaman', 'Modul Mudah Dipahami', 'Fasilitas Lengkap', 'Sertifikat Kompetensi'][i-1] }}
                                    </h4>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        {{ [
                                            'Pelatihan diberikan oleh tenaga profesional yang ahli di bidangnya dan memiliki pengalaman industri.',
                                            'Setiap modul disusun untuk pemula hingga lanjutan sehingga peserta dapat belajar secara bertahap.',
                                            'Didukung ruang kelas yang nyaman, peralatan praktik lengkap, dan lingkungan belajar kondusif.',
                                            'Peserta mendapatkan sertifikat yang diakui dan dapat digunakan untuk melamar pekerjaan.'
                                        ][i-1] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 md:py-24 bg-white text-center px-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl -ml-32 -mt-32"></div>
            <div class="relative z-10">
                <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4">Siap Memulai Karier Anda?</h2>
                <p class="text-slate-500 mb-10 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">Jangan tunda masa depan Anda. Bergabunglah dengan LKP ELSAM dan jadilah tenaga kerja profesional yang dicari perusahaan.</p>
                <Link href="/daftar" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-2xl font-black text-lg md:text-xl transition-all shadow-xl inline-block uppercase tracking-widest scale-100 hover:scale-105 active:scale-95">
                    Daftar Sekarang
                </Link>
            </div>
        </section>

        <!-- SECTION 5: HUBUNGI KAMI -->
        <section class="py-16 md:py-24 bg-slate-50 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12 md:mb-16">
                    <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4 uppercase">Informasi Kontak</h2>
                    <div class="h-1.5 w-20 bg-orange-500 mx-auto rounded-full"></div>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <div v-for="info in contactInfo" :key="info.title" class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm text-center hover:-translate-y-1 transition-all">
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-5">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" :d="info.iconPath" /></svg>
                        </div>
                        <h4 class="font-bold text-slate-900 mb-2 text-lg">{{ info.title }}</h4>
                        <p class="text-slate-500 text-sm leading-relaxed">{{ info.content }}</p>
                    </div>
                </div>

                <div class="w-full h-[400px] md:h-[500px] rounded-2xl overflow-hidden shadow-2xl border-4 md:border-8 border-white">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.1592171743587!2d109.65007127411849!3d-7.666025675866393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7aca07ee3695c1%3A0x13714daf29fb4892!2sGenius%20Course%20Kebumen!5e0!3m2!1sid!2sid!4v1778080141443!5m2!1sid!2sid"
                        class="w-full h-full" style="border:0;" allowfullscreen="true" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>

        
    </Layout>
</template>

<style scoped>
.v-enter-active, .v-leave-active { transition: opacity 1.2s ease-in-out; }
.v-enter-from, .v-leave-to { opacity: 0; }
</style>