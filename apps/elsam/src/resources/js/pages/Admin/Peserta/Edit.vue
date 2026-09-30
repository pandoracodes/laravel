<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch, ref } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { 
    User, Mail, Phone, MapPin, ChevronLeft, Save,
    Layers, Activity, Calendar // Tambahkan Calendar icon
} from 'lucide-vue-next';

const props = defineProps({
    peserta: Object, 
    programs: Array  
});

// Helper untuk format tanggal ke YYYY-MM-DD agar terbaca oleh input type="date"
const formatDate = (dateString) => {
    if (!dateString) return '';
    return dateString.split('T')[0];
};

const form = useForm({
    nama_peserta: props.peserta.nama_peserta ?? '',
    nama_orang_tua: props.peserta.nama_orang_tua ?? '',
    email: props.peserta.email ?? '',
    no_telepon: props.peserta.no_telepon ?? '',
    no_wali: props.peserta.no_wali ?? '', // Sudah ada di form
    no_ktp: props.peserta.no_ktp ?? '',
    id_program: props.peserta.id_program ?? '', 
    id_paket: props.peserta.id_paket ?? '',     
    jenis_kelamin: props.peserta.jenis_kelamin ?? '',
    alamat_peserta: props.peserta.alamat_peserta ?? '',
    status_peserta: props.peserta.status_peserta ?? '',
    tanggal_pendaftaran: formatDate(props.peserta.tanggal_pendaftaran), // TAMBAHKAN INI
    tanggal_daftar_ulang: formatDate(props.peserta.tanggal_daftar_ulang),
});

// Otomatis isi tanggal daftar ulang jika status diubah ke Aktif
watch(() => form.status_peserta, (newStatus) => {
    if (newStatus === 'Aktif' && !form.tanggal_daftar_ulang) {
        form.tanggal_daftar_ulang = new Date().toISOString().split('T')[0];
    }
});

const availablePakets = computed(() => {
    const selectedProg = props.programs.find(p => p.id_program === form.id_program);
    return selectedProg ? selectedProg.pakets : [];
});

const isInitialLoad = ref(true);
watch(() => form.id_program, (newVal) => {
    if (!isInitialLoad.value) {
        form.id_paket = '';
    }
    isInitialLoad.value = false;
});

const submit = () => {
     form.put(`/admin/peserta/${props.peserta.id_peserta}`, {
        onSuccess: () => alert('Data berhasil diperbarui'),
        onError: (errors) => console.log('Error Validasi:', errors)
    });
};
</script>

