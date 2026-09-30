<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { 
    User, Mail, Phone, CreditCard, Users, 
    BookOpen, MapPin, ChevronRight, Layers,
    CheckCircle, AlertCircle
} from 'lucide-vue-next';

// Props yang dikirim dari Controller
const props = defineProps({
    programs: Array,
    isAdmin: Boolean
});

// Inisialisasi Form Inertia
const form = useForm({
    nama_peserta: '',
    nama_orang_tua: '', 
    email: '',
    no_telepon: '',
    no_wali: '',
    no_ktp: '',
    id_program: '', 
    id_paket: '',   
    jenis_kelamin: '', 
    alamat_peserta: '',
});

// LOGIKA: Filter Paket berdasarkan Program yang dipilih
const availablePakets = computed(() => {
    // DISESUAIKAN: Mencari berdasarkan id_program (PK Program)
    const selectedProg = props.programs.find(p => p.id_program === form.id_program);
    return selectedProg ? selectedProg.pakets : [];
});



watch(() => form.id_program, () => {
    form.id_paket = '';
});

// Fungsi Submit
const submit = () => {
    const targetUrl = props.isAdmin 
        ? '/admin/peserta'  
        : '/daftar/simpan'; 

    form.post(targetUrl, {
        onSuccess: () => {
            form.reset();
            alert('Data Berhasil Disimpan!');
        },
        onError: (err) => {
            console.error(err);
        }
    });
};
</script>

