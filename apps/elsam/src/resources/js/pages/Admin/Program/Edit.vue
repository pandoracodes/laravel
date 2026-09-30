<script setup lang="ts">
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue'; 
import { Save, ArrowLeft, BookOpen, ImageIcon, Layers, PlusCircle, Trash2 } from 'lucide-vue-next';

interface Paket {
    id_paket?: number;
    nama_paket: string;
    deskripsi: string; // Tambahkan ini di interface
    total_pertemuan: string;
    harga: number;
}

interface Program {
    id_program: number;
    nama_program: string;
    deskripsi: string;
    status: string;
    gambar: string | null;
    pakets: Paket[];
}

const props = defineProps<{
    program: Program
}>();

const form = useForm({
    _method: 'PUT', 
    nama_program: props.program.nama_program,
    deskripsi: props.program.deskripsi,
    status: props.program.status,
    gambar: null as File | null,
    pakets: props.program.pakets.map(p => ({
        nama_paket: p.nama_paket,
        deskripsi: p.deskripsi || '', // Pastikan data deskripsi di-map ke form
        total_pertemuan: p.total_pertemuan,
        harga: p.harga
    }))
});

const addPaket = () => {
    // Tambahkan deskripsi kosong saat tambah baris baru
    form.pakets.push({ nama_paket: '', deskripsi: '', total_pertemuan: '', harga: 0 });
};

const removePaket = (index: number) => {
    if (form.pakets.length > 1) form.pakets.splice(index, 1);
};

const handleImageChange = (e: any) => {
    form.gambar = e.target.files[0];
};

const submit = () => {
    form.post(`/admin/program/${props.program.id_program}`, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => alert('Program berhasil diperbarui!'),
    });
};
</script>

<template>
    <AppLayout title="Edit Program">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    Edit Program: {{ props.program.nama_program }}
                </h2>
                <Link href="/admin/program" class="flex items-center gap-2 text-sm text-gray-600">
                    <ArrowLeft class="w-4 h-4" /> Kembali
                </Link>
            </div>
        </template>

        <div class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    
                    <!-- INFORMASI UTAMA -->
                    <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nama Program</label>
                                <input v-model="form.nama_program" type="text" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:ring-indigo-500" />
                                <div v-if="form.errors.nama_program" class="text-red-500 text-xs mt-1">{{ form.errors.nama_program }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                                <select v-model="form.status" class="w-full px-4 py-3 rounded-xl border-gray-200">
                                    <option value="Aktif">Aktif</option>
                                    <option value="Non-Aktif">Non-Aktif</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ganti Gambar (Opsional)</label>
                            <div class="flex items-center gap-4">
                                <img v-if="props.program.gambar" :src="`/storage/${props.program.gambar}`" class="w-20 h-20 rounded-xl object-cover border" />
                                <input type="file" @change="handleImageChange" class="text-sm" />
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Program</label>
                            <textarea v-model="form.deskripsi" rows="4" class="w-full px-4 py-3 rounded-xl border-gray-200"></textarea>
                        </div>
                    </div>

                    <!-- DAFTAR PAKET -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-indigo-50 px-6 py-4 flex justify-between items-center">
                            <div class="flex items-center gap-2 font-bold text-indigo-900"><Layers class="w-5 h-5"/> Paket Kursus</div>
                            <button type="button" @click="addPaket" class="text-xs bg-white text-indigo-600 px-3 py-1.5 rounded-lg border border-indigo-200 font-bold hover:bg-indigo-600 hover:text-white transition">+ Tambah Paket</button>
                        </div>
                        <div class="p-8 space-y-6">
                            <div v-for="(paket, index) in form.pakets" :key="index" class="p-6 bg-slate-50 rounded-2xl border border-gray-200 hover:border-indigo-200 transition-all">
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                                    <!-- Baris 1: Info Utama Paket -->
                                    <div class="md:col-span-5">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Nama Paket</label>
                                        <input v-model="paket.nama_paket" type="text" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm" placeholder="Contoh: Paket Reguler" />
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Total Sesi</label>
                                        <input v-model="paket.total_pertemuan" type="text" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm" placeholder="12 Sesi" />
                                    </div>
                                    <div class="md:col-span-3">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Harga (Rp)</label>
                                        <input v-model="paket.harga" type="number" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm" />
                                    </div>
                                    <div class="md:col-span-1 flex justify-center items-start pt-6">
                                        <button type="button" @click="removePaket(index)" class="p-2.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all">
                                            <Trash2 class="w-5 h-5"/>
                                        </button>
                                    </div>

                                    <!-- Baris 2: Deskripsi Paket (Full Width) -->
                                    <div class="md:col-span-11">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 block">Deskripsi / Fasilitas Paket</label>
                                        <textarea v-model="paket.deskripsi" rows="2" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-sm focus:ring-indigo-500" placeholder="Contoh: Sertifikat, Modul, Free Konsultasi..."></textarea>
                                        <p v-if="form.errors[`pakets.${index}.deskripsi`]" class="text-red-500 text-[10px] mt-1">{{ form.errors[`pakets.${index}.deskripsi`] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4 p-6 bg-white rounded-2xl border border-gray-200">
                        <button type="submit" :disabled="form.processing" class="bg-indigo-600 text-white px-10 py-3 rounded-xl font-bold shadow-lg hover:bg-indigo-700 disabled:opacity-50 flex items-center gap-2">
                            <Save class="w-5 h-5" /> {{ form.processing ? 'Menyimpan...' : 'Update Program' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>