<template>
    <Head :title="'Edit Peserta - ' + peserta.nama_peserta" />

    <AppLayout>
        <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            
            <!-- Breadcrumb -->
            <div class="mb-6">
                <Link href="/admin/peserta" class="inline-flex items-center text-sm text-slate-500 hover:text-indigo-600 transition-colors">
                    <ChevronLeft class="w-4 h-4 mr-1" />
                    Kembali ke Daftar Peserta
                </Link>
            </div>

            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Data Peserta</h1>
                    <p class="text-slate-500 mt-1">Ubah informasi pendaftaran peserta <span class="font-bold text-indigo-600">{{ peserta.no_pendaftaran }}</span></p>
                </div>
                <div class="flex items-center px-4 py-2 bg-indigo-50 rounded-2xl border border-indigo-100">
                    <div class="text-right">
                        <p class="text-[10px] uppercase font-bold text-indigo-400 leading-none">Status Saat Ini</p>
                        <p class="text-sm font-bold text-indigo-700">{{ form.status_peserta }}</p>
                    </div>
                    <Activity class="w-5 h-5 ml-3 text-indigo-500" />
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    <!-- Kolom Kiri: Biodata & Kontak -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                    <User class="w-5 h-5" />
                                </div>
                                <h2 class="font-bold text-slate-800">Biodata Diri</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Nama Lengkap Peserta</label>
                                    <input v-model="form.nama_peserta" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                                    <p v-if="form.errors.nama_peserta" class="text-red-500 text-xs mt-1">{{ form.errors.nama_peserta }}</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Nama Orang Tua / Wali</label>
                                    <input v-model="form.nama_orang_tua" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">NIK (KTP)</label>
                                    <input v-model="form.no_ktp" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                                    <p v-if="form.errors.no_ktp" class="text-red-500 text-xs mt-1">{{ form.errors.no_ktp }}</p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2 ml-1">Jenis Kelamin</label>
                                    <div class="flex gap-4">
                                        <label class="flex-1">
                                            <input type="radio" v-model="form.jenis_kelamin" value="Laki-laki" class="peer hidden">
                                            <div class="p-3 text-center rounded-xl border-2 border-slate-100 bg-slate-50 cursor-pointer peer-checked:border-indigo-600 peer-checked:bg-indigo-600 peer-checked:text-white transition-all">Laki-laki</div>
                                        </label>
                                        <label class="flex-1">
                                            <input type="radio" v-model="form.jenis_kelamin" value="Perempuan" class="peer hidden">
                                            <div class="p-3 text-center rounded-xl border-2 border-slate-100 bg-slate-50 cursor-pointer peer-checked:border-pink-600 peer-checked:bg-pink-600 peer-checked:text-white transition-all">Perempuan</div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                                    <MapPin class="w-5 h-5" />
                                </div>
                                <h2 class="font-bold text-slate-800">Domisili</h2>
                            </div>
                            <textarea v-model="form.alamat_peserta" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all outline-none" placeholder="Alamat lengkap..."></textarea>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Program & Status -->
                    <div class="space-y-6">
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                                    <Phone class="w-5 h-5" />
                                </div>
                                <h2 class="font-bold text-slate-800">Kontak</h2>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Email</label>
                                    <input v-model="form.email" type="email" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition-all outline-none text-sm">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">No. WhatsApp Peserta</label>
                                    <input v-model="form.no_telepon" type="text" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition-all outline-none text-sm">
                                </div>
                                <!-- TAMBAHAN KOLOM NO WALI -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">No. WhatsApp Wali</label>
                                    <input v-model="form.no_wali" type="text" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white transition-all outline-none text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                    <Layers class="w-5 h-5" />
                                </div>
                                <h2 class="font-bold text-slate-800">Pelatihan</h2>
                            </div>
                            <div class="space-y-4">
                                <!-- TAMBAHAN TANGGAL PENDAFTARAN -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Tanggal Pendaftaran</label>
                                    <input v-model="form.tanggal_pendaftaran" type="date" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white outline-none text-sm">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Program</label>
                                    <select v-model="form.id_program" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white outline-none text-sm">
                                        <option value="">Pilih Program</option>
                                        <option v-for="prog in programs" :key="prog.id_program" :value="prog.id_program">
                                            {{ prog.nama_program }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Paket Pelatihan</label>
                                    <select v-model="form.id_paket" :disabled="!form.id_program" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white outline-none text-sm disabled:opacity-50">
                                        <option value="">Pilih Paket</option>
                                        <option v-for="paket in availablePakets" :key="paket.id_paket" :value="paket.id_paket">
                                            {{ paket.nama_paket }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.id_paket" class="text-red-500 text-xs mt-1">{{ form.errors.id_paket }}</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Status Peserta</label>
                                    <select v-model="form.status_peserta" class="w-full px-4 py-2.5 bg-indigo-600 text-white border-none rounded-xl outline-none text-sm font-bold">
                                        <option value="Calon Peserta">Calon Peserta</option>
                                        <option value="Aktif">Aktif</option>
                                        <option value="Lulus">Lulus</option>
                                        <option value="Non-Aktif">Non-Aktif</option>
                                    </select>
                                </div>
                                <!-- Input Daftar Ulang -->
                                <div v-if="form.status_peserta === 'Aktif' || form.tanggal_daftar_ulang" class="pt-2 animate-in fade-in duration-500">
                                    <label class="block text-[10px] font-bold text-emerald-600 uppercase mb-1 ml-1">Tanggal Daftar Ulang</label>
                                    <input v-model="form.tanggal_daftar_ulang" type="date" class="w-full px-4 py-2.5 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl outline-none text-sm font-semibold focus:ring-2 focus:ring-emerald-500 transition-all">
                                    <p class="text-[10px] text-slate-400 mt-1 ml-1">*Diisi saat registrasi ulang.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
                    <Link href="/admin/peserta" class="px-6 py-3 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-2xl transition-all">
                        Batal
                    </Link>
                    <button type="submit" :disabled="form.processing" class="flex items-center gap-2 px-10 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-50">
                        <Save class="w-4 h-4" />
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>