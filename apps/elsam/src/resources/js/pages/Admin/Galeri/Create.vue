<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { 
    ChevronLeft, CloudUpload, Image as ImageIcon, 
    Video as VideoIcon, Trash2, CheckCircle2, Calendar
} from 'lucide-vue-next';


const form = useForm({
    judul: '',
    jenis_media: 'FOTO',
    file: null, 
    tanggal_upload: new Date().toISOString().split('T')[0], // Default tanggal hari ini
});

const mediaPreview = ref(null);
const isDragging = ref(false);

const handleFileSelect = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    processFile(file);
};

const onDrop = (e) => {
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    processFile(file);
};

const processFile = (file) => {
    form.file = file; // Simpan file ke form
    if (file.type.startsWith('image/')) {
        mediaPreview.value = URL.createObjectURL(file);
    } else {
        mediaPreview.value = 'video-placeholder';
    }
};

const submit = () => {
    // 2. Gunakan route name agar lebih konsisten
    form.post('/admin/galeri', {
        forceFormData: true,
        onSuccess: () => {
            // Kita bisa tambahkan notifikasi sukses di sini
            form.reset();
            mediaPreview.value = null;
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Unggah Galeri" />
        
        <div class="min-h-screen bg-[#F8FAFC] pb-20">
            <!-- Sticky Header -->
            <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200 px-6 py-4">
                <div class="max-w-5xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Link href="/admin/galeri" class="group p-2 hover:bg-slate-100 rounded-full transition-all">
                            <ChevronLeft class="w-5 h-5 text-slate-600 group-hover:-translate-x-1 transition-transform" />
                        </Link>
                        <div>
                            <h1 class="text-lg font-bold text-slate-900 leading-tight">Tambah Galeri</h1>
                            <p class="text-xs text-slate-500">Publikasikan foto atau video kegiatan terbaru</p>
                        </div>
                    </div>
                    
                    <button 
                        @click="submit" 
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm shadow-indigo-200"
                    >
                        <CloudUpload v-if="!form.processing" class="w-4 h-4" />
                        <span v-else class="w-4 h-4 border-2 border-white/30 border-t-white animate-spin rounded-full"></span>
                        {{ form.processing ? 'Menyimpan...' : 'Terbitkan Sekarang' }}
                    </button>
                </div>
            </div>

            <div class="max-w-5xl mx-auto mt-8 px-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Main Input -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                            <div class="p-6 border-b border-slate-50">
                                <label class="text-[13px] font-bold text-slate-700 block mb-3 uppercase tracking-wider">Judul Konten</label>
                                <input 
                                    v-model="form.judul"
                                    type="text" 
                                    placeholder="Masukkan judul kegiatan..."
                                    class="w-full px-4 py-3 rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all text-slate-800 placeholder:text-slate-400"
                                />
                                <div v-if="form.errors.judul" class="mt-2 text-xs text-red-500 flex items-center gap-1">
                                    {{ form.errors.judul }}
                                </div>
                            </div>

                            <!-- Upload Zone -->
                            <div class="p-6">
                                <label class="text-[13px] font-bold text-slate-700 block mb-3 uppercase tracking-wider">File Media</label>
                                
                                <div 
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @drop.prevent="onDrop"
                                    :class="[
                                        'relative group min-h-[350px] rounded-2xl border-2 border-dashed transition-all flex flex-col items-center justify-center p-4 overflow-hidden',
                                        isDragging ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-50'
                                    ]"
                                >
                                    <!-- Preview State -->
                                    <template v-if="form.file">
                                        <div class="relative w-full h-full group/preview">
                                            <img v-if="mediaPreview !== 'video-placeholder'" :src="mediaPreview" class="w-full h-[320px] object-cover rounded-xl shadow-md" />
                                            <div v-else class="w-full h-[320px] bg-slate-900 rounded-xl flex flex-col items-center justify-center text-white">
                                                <VideoIcon class="w-16 h-16 mb-4 text-indigo-400" />
                                                <p class="font-medium text-sm">{{ form.file.name }}</p>
                                            </div>
                                            
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover/preview:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                                <button @click="form.file = null; mediaPreview = null" class="bg-white/20 backdrop-blur-md hover:bg-red-500 text-white p-3 rounded-full transition-colors">
                                                    <Trash2 class="w-5 h-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Empty State -->
                                    <template v-else>
                                        <div class="text-center">
                                            <div class="w-16 h-16 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400 group-hover:scale-110 group-hover:text-indigo-500 transition-all duration-300">
                                                <CloudUpload class="w-8 h-8" />
                                            </div>
                                            <p class="text-sm font-semibold text-slate-700">Tarik file atau <span class="text-indigo-600 cursor-pointer">cari file</span></p>
                                            <p class="text-[11px] text-slate-500 mt-1 uppercase tracking-tight">Format: JPG, PNG, MP4 (Max 20MB)</p>
                                        </div>
                                        <input type="file" @change="handleFileSelect" class="absolute inset-0 opacity-0 cursor-pointer" />
                                    </template>
                                </div>
                                <!-- Error field diperbaiki jadi form.errors.file -->
                                <div v-if="form.errors.file" class="mt-3 text-xs text-red-500 flex items-center gap-1">
                                    {{ form.errors.file }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Settings -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span> Konfigurasi
                            </h3>
                            
                            <div class="space-y-5">
                                <!-- INPUT TANGGAL (Penting karena ada di DB) -->
                                <div>
                                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2 block">Tanggal Upload</label>
                                    <div class="relative">
                                        <Calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                                        <input 
                                            v-model="form.tanggal_upload"
                                            type="date" 
                                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 text-sm focus:border-indigo-500 focus:ring-indigo-500/10 transition-all"
                                        />
                                    </div>
                                    <div v-if="form.errors.tanggal_upload" class="mt-1 text-[10px] text-red-500">{{ form.errors.tanggal_upload }}</div>
                                </div>

                                <div>
                                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2 block">Jenis Media</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button 
                                            @click="form.jenis_media = 'FOTO'"
                                            type="button"
                                            :class="[
                                                'flex items-center justify-center gap-2 py-3 rounded-xl border text-xs font-bold transition-all',
                                                form.jenis_media === 'FOTO' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100' : 'border-slate-200 text-slate-500 hover:bg-slate-50'
                                            ]"
                                        >
                                            <ImageIcon class="w-4 h-4" /> FOTO
                                        </button>
                                        <button 
                                            @click="form.jenis_media = 'VIDEO'"
                                            type="button"
                                            :class="[
                                                'flex items-center justify-center gap-2 py-3 rounded-xl border text-xs font-bold transition-all',
                                                form.jenis_media === 'VIDEO' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100' : 'border-slate-200 text-slate-500 hover:bg-slate-50'
                                            ]"
                                        >
                                            <VideoIcon class="w-4 h-4" /> VIDEO
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 mt-6">
                                    <h4 class="text-[10px] font-bold text-slate-400 uppercase mb-3">Informasi</h4>
                                    <ul class="space-y-2">
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>