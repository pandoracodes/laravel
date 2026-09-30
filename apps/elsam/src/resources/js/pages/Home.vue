<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'; // Tambahkan ref, onMounted, onUnmounted
import Layout from '@/layouts/layout.vue';
import { Head, Link } from '@inertiajs/vue3';
import CourseCard from '@/components/CardKursus.vue';
import ContactCard from '@/components/ContactCard.vue';
import CardArtikel from '@/components/CardArtikel.vue';
import CardGaleri from '@/components/CardGaleri.vue';
// Import Icon untuk Lightbox
import { X, ChevronLeft, ChevronRight, Calendar } from 'lucide-vue-next';

interface Props {
    courses: Array<any>;
    articles: Array<any>;
    galleries: Array<any>;
    canRegister?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    canRegister: true,
});

// --- LOGIKA LIGHTBOX (Sama dengan Index Galeri) ---
const selectedGallery = ref<any>(null);
const selectedIndex = ref<number>(-1);

const openLightbox = (item: any, index: number) => {
    selectedGallery.value = item;
    selectedIndex.value = index;
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    selectedGallery.value = null;
    selectedIndex.value = -1;
    document.body.style.overflow = 'auto';
};

const nextLightbox = () => {
    if (selectedIndex.value < props.galleries.length - 1) {
        selectedIndex.value++;
        selectedGallery.value = props.galleries[selectedIndex.value];
    }
};

const prevLightbox = () => {
    if (selectedIndex.value > 0) {
        selectedIndex.value--;
        selectedGallery.value = props.galleries[selectedIndex.value];
    }
};

// Fungsi Format Tanggal agar tidak muncul "Baru-baru ini" jika ada datanya
const formatDate = (dateString: string) => {
    if (!dateString) return 'Baru-baru ini';
    try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).format(date);
    } catch (e) {
        return dateString;
    }
};

const handleKeyDown = (e: KeyboardEvent) => {
    if (!selectedGallery.value) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextLightbox();
    if (e.key === 'ArrowLeft') prevLightbox();
};

onMounted(() => window.addEventListener('keydown', handleKeyDown));
onUnmounted(() => window.removeEventListener('keydown', handleKeyDown));

const contactInfo = [
    { title: "Alamat", content: "Jl. Veteran No.8, Kebumen, Bumirejo, Kec.Kebumen, Kabupaten Kebumen, Jawa Tengah 54316", icon: "location" as const },
    { title: "Telepon", content: "+6281249899550", icon: "phone" as const },
    { title: "Email", content: "lkpelsam.kebumen@gmail.com", icon: "email" as const },
    { title: "Jam Operasional", content: "Senin - Minggu: 08.00 - 16.30 WIB", icon: "clock" as const }
];
</script>

