<script setup>
import { useForm, Link, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
    artikel: Object,
    kategoris: Array 
});

// Preview untuk gambar lama atau gambar baru yang dipilih
const imagePreview = ref(props.artikel.gambar ? `/storage/${props.artikel.gambar}` : null);

const form = useForm({
    _method: 'put', 
    id_kategori: props.artikel.id_kategori,
    judul_artikel: props.artikel.judul_artikel,
    isi_artikel: props.artikel.isi_artikel,
    penulis: props.artikel.penulis,
    tanggal_publish: props.artikel.tanggal_publish,
    gambar: null, 
});

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.gambar = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {

    form.post(`/admin/artikel/${props.artikel.id_artikel}`, {
        onSuccess: () => {
            // Berhasil update
        },
    });
};
</script>

<template>
    <Head title="Edit Artikel" />

    <AppLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Artikel</h1>
                    <p class="text-gray-500 mt-1">Perbarui informasi artikel Anda.</p>
                </div>
                <Link href="/admin/artikel" class="text-indigo-600 hover:text-indigo-800 font-semibold flex items-center text-sm">
                    Kembali ke Daftar
                </Link>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 max-w-4xl mx-auto">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- Upload Gambar Section -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Gambar Sampul</label>
                        <div class="flex items-center space-x-6">
                            <div class="w-40 h-40 bg-gray-50 rounded-3xl overflow-hidden border-2 border-dashed border-gray-200 flex items-center justify-center relative">
                                <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                                <div v-else class="text-center text-gray-400">
                                    <span class="text-[10px]">Tanpa Gambar</span>
                                </div>
                            </div>

                            <div class="flex-1">
                                <input type="file" @change="onFileChange" accept="image/*" 
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition duration-300" />
                                <p class="mt-2 text-xs text-gray-400 italic">Pilih file baru jika ingin mengganti gambar.</p>
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
                                <option v-for="kat in kategoris" :key="kat.id_kategori" :value="kat.id_kategori">
                                    {{ kat.nama_kategori }}
                                </option>
                            </select>
                            <div v-if="form.errors.id_kategori" class="text-red-500 text-xs mt-2">{{ form.errors.id_kategori }}</div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Judul Artikel</label>
                            <input v-model="form.judul_artikel" type="text" 
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition">
                            <div v-if="form.errors.judul_artikel" class="text-red-500 text-xs mt-2">{{ form.errors.judul_artikel }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Penulis</label>
                            <input v-model="form.penulis" type="text" 
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Publish</label>
                            <input v-model="form.tanggal_publish" type="date" 
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Konten</label>
                        <textarea v-model="form.isi_artikel" rows="10" 
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition"></textarea>
                        <div v-if="form.errors.isi_artikel" class="text-red-500 text-xs mt-2">{{ form.errors.isi_artikel }}</div>
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" :disabled="form.processing"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-10 py-4 rounded-2xl font-bold transition flex items-center">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>