<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { 
    ChevronLeft, CloudUpload, Image as ImageIcon, 
    Video as VideoIcon, Trash2, CheckCircle2 
} from 'lucide-vue-next';

const props = defineProps({
    galeri: Object // Data dari controller
});

const form = useForm({
    _method: 'put',
    judul: props.galeri.judul,
    jenis_media: props.galeri.jenis_media,
    tanggal_upload: props.galeri.tanggal_upload,
    file: null, // Samakan dengan GaleriRequest
});

// Update mediaPreview awal:
const mediaPreview = ref(props.galeri.file_url); // Pakai file_url dari model

const processFile = (file) => {
    form.file = file; // Simpan ke form.file
    // ... rest of logic
};
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


const submit = () => {
    // Kirim ke route update admin.galeri.update
    form.post(`/admin/galeri/${props.galeri.id_galeri}`, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            alert('Berhasil diperbarui!');
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Edit Galeri" />
        
        <div class="min-h-screen bg-[#F8FAFC] pb-20">
            <!-- Sticky Header -->
            <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200 px-6 py-4">
                <div class="max-w-5xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Link href="/admin/galeri" class="group p-2 hover:bg-slate-100 rounded-full transition-all">
                            <ChevronLeft class="w-5 h-5 text-slate-600 group-hover:-translate-x-1 transition-transform" />
                        </Link>
                        <div>
                            <h1 class="text-lg font-bold text-slate-900 leading-tight">Edit Galeri</h1>
                            <p class="text-xs text-slate-500">Perbarui data foto atau video kegiatan</p>
                        </div>
                    </div>
                    
                    <button 
                        @click="submit" 
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 disabled:bg-slate-300 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition-all shadow-sm shadow-indigo-200"
                    >
                        <CloudUpload v-if="!form.processing" class="w-4 h-4" />
                        <span v-else class="w-4 h-4 border-2 border-white/30 border-t-white animate-spin rounded-full"></span>
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
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
                                    <span class="w-1 h-1 bg-red-500 rounded-full"></span> {{ form.errors.judul }}
                                </div>
                            </div>

                            <!-- Upload Zone -->
                            <div class="p-6">
                                <label class="text-[13px] font-bold text-slate-700 block mb-3 uppercase tracking-wider">File Media (Kosongkan jika tidak diubah)</label>
                                
                                <div 
                                    @dragover.prevent="isDragging = true"
                                    @dragleave.prevent="isDragging = false"
                                    @drop.prevent="onDrop"
                                    :class="[
                                        'relative group min-h-[350px] rounded-2xl border-2 border-dashed transition-all flex flex-col items-center justify-center p-4 overflow-hidden',
                                        isDragging ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-200 bg-slate-50/50 hover:bg-slate-50'
                                    ]"
                                >
                                    <!-- Preview State (Existing or New) -->
                                    <template v-if="mediaPreview">
                                        <div class="relative w-full h-full group/preview">
                                            <!-- Jika jenis media adalah Foto atau baru upload foto -->
                                            <img 
                                                v-if="mediaPreview !== 'video-placeholder'" 
                                                :src="mediaPreview" 
                                                class="w-full h-[320px] object-cover rounded-xl shadow-md" 
                                            />
                                            
                                            <!-- Jika video -->
                                            <div v-else class="w-full h-[320px] bg-slate-900 rounded-xl flex flex-col items-center justify-center text-white">
                                                <VideoIcon class="w-16 h-16 mb-4 text-indigo-400" />
                                                <p class="font-medium text-sm text-center px-4">
                                                    {{ form.file_media ? form.file_media.name : 'File Video Tersimpan' }}
                                                </p>
                                            </div>
                                            
                                            <!-- Overlay Actions -->
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover/preview:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                                <button type="button" @click="form.file_media = null; mediaPreview = null" class="bg-white/20 backdrop-blur-md hover:bg-red-500 text-white p-3 rounded-full transition-colors">
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
                                            <p class="text-sm font-semibold text-slate-700">Pilih file baru atau tarik ke sini</p>
                                        </div>
                                        <input type="file" @change="handleFileSelect" class="absolute inset-0 opacity-0 cursor-pointer" />
                                    </template>
                                </div>
                                <div v-if="form.errors.file_media" class="mt-3 text-xs text-red-500 flex items-center gap-1">
                                    <span class="w-1 h-1 bg-red-500 rounded-full"></span> {{ form.errors.file_media }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Settings -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span> Konfigurasi Media
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2 block">Kategori Media</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button 
                                            @click="form.jenis_media = 'Foto'"
                                            type="button"
                                            :class="[
                                                'flex items-center justify-center gap-2 py-3 rounded-xl border text-xs font-bold transition-all',
                                                form.jenis_media === 'Foto' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100' : 'border-slate-200 text-slate-500 hover:bg-slate-50'
                                            ]"
                                        >
                                            <ImageIcon class="w-4 h-4" /> FOTO
                                        </button>
                                        <button 
                                            @click="form.jenis_media = 'Video'"
                                            type="button"
                                            :class="[
                                                'flex items-center justify-center gap-2 py-3 rounded-xl border text-xs font-bold transition-all',
                                                form.jenis_media === 'Video' ? 'border-indigo-600 bg-indigo-50 text-indigo-700 shadow-sm shadow-indigo-100' : 'border-slate-200 text-slate-500 hover:bg-slate-50'
                                            ]"
                                        >
                                            <VideoIcon class="w-4 h-4" /> VIDEO
                                        </button>
                                    </div>
                                </div>

                                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100 mt-6">
                                    <h4 class="text-[10px] font-bold text-amber-700 uppercase mb-2">Catatan Perubahan</h4>
                                    <p class="text-[11px] text-amber-600 leading-relaxed">
                                        Mengubah file akan menghapus file lama secara permanen dari server. Pastikan format file sudah sesuai.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>