<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import Layout from '@/layouts/layout.vue';
import { Head, Link } from '@inertiajs/vue3'; // Tambahkan Link
import CardGaleri from '@/components/CardGaleri.vue';
import { X, ChevronLeft, ChevronRight, Calendar } from 'lucide-vue-next';

const props = defineProps<{
    galleries: {
        data: Array<any>;
        links: Array<any>;
    },
    heroGalleries: Array<any>;
}>();

// --- LOGIKA SLIDER HERO ---
const currentSlide = ref(0);
let slideInterval: any = null;

const nextSlide = () => { 
    if (props.heroGalleries.length > 0) {
        currentSlide.value = (currentSlide.value + 1) % props.heroGalleries.length;
    }
};

const prevSlide = () => { 
    if (props.heroGalleries.length > 0) {
        currentSlide.value = (currentSlide.value - 1 + props.heroGalleries.length) % props.heroGalleries.length;
    }
};

const startInterval = () => { 
    stopInterval(); 
    if (props.heroGalleries?.length > 1) slideInterval = setInterval(nextSlide, 5000); 
};

const stopInterval = () => { if (slideInterval) clearInterval(slideInterval); };

// --- LOGIKA LIGHTBOX ---
const selectedGallery = ref<any>(null);
const selectedIndex = ref<number>(-1);

const openLightbox = (item: any, index: number) => {
    selectedGallery.value = item;
    selectedIndex.value = index;
    document.body.style.overflow = 'hidden'; 
};

const formatDate = (dateString: string) => {
    if (!dateString) return 'Baru-baru ini';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    }).format(date);
};

const closeLightbox = () => {
    selectedGallery.value = null;
    selectedIndex.value = -1;
    document.body.style.overflow = 'auto'; 
};

const nextLightbox = () => {
    if (selectedIndex.value < props.galleries.data.length - 1) {
        selectedIndex.value++;
        selectedGallery.value = props.galleries.data[selectedIndex.value];
    }
};

const prevLightbox = () => {
    if (selectedIndex.value > 0) {
        selectedIndex.value--;
        selectedGallery.value = props.galleries.data[selectedIndex.value];
    }
};

const handleKeyDown = (e: KeyboardEvent) => {
    if (!selectedGallery.value) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') nextLightbox();
    if (e.key === 'ArrowLeft') prevLightbox();
};