<template>
    <Head title="" />
    <Layout>

        <!-- 1. HERO SECTION -->
        <div class="relative w-full h-screen overflow-hidden">
            <img src="/image/hero-elsam 3.png"
                class="absolute inset-0 z-0 h-full w-full object-cover" loading="eager" fetchpriority="high" />
            <div class="absolute inset-0 bg-black/60 z-0"></div>
            <div class="relative z-10 flex flex-col items-center justify-center h-full px-4 text-center text-white">
                <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight leading-tight mb-6 drop-shadow-lg">
                    Pelatihan Berkualitas Untuk <br class="hidden md:block" />
                    Karier Yang Lebih Baik.
                </h1>
                <p class="text-lg md:text-xl text-gray-200 max-w-2xl font-light drop-shadow-md">
                    Tingkatkan keterampilan Anda bersama LKP Elsam melalui pelatihan terstruktur dan instruktur profesional
                </p>
            </div>
        </div>

        <!-- 2. FLOATING BLUE BOX -->
        <div class="relative z-20 px-4 sm:px-6 lg:px-8 -mt-20 md:-mt-16">
            <div class="max-w-6xl mx-auto bg-[#3b6db3] rounded-xl shadow-2xl overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3">
                    <div class="p-8 md:p-12 lg:col-span-2 text-white flex flex-col justify-center">
                        <h2 class="text-xl md:text-2xl font-bold leading-normal mb-6">
                            Pelajari bagaimana LKP Elsam membantu mengembangkan kompetensi, membentuk karakter, dan mempersiapkan Anda menghadapi dunia kerja
                        </h2>
                        <div>
                            <Link href ='/tentang-kami'class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold py-3 px-8 rounded-lg transition duration-300 shadow-md transform hover:scale-105">
                                Cari Tahu Tentang Kami
                            </Link>
                        </div>
                    </div>
                    <div class="bg-[#34609e] p-8 md:p-12 flex flex-col justify-center items-center text-white border-t lg:border-t-0 lg:border-l border-white/10">
                        <div class="text-center mb-8 last:mb-0">
                            <span class="block text-4xl md:text-5xl font-extrabold mb-1">200+</span>
                            <span class="text-sm uppercase tracking-widest opacity-80">Peserta Lulusan</span>
                        </div>
                        <div class="text-center">
                            <span class="block text-4xl md:text-5xl font-extrabold mb-1">3</span>
                            <span class="text-sm uppercase tracking-widest opacity-80">Program Kursus</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. MENGAPA MEMILIH LKP ELSAM -->
        <section class="relative py-20 md:py-32 bg-gray-50 overflow-hidden">
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
                    <div class="lg:col-span-4">
                        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight">
                            Mengapa <br class="hidden lg:block" /> memilih LKP <br class="hidden lg:block" /> ELSAM?
                        </h2>
                        <div class="h-1.5 w-20 bg-blue-600 mt-6 rounded-full"></div>
                    </div>

                    <div class="lg:col-span-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-12">
                            <div v-for="i in 4" :key="i" class="flex items-start space-x-5">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white shadow-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900 mb-2">
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

       <!-- 4. PROGRAM KURSUS -->
        <section class="py-20 md:py-32 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Program Kursus Unggulan</h2>
            <div class="h-1.5 w-20 bg-orange-500 mx-auto rounded-full"></div>
            <p class="mt-6 text-gray-600 max-w-2xl mx-auto text-lg">
                Investasikan waktu Anda untuk masa depan yang lebih cerah bersama LKP ELSAM.
            </p>
        </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <CourseCard v-for="item in courses" :key="item.id" :course="item" />
                </div>
            </div>
            </div>
        </section>

       <section class="py-20 md:py-32 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Galeri Kegiatan</h2>
                    <div class="h-1.5 w-20 bg-orange-500 mx-auto rounded-full"></div>
                    <p class="mt-6 text-gray-600 max-w-2xl mx-auto text-lg">
                    Momen berharga dan dokumentasi seru dari setiap aktivitas pelatihan bersama LKP ELSAM.</p>

                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <CardGaleri
                        v-for="(item, index) in galleries"
                        :key="item.id_galeri"
                        :id="item.id_galeri"
                        :image="item.file_url"
                        :title="item.judul"
                        :category="item.jenis_media"
                        class="cursor-pointer"
                        @click="openLightbox(item, index)"
                    />
                </div>

                <div class="mt-12 text-center">
                    <Link href="/galeri" class="text-[#3b6db3] font-bold hover:text-blue-700 underline">Lihat Seluruh Dokumentasi</Link>
                </div>
            </div>
        </section>


        <!-- 5. ARTIKEL / BLOG TERBARU -->
<section class="py-20 md:py-32 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Section: Sekarang di Tengah (Sama dengan Galeri) -->
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Artikel Terbaru</h2>
            <div class="h-1.5 w-20 bg-orange-500 mx-auto rounded-full"></div>
            <p class="mt-6 text-gray-600 max-w-2xl mx-auto text-lg">
                Wawasan dan informasi seputar kegiatan & tips dari LKP ELSAM.
            </p>
        </div>

        <!-- Grid Artikel -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <CardArtikel
                v-for="article in articles"
                :key="article.id_artikel"
                :artikel="article"
            />
        </div>

        <!-- Tombol Lihat Semua: Sekarang di Bawah (Sama dengan Galeri) -->
        <div class="mt-12 text-center">
            <Link href="/artikel" class="text-[#3b6db3] font-bold hover:text-blue-700 underline text-lg transition-all">
                Lihat Semua Artikel
            </Link>
        </div>
    </div>
