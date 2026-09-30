<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import { ref } from 'vue'; // Tambahkan ref untuk preview gambar
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    kategoris: Array 
});


const imagePreview = ref(null);

const form = useForm({
    id_kategori: '',
    judul_artikel: '',
    isi_artikel: '',
    penulis: '',
    tanggal_publish: new Date().toISOString().split('T')[0],
    gambar: null, // Tambahkan field gambar
});

// Fungsi untuk menangani perubahan file gambar
const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.gambar = file;
        imagePreview.value = URL.createObjectURL(file); // Membuat link preview
    }
};

const submit = () => {
    form.post('/admin/artikel', {
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
        },
    });
};
</script>

<template>
    <Head title="Tambah Artikel Baru" />

    <AppLayout>
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Buat Artikel Baru</h1>
                    <p class="text-gray-500 mt-1">Tambahkan Artikel Terbaru Disini.</p>
                </div>
                <Link href="/admin/artikel" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Daftar
                </Link>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 max-w-4xl mx-auto">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- Upload Gambar Section -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Gambar Sampul Artikel</label>
                        <div class="flex items-center space-x-6">
                            <!-- Kotak Preview -->
                            <div class="w-40 h-40 bg-gray-50 rounded-3xl overflow-hidden border-2 border-dashed border-gray-200 flex items-center justify-center relative">
                                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                                <div v-else class="text-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-[10px]">Belum ada foto</span>
                                </div>
                            </div>

                            <!-- Input File -->
                            <div class="flex-1">
                                <input type="file" @change="onFileChange" accept="image/*" 
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition duration-300" />
                                <p class="mt-2 text-xs text-gray-400 italic">Rekomendasi ukuran: 1200x800px (Max 2MB)</p>
                            </div>
                        </div>
                        <div v-if="form.errors.gambar" class="text-red-500 text-xs mt-2">{{ form.errors.gambar }}</div>
                    </div>

                    <!-- Kategori & Judul -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                            <select v-model="form.id_kategori" 
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition">
                                <option value="" disabled>-- Pilih --</option>
                                <option v-for="kat in kategoris" :key="kat.id_kategori" :value="kat.id_kategori">
                                    {{ kat.nama_kategori }}
                                </option>
                            </select>
                            <div v-if="form.errors.id_kategori" class="text-red-500 text-xs mt-2">{{ form.errors.id_kategori }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Judul Artikel</label>
                            <input v-model="form.judul_artikel" type="text" 
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition"
                                placeholder="Ketik judul artikel di sini...">
                            <div v-if="form.errors.judul_artikel" class="text-red-500 text-xs mt-2 ml-2">{{ form.errors.judul_artikel }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Penulis -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama Penulis</label>
                            <input v-model="form.penulis" type="text" 
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition"
                                placeholder="Contoh: Admin Elsam">
                            <div v-if="form.errors.penulis" class="text-red-500 text-xs mt-2 ml-2">{{ form.errors.penulis }}</div>
                        </div>

                        <!-- Tanggal Publish -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Publish</label>
                            <input v-model="form.tanggal_publish" type="date" 
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition">
                            <div v-if="form.errors.tanggal_publish" class="text-red-500 text-xs mt-2 ml-2">{{ form.errors.tanggal_publish }}</div>
                        </div>
                    </div>

                    <!-- Isi Artikel -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Konten / Isi Artikel</label>
                        <textarea v-model="form.isi_artikel" rows="10" 
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition"
                            placeholder="Tulis isi lengkap artikel..."></textarea>
                        <div v-if="form.errors.isi_artikel" class="text-red-500 text-xs mt-2 ml-2">{{ form.errors.isi_artikel }}</div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-4 border-t border-gray-50">
                        <button type="submit" :disabled="form.processing"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-2xl font-bold transition shadow-lg shadow-indigo-200 flex items-center">
                            <span v-if="!form.processing">Terbitkan Artikel Sekarang</span>
                            <span v-else class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sedang Memproses...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>