onMounted(() => {
    startInterval();
    window.addEventListener('keydown', handleKeyDown);
});
onUnmounted(() => {
    stopInterval();
    window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <Head title="Galeri" />
    <Layout>
        <!-- Hero Section -->
        <div class="relative group overflow-hidden flex items-end justify-center h-[350px] md:h-[500px] bg-gray-900">
            
<!-- Slides Hero -->
<div v-if="heroGalleries && heroGalleries.length > 0" class="absolute inset-0">
    <transition-group name="fade">
        <div 
            v-for="(slide, index) in heroGalleries" 
            :key="slide.id_galeri || index"
            v-show="currentSlide === index"
            class="absolute inset-0 w-full h-full flex items-center justify-center bg-black"
        >
            <!-- Background Blur: Hanya tampil jika medianya FOTO -->
            <!-- Kita cek: Jika file TIDAK diakhiri .mp4 DAN jenis_media bukan VIDEO -->
            <img 
                v-if="!slide.file_url.toLowerCase().endsWith('.mp4') && slide.jenis_media !== 'VIDEO' && slide.jenis_media !== 'video'"
                :src="slide.file_url" 
                class="absolute inset-0 w-full h-full object-cover blur-xl opacity-40 scale-110" 
            />
            
            <!-- MEDIA UTAMA HERO -->
            <!-- Pengecekan Video yang lebih kuat (Cek Tulisan Database ATAU Akhiran File) -->
            <template v-if="slide.jenis_media?.toUpperCase() === 'VIDEO' || slide.file_url.toLowerCase().endsWith('.mp4')">
                <video 
                    autoplay 
                    muted 
                    loop 
                    playsinline
                    class="relative z-10 max-w-full max-h-full object-contain"
                >
                    <source :src="slide.file_url" type="video/mp4">
                </video>
            </template>

            <!-- Jika bukan Video, tampilkan Foto -->
            <template v-else>
                <img 
                    :src="slide.file_url" 
                    :alt="slide.judul"
                    class="relative z-10 max-w-full max-h-full object-contain"
                />
            </template>
        </div>
    </transition-group>
</div>
            <img v-else src="image/hero-elsam 3.png" class="absolute inset-0 w-full h-full object-cover" />

            <div class="absolute inset-0 bg-gradient-to-t from-[#0a162b] via-transparent to-transparent z-10"></div>

            <!-- Navigasi Hero -->
            <template v-if="heroGalleries && heroGalleries.length > 1">
                <button @click="prevSlide(); startInterval();" class="absolute left-4 top-1/2 -translate-y-1/2 z-30 p-2 rounded-full bg-white/10 hover:bg-orange-500 text-white transition-all opacity-0 group-hover:opacity-100 backdrop-blur-md">
                    <ChevronLeft class="w-6 h-6" />
                </button>
                <button @click="nextSlide(); startInterval();" class="absolute right-4 top-1/2 -translate-y-1/2 z-30 p-2 rounded-full bg-white/10 hover:bg-orange-500 text-white transition-all opacity-0 group-hover:opacity-100 backdrop-blur-md">
                    <ChevronRight class="w-6 h-6" />
                </button>
            </template>

            <!-- Teks Hero -->
            <div class="relative z-20 pb-12 px-4 text-center">
                <p class="text-blue-50 text-sm md:text-lg max-w-2xl mx-auto opacity-90 font-light">
                    Momen berharga dan dokumentasi seru dari setiap aktivitas pelatihan kami.
                </p>
                <div v-if="heroGalleries && heroGalleries.length > 1" class="flex justify-center gap-2 mt-6">
                    <button 
                        v-for="(_, index) in heroGalleries" :key="index"
                        @click="currentSlide = index; startInterval();"
                        class="h-1.5 transition-all duration-300 rounded-full"
                        :class="currentSlide === index ? 'bg-orange-500 w-8' : 'bg-white/40 w-2'"
                    ></button>
                </div>
            </div>
        </div>

        <!-- Section Grid Galeri -->
        <div class="bg-gray-50 min-h-screen py-16">
            <div class="max-w-7xl mx-auto px-4">
                <div v-if="galleries.data.length > 0">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
                        <CardGaleri 
                            v-for="(item, index) in galleries.data" 
                            :key="item.id_galeri"
                            :image="item.file_url"
                            :title="item.judul"
                            :category="item.jenis_media"
                            class="cursor-pointer"
                            @click="openLightbox(item, index)"
                        />
                    </div>

                    <!-- Paginasi -->
                    <div class="mt-16 flex justify-center gap-2">
                        <Link 
                            v-for="link in galleries.links" :key="link.label"
                            :href="link.url || '#'"
                            v-html="link.label"
                            class="px-4 py-2 border rounded-lg transition-all"
                            :class="{  'bg-blue-600 text-white border-blue-600 shadow-lg': link.active, 
                            'text-gray-400': !link.url, 'bg-white hover:bg-gray-100': link.url && !link.active }"
                        />
                    </div>
                </div>

                <div v-else class="text-center py-20">
                    <div class="bg-white inline-block p-10 rounded-3xl shadow-sm border border-gray-100">
                        <p class="text-gray-500 text-lg">Belum ada dokumentasi kegiatan saat ini.</p>
                    </div>
                </div>
            </div>
        </div>

       <!-- LIGHTBOX MODAL -->
       <transition name="fade">
            <div v-if="selectedGallery" class="fixed inset-0 z-[100] flex flex-col bg-black/95 backdrop-blur-md">
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

                <div class="flex-1 relative flex items-center justify-center p-4 md:p-10">
                    <div class="absolute inset-0" @click="closeLightbox"></div>

                    <button v-if="selectedIndex > 0" @click.stop="prevLightbox" class="absolute left-4 z-[120] p-4 text-white hover:text-orange-500 transition-colors">
                        <ChevronLeft class="w-10 h-10" />
                    </button>
                    <button v-if="selectedIndex < galleries.data.length - 1" @click.stop="nextLightbox" class="absolute right-4 z-[120] p-4 text-white hover:text-orange-500 transition-colors">
                        <ChevronRight class="w-10 h-10" />
                    </button>

                    <!-- Media Utama Lightbox -->
                    <div class="relative z-[110] max-w-full max-h-full flex items-center justify-center">
                        <img 
                            v-if="selectedGallery.jenis_media === 'FOTO'"
                            :src="selectedGallery.file_url" 
                            class="max-w-[90vw] max-h-[75vh] object-contain shadow-2xl rounded-sm"
                        />
                        
                        <!-- VIDEO LIGHTBOX: Tambahkan playsinline dan hapus muted jika ingin ada suara -->
                        <video 
                            v-else 
                            :key="selectedGallery.file_url"
                            controls 
                            autoplay
                            playsinline
                            class="max-w-[90vw] max-h-[75vh] shadow-2xl rounded-sm"
                        >
                            <source :src="selectedGallery.file_url" type="video/mp4">
                            Browser anda tidak mendukung video.
                        </video>
                    </div>
                </div>

                <div class="p-6 text-center text-white/50 text-sm font-mono">
                    {{ selectedIndex + 1 }} / {{ galleries.data.length }}
                </div>
            </div>
        </transition>
    </Layout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: all 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: scale(1.05); }

/* Custom transition untuk hero */
.fade-hero-enter-active, .fade-hero-leave-active { transition: opacity 1.5s ease; }
.fade-hero-enter-from, .fade-hero-leave-to { opacity: 0; }
</style>