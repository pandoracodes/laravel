<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue'; 
import { 
    Save, ArrowLeft, BookOpen, 
    CheckCircle, PlusCircle, Trash2, 
    ImageIcon, Layers
} from 'lucide-vue-next';

const UrlProgramIndex = "/admin/program"; 

const form = useForm({
    nama_program: '',
    deskripsi: '',
    status: 'Aktif',    
    gambar: null as File | null,
    pakets: [
        { nama_paket: '', deskripsi: '', total_pertemuan: '', harga: 0 }
    ]
});

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.gambar = target.files[0];
    }
};

const addPaket = () => {
    form.pakets.push({ nama_paket: '', deskripsi: '', total_pertemuan: '', harga: 0 });
};

const removePaket = (index: number) => {
    if (form.pakets.length > 1) {
        form.pakets.splice(index, 1);
    }
};

const submit = () => {
    form.post('/admin/program', {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout title="Tambah Program Baru">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xl text-gray-800">Tambah Program Pelatihan</h2>
                <Link :href="UrlProgramIndex" class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition">
                    <ArrowLeft class="w-4 h-4" /> Kembali ke Daftar
                </Link>
            </div>
        </template>

        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- INFORMASI UTAMA -->
                    <div class="bg-white border border-gray-200 shadow-sm sm:rounded-2xl overflow-hidden">
                        <div class="bg-slate-50 px-6 py-4 border-b flex items-center gap-2">
                            <BookOpen class="w-5 h-5 text-indigo-600" />
                            <h3 class="font-bold text-gray-800">Informasi Utama Program</h3>
                        </div>

                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Program</label>
                                    <input v-model="form.nama_program" type="text" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500" placeholder="Contoh: Setir Mobil Profesional" />
                                    <p v-if="form.errors.nama_program" class="text-red-500 text-xs mt-1">{{ form.errors.nama_program }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Status Program</label>
                                    <select v-model="form.status" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500">
                                        <option value="Aktif">Aktif (Tampil di Website)</option>
                                        <option value="Non-Aktif">Non-Aktif (Draft)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Gambar Banner</label>
                                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100">
                                        <ImageIcon class="w-8 h-8 mb-2 text-gray-400" />
                                        <p class="text-xs text-gray-500">Klik untuk upload (PNG, JPG, WEBP)</p>
                                        <input type="file" @change="handleImageChange" class="hidden" accept="image/*"/>
                                    </label>
                                    <div v-if="form.gambar" class="mt-2 text-xs text-indigo-600 font-bold flex items-center gap-1">
                                        <CheckCircle class="w-3 h-3" /> {{ form.gambar.name }}
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Program</label>
                                <textarea v-model="form.deskripsi" rows="3" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500" placeholder="Jelaskan mengenai program ini..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- DAFTAR PAKET -->
                    <div class="bg-white border border-gray-200 shadow-sm sm:rounded-2xl overflow-hidden">
                        <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <Layers class="w-5 h-5 text-indigo-600" />
                                <h3 class="font-bold text-indigo-900">Konfigurasi Paket Pelatihan</h3>
                            </div>
                            <button type="button" @click="addPaket" class="flex items-center gap-1 text-xs font-bold text-indigo-600 bg-white px-3 py-1.5 rounded-lg border border-indigo-200 hover:bg-indigo-600 hover:text-white transition-all">
                                <PlusCircle class="w-4 h-4" /> Tambah Pilihan Paket
                            </button>
                        </div>

                        <div class="p-8 space-y-6">
                            <div v-for="(paket, index) in form.pakets" :key="index" class="relative bg-slate-50 border border-slate-200 p-6 rounded-2xl hover:border-indigo-200 transition-all">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                                    <!-- Baris 1 -->
                                    <div class="md:col-span-5">
                                        <label class="block text-[10px] uppercase font-black text-slate-400 mb-2">Nama Paket</label>
                                        <input v-model="paket.nama_paket" type="text" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm" placeholder="Misal: Paket Pemula" />
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-[10px] uppercase font-black text-slate-400 mb-2">Total Sesi</label>
                                        <input v-model="paket.total_pertemuan" type="text" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm" placeholder="12 Sesi" />
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="block text-[10px] uppercase font-black text-slate-400 mb-2">Biaya (Rp)</label>
                                        <input v-model="paket.harga" type="number" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm" placeholder="Nominal" />
                                    </div>
                                    <div class="md:col-span-1 flex justify-center items-start pt-6">
                                        <button type="button" @click="removePaket(index)" class="p-2.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl">
                                            <Trash2 class="w-5 h-5" />
                                        </button>
                                    </div>

                                    <!-- Baris 2: Deskripsi Paket -->
                                    <div class="md:col-span-11">
                                        <label class="block text-[10px] uppercase font-black text-slate-400 mb-2">Deskripsi / Fasilitas Paket</label>
                                        <textarea v-model="paket.deskripsi" rows="2" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm" placeholder="Contoh: Sertifikat, Modul, Free Konsultasi..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AKSI -->
                    <div class="flex items-center justify-end gap-4 bg-white p-6 rounded-2xl border border-gray-200">
                        <Link :href="UrlProgramIndex" class="px-8 py-3 text-sm font-bold text-gray-500">Batalkan</Link>
                        <button type="submit" :disabled="form.processing" class="flex items-center gap-2 px-10 py-3 bg-indigo-600 text-white rounded-xl font-bold disabled:opacity-50 transition-all active:scale-95">
                            <Save v-if="!form.processing" class="w-5 h-5" />
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Program & Paket' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>