<template>
    <Head title="Pendaftaran Kursus - LKP ELSAM" />

    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 font-sans">
        <div class="max-w-4xl mx-auto">
            
            <!-- HEADER -->
            <div class="text-center mb-10">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 p-2 bg-white rounded-2xl shadow-sm border border-slate-200 flex items-center justify-center">
                        <img src="/image/Logo Elsam.png" alt="Logo" class="max-w-full h-auto" />
                    </div>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900">Formulir Pendaftaran</h1>
                <p class="mt-2 text-slate-500">Lengkapi data di bawah ini untuk bergabung dengan LKP ELSAM</p>
            </div>

            <!-- FORM START -->
            <form @submit.prevent="submit" class="space-y-6">
                
                <!-- SECTION 1: BIODATA -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                            <User class="w-5 h-5" />
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">Biodata Peserta</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Peserta -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Nama Lengkap</label>
                            <div class="relative">
                                <User class="absolute left-3 top-3 w-4 h-4 text-slate-400" />
                                <input v-model="form.nama_peserta" type="text" placeholder="Nama sesuai KTP/Ijazah"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                                    :class="{'border-red-500': form.errors.nama_peserta}">
                            </div>
                            <div v-if="form.errors.nama_peserta" class="text-red-500 text-xs flex items-center gap-1">
                                <AlertCircle class="w-3 h-3" /> {{ form.errors.nama_peserta }}
                            </div>
                        </div>

                        <!-- NIK -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">NIK (KTP)</label>
                            <div class="relative">
                                <CreditCard class="absolute left-3 top-3 w-4 h-4 text-slate-400" />
                                <input v-model="form.no_ktp" type="text" placeholder="16 Digit NIK"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all outline-none"
                                    :class="{'border-red-500': form.errors.no_ktp}">
                            </div>
                            <div v-if="form.errors.no_ktp" class="text-red-500 text-xs mt-1">{{ form.errors.no_ktp }}</div>
                        </div>

                        <!-- Nama Orang Tua -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Nama Orang Tua / Wali</label>
                            <div class="relative">
                                <Users class="absolute left-3 top-3 w-4 h-4 text-slate-400" />
                                <input v-model="form.nama_orang_tua" type="text" placeholder="Nama Ayah/Ibu"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Alamat Email</label>
                            <div class="relative">
                                <Mail class="absolute left-3 top-3 w-4 h-4 text-slate-400" />
                                <input v-model="form.email" type="email" placeholder="contoh@email.com"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all outline-none">
                            </div>
                            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-sm font-semibold text-slate-700">Jenis Kelamin</label>
                            <div class="flex gap-4">
                                <label class="flex-1 flex items-center justify-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all"
                                    :class="form.jenis_kelamin === 'Laki-laki' ? 'bg-indigo-600 border-indigo-600 text-white shadow-md' : 'bg-white border-slate-100 text-slate-600'">
                                    <input type="radio" v-model="form.jenis_kelamin" value="Laki-laki" class="hidden">
                                    <span class="font-bold">Laki-laki</span>
                                </label>
                                <label class="flex-1 flex items-center justify-center gap-2 p-3 border-2 rounded-xl cursor-pointer transition-all"
                                    :class="form.jenis_kelamin === 'Perempuan' ? 'bg-pink-600 border-pink-600 text-white shadow-md' : 'bg-white border-slate-100 text-slate-600'">
                                    <input type="radio" v-model="form.jenis_kelamin" value="Perempuan" class="hidden">
                                    <span class="font-bold">Perempuan</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: PROGRAM & KONTAK -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                            <BookOpen class="w-5 h-5" />
                        </div>
                        <h2 class="text-lg font-bold text-slate-800">Program & Kontak</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- WhatsApp -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">No. WhatsApp Aktif</label>
                            <div class="relative">
                                <Phone class="absolute left-3 top-3 w-4 h-4 text-slate-400" />
                                <input v-model="form.no_telepon" type="text" placeholder="08xxxxxxxxxx"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                            </div>
                        </div>

                        <!-- No Wali -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">No. HP Orang Tua</label>
                            <div class="relative">
                                <Users class="absolute left-3 top-3 w-4 h-4 text-slate-400" />
                                <input v-model="form.no_wali" type="text" placeholder="Nomor Darurat"
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 transition-all outline-none">
                            </div>
                        </div>

                        <!-- Program Select -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Pilih Program Utama</label>
                            <div class="relative">
                                <BookOpen class="absolute left-3 top-3 w-4 h-4 text-slate-400" />
                                <select v-model="form.id_program" class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl appearance-none outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">-- Pilih Program --</option>
                                    <option v-for="prog in programs" :key="prog.id_program" :value="prog.id_program">
                                        {{ prog.nama_program }}
                                    </option>
                                </select>
                                <ChevronRight class="absolute right-3 top-3.5 w-4 h-4 text-slate-400 rotate-90" />
                            </div>
                        </div>

                        <!-- Paket Select -->
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Pilih Paket Pelatihan</label>
                            <div class="relative">
                                <Layers class="absolute left-3 top-3 w-4 h-4 text-slate-400" />
                                <!-- DISESUAIKAN: v-model ke paket_id -->
                                <select v-model="form.id_paket" :disabled="!form.id_program"
                                    class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl appearance-none outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50">
                                    <option value="">-- Pilih Paket --</option>
                                    <!-- DISESUAIKAN: :key dan :value menggunakan id_paket -->
                                    <option v-for="paket in availablePakets" :key="paket.id_paket" :value="paket.id_paket">
                                        {{ paket.nama_paket }} ({{ paket.total_pertemuan }})
                                    </option>
                                </select>
                                <ChevronRight class="absolute right-3 top-3.5 w-4 h-4 text-slate-400 rotate-90" />
                            </div>
                            <!-- DISESUAIKAN: Error field disamakan dengan paket_id -->
                            <div v-if="form.errors.id_paket" class="text-red-500 text-xs mt-1">{{ form.errors.id_paket }}</div>
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Alamat Lengkap</label>
                            <div class="relative">
                                <MapPin class="absolute left-3 top-3 w-4 h-4 text-slate-400" />
                                <textarea v-model="form.alamat_peserta" rows="3" placeholder="Jl. Contoh No. 123, Kec, Kab..."
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <p class="text-xs text-slate-500">
                        Pastikan data yang Anda masukkan sudah benar sebelum menekan tombol simpan.
                    </p>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <Link href="/" class="flex-1 sm:flex-none px-6 py-2.5 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition text-center">
                            Batal
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="flex-1 sm:flex-none px-8 py-2.5 text-sm font-extrabold text-white bg-indigo-600 rounded-xl shadow-lg shadow-indigo-100 hover:bg-indigo-700 disabled:opacity-50 flex items-center justify-center gap-2">
                            <span v-if="form.processing">Memproses...</span>
                            <span v-else class="flex items-center gap-2 text-white"> <CheckCircle class="w-4 h-4" /> Simpan Pendaftaran </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>