</section>

        <!-- 6. HUBUNGI KAMI -->
        <section class="py-20 md:py-32 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">Hubungi Kami</h2>
                    <div class="h-1.5 w-20 bg-orange-500 mx-auto rounded-full"></div>
                    <p class="mt-6 text-gray-600 max-w-2xl mx-auto text-lg">Kami siap membantu menjawab pertanyaan dan melayani pendaftaran Anda.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                    <ContactCard v-for="(info, index) in contactInfo" :key="index" :title="info.title" :content="info.content"
                        :icon-type="info.icon" />
                </div>

                <div class="w-full h-[500px] rounded-2xl overflow-hidden shadow-2xl border-8 border-white">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.1592171743587!2d109.65007127411849!3d-7.666025675866393!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7aca07ee3695c1%3A0x13714daf29fb4892!2sGenius%20Course%20Kebumen!5e0!3m2!1sid!2sid!4v1778080141443!5m2!1sid!2sid"
                        class="w-full h-full" style="border:0;" allowfullscreen="true" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>
        <!-- LIGHTBOX MODAL (Tambahkan ini sebelum penutup Layout) -->
        <transition name="fade">
            <div v-if="selectedGallery" class="fixed inset-0 z-[100] flex flex-col bg-black/95 backdrop-blur-md">
                <!-- Header Modal -->
                <div class="flex justify-between items-center p-6 text-white z-[110]">
                    <div class="flex flex-col">
                        <h2 class="text-xl font-bold">{{ selectedGallery.judul }}</h2>
                        <span class="text-sm text-gray-400 flex items-center gap-2">
                            <Calendar class="w-4 h-4" />
                            {{ selectedGallery.tanggal || formatDate(selectedGallery.created_at) }}
                        </span>
                    </div>
                    <button @click="closeLightbox" class="p-2 hover:bg-white/10 rounded-full transition-colors">
                        <X class="w-8 h-8" />
                    </button>
                </div>

                <!-- Media Content Area -->
                <div class="flex-1 relative flex items-center justify-center p-4 md:p-10">
                    <div class="absolute inset-0" @click="closeLightbox"></div>

                    <button v-if="selectedIndex > 0" @click.stop="prevLightbox" class="absolute left-4 z-[120] p-4 text-white hover:text-orange-500 transition-colors">
                        <ChevronLeft class="w-10 h-10" />
                    </button>
                    <button v-if="selectedIndex < galleries.length - 1" @click.stop="nextLightbox" class="absolute right-4 z-[120] p-4 text-white hover:text-orange-500 transition-colors">
                        <ChevronRight class="w-10 h-10" />
                    </button>

                    <div class="relative z-[110] max-w-full max-h-full flex items-center justify-center">
                        <img
                            v-if="selectedGallery.jenis_media === 'FOTO'"
                            :src="selectedGallery.file_url"
                            class="max-w-[90vw] max-h-[75vh] object-contain shadow-2xl rounded-sm transition-all duration-300"
                        />
                        <video v-else controls class="max-w-[90vw] max-h-[75vh] shadow-2xl rounded-sm">
                            <source :src="selectedGallery.file_url" type="video/mp4">
                        </video>
                    </div>
                </div>

                <!-- Counter Bawah -->
                <div class="p-6 text-center text-white/50 text-sm font-mono">
                    {{ selectedIndex + 1 }} / {{ galleries.length }}
                </div>
            </div>
        </transition>
    </Layout>
</template>
<style scoped>
.fade-enter-active, .fade-leave-active { transition: all 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: scale(1.02); }
</style>
