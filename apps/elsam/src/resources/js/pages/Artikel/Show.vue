<script setup>
import { Head, Link } from '@inertiajs/vue3';
import CardArtikel from '@/components/CardArtikel.vue';
import Layout from '@/layouts/layout.vue';

const props = defineProps({
    artikel: Object,
    relatedArtikels: Array
});

// Helper untuk format tanggal
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

// Estimasi waktu baca sederhana
const readingTime = (content) => {
    const wordsPerMinute = 200;
    const textLength = content ? content.split(/\s/g).length : 0;
    return Math.ceil(textLength / wordsPerMinute);
};

// Fungsi Copy Link
const copyToClipboard = () => {
    navigator.clipboard.writeText(window.location.href);
    alert('Link berhasil disalin!');
};
</script>

<template>
    <Layout>
        <Head :title="artikel.judul_artikel" />

        <!-- Background Putih Utama -->
        <div class="min-h-screen bg-white text-slate-900 font-sans">
            
            <!-- HEADER / HERO AREA -->
            <header class="pt-24 pb-12 px-6">
                <div class="max-w-4xl mx-auto">
                    <!-- Breadcrumb & Badges -->
                    <div class="flex flex-wrap items-center gap-4 mb-8">
                        <!-- Kategori (Biru Muda Soft) -->
                        <span class="px-4 py-1.5 bg-sky-50 text-sky-600 text-xs font-bold uppercase tracking-widest rounded-full border border-sky-100">
                            {{ artikel.kategori?.nama_kategori || 'Umum' }}
                        </span>
                        
                        <!-- Tanggal (Orange Muda Soft) -->
                        <div class="flex items-center text-orange-600/80 text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002-2z" />
                            </svg>
                            {{ formatDate(artikel.tanggal_publish) }}
                        </div>

                        <!-- Waktu Baca -->
                        <div class="flex items-center text-slate-400 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ readingTime(artikel.isi_artikel) }} Menit Baca
                        </div>
                    </div>

                    <!-- Judul Utama (Hitam) -->
                    <h1 class="text-3xl md:text-5xl font-extrabold leading-tight mb-10 text-slate-900">
                        {{ artikel.judul_artikel }}
                    </h1>

                    <!-- Penulis -->
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-100 w-fit">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-sky-400 to-orange-300 flex items-center justify-center font-bold text-white shadow-md">
                            {{ artikel.penulis ? artikel.penulis.charAt(0) : 'A' }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ artikel.penulis }}</p>
                            <p class="text-xs text-slate-500 italic">Penulis Artikel</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- GAMBAR UTAMA -->
            <section class="px-6 mb-16">
                <div class="max-w-5xl mx-auto">
                    <div class="relative aspect-video rounded-[2rem] overflow-hidden shadow-xl border border-slate-100">
                        <img 
                            v-if="artikel.gambar" 
                            :src="'/storage/' + artikel.gambar" 
                            class="w-full h-full object-cover"
                            :alt="artikel.judul_artikel"
                        />
                        <div v-else class="w-full h-full bg-slate-100 flex items-center justify-center">
                            <span class="text-slate-400 font-medium">Gambar tidak tersedia</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- KONTEN ARTIKEL -->
            <article class="px-6 mb-24">
                <div class="max-w-4xl mx-auto">
                    <!-- Prose untuk styling otomatis HTML dari editor -->
                    <div 
                        class="prose prose-lg max-w-none 
                        prose-headings:text-slate-900 prose-headings:font-bold
                        prose-p:text-slate-700 prose-p:leading-relaxed
                        prose-strong:text-orange-600 prose-strong:font-bold
                        prose-a:text-sky-600 hover:prose-a:text-sky-500
                        prose-img:rounded-2xl"
                        v-html="artikel.isi_artikel"
                    >
                    </div>

                    <!-- Tombol Share dengan Logo SVG -->
                    <div class="mt-16 pt-8 border-t border-slate-100 flex items-center gap-4">
                        <span class="text-sm text-slate-400 font-bold uppercase tracking-widest">Bagikan:</span>
                        
                        <!-- Facebook -->
                        <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(artikel.slug)}`" target="_blank"
                            class="p-2.5 bg-slate-50 text-slate-600 hover:bg-sky-50 hover:text-sky-600 rounded-xl transition-all border border-slate-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>

                        <!-- WhatsApp -->
                        <a :href="`https://api.whatsapp.com/send?text=${encodeURIComponent(artikel.judul_artikel)} - ${encodeURIComponent(artikel.slug)}`" target="_blank"
                            class="p-2.5 bg-slate-50 text-slate-600 hover:bg-green-50 hover:text-green-600 rounded-xl transition-all border border-slate-100">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </a>

                        <!-- Salin Link -->
                        <button @click="copyToClipboard"
                            class="p-2.5 bg-slate-50 text-slate-600 hover:bg-orange-50 hover:text-orange-600 rounded-xl transition-all border border-slate-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        </button>
                    </div>
                </div>
            </article>

            <!-- ARTIKEL TERKAIT -->
            <section class="bg-slate-50 py-24 border-t border-slate-100">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="flex items-center justify-between mb-12">
                        <h2 class="text-3xl font-bold text-slate-900">Artikel <span class="text-sky-600">Terkait</span></h2>
                        <Link href="/artikel" class="text-orange-600 hover:text-orange-700 font-bold flex items-center gap-2 transition-colors">
                            Lihat Semua 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <CardArtikel 
                            v-for="rel in relatedArtikels" 
                            :key="rel.id_artikel" 
                            :artikel="rel" 
                        />
                    </div>
                </div>
            </section>
        </div>
    </Layout>
</template>

<style>
/* Memastikan paragraf dari editor punya jarak yang pas */
.prose p {
    margin-bottom: 1.5rem;
    color: #334155; /* Slate 700 */
}

/* Mempercantik tampilan list dari editor */
.prose ul {
    list-style-type: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}

.prose ol {
    list-style-type: decimal;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}
</style>