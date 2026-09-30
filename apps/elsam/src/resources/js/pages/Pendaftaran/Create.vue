<script setup lang="ts">
import Layout from '@/layouts/layout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';


const props = defineProps<{
    programs: Array<any>,
    submit_url: string
}>();

// Form object disesuaikan dengan fillable di Model & rules di Request
const form = useForm({
    nama_peserta: '',
    nama_orang_tua: '',
    email: '',
    no_telepon: '',
    no_wali: '',
    no_ktp: '',
    id_paket: '',
    alamat_peserta: '',
    jenis_kelamin: '',
});

// State internal untuk bantuan pemilihan paket
const selectedProgramId = ref('');

// Filter paket berdasarkan program yang dipilih
const availablePakets = computed(() => {
    const program = props.programs.find(p => p.id_program == selectedProgramId.value);
    return program ? program.pakets : [];
});

// Reset id_paket jika ganti program
watch(selectedProgramId, () => {
    form.id_paket = '';
});

const submit = () => {
    form.post(props.submit_url, {
        preserveScroll: true,
        onSuccess: () => {
            const flash = usePage().props.flash as any;
            if (flash.wa_link) {
                // Memberi jeda sebentar agar user bisa baca pesan sukses sebelum redirect/buka WA
                setTimeout(() => {
                    window.open(flash.wa_link, '_blank');
                }, 1000);
            }
            alert('Pendaftaran Berhasil! Kami akan mengarahkan Anda ke WhatsApp Admin untuk konfirmasi.');
            form.reset();
            selectedProgramId.value = '';
        },
    });
};
</script>

<template>
    <Head title="Pendaftaran Siswa Baru - LKP ELSAM" />
    <Layout>
        <div class="py-12 bg-gray-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4">
                 <div class="text-center mb-10">
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 p-2 bg-white rounded-2xl shadow-sm border border-slate-200 flex items-center justify-center">
                        <img src="/image/Logo Elsam.png" alt="Logo" class="max-w-full h-auto" />
                    </div>
                </div>
            </div>
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">

                    <!-- Header -->
                    <div class="bg-[#3b6db3] p-8 text-white text-center">
                        <h1 class="text-3xl font-bold">Formulir Pendaftaran</h1>
                        <p class="opacity-90 mt-2 italic">Bergabunglah bersama LKP ELSAM untuk masa depan gemilang</p>
                    </div>

                    <form @submit.prevent="submit" class="p-8 space-y-8">

                        <!-- Section 1: Identitas Diri -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Identitas Peserta</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Nama -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap Siswa *</label>
                                    <!-- Menambahkan border-2 border-gray-400 agar garis tepi lebih jelas -->
                                    <input v-model="form.nama_peserta" type="text"
                                        class="w-full rounded-xl border-2 border-gray-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all"
                                        placeholder="Isi Nama Lengkap">
                                    <p v-if="form.errors.nama_peserta" class="text-red-500 text-xs mt-1">{{ form.errors.nama_peserta }}</p>
                                </div>

                                <!-- NIK -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">NIK (KTP) *</label>
                                    <input v-model="form.no_ktp" type="text" maxlength="16"
                                        class="w-full rounded-xl border-2 border-gray-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all"
                                        placeholder="16 digit NIK">
                                    <p v-if="form.errors.no_ktp" class="text-red-500 text-xs mt-1">{{ form.errors.no_ktp }}</p>
                                </div>

                                <!-- Jenis Kelamin -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Kelamin *</label>
                                    <select v-model="form.jenis_kelamin"
                                        class="w-full rounded-xl border-2 border-gray-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all">
                                        <option value="">-- Pilih --</option>
                                        <option value="Laki-laki">Laki-laki</option>
                                        <option value="Perempuan">Perempuan</option>
                                    </select>
                                    <p v-if="form.errors.jenis_kelamin" class="text-red-500 text-xs mt-1">{{ form.errors.jenis_kelamin }}</p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Email Aktif *</label>
                                    <input v-model="form.email" type="email"
                                        class="w-full rounded-xl border-2 border-gray-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all"
                                        placeholder="contoh@gmail.com">
                                    <p v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Data Orang Tua & Kontak -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Kontak & Keluarga</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Orang Tua / Wali *</label>
                                    <input v-model="form.nama_orang_tua" type="text"
                                        class="w-full rounded-xl border-2 border-gray-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all">
                                    <p v-if="form.errors.nama_orang_tua" class="text-red-500 text-xs mt-1">{{ form.errors.nama_orang_tua }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">No. WhatsApp Peserta *</label>
                                    <input v-model="form.no_telepon" type="tel"
                                        class="w-full rounded-xl border-2 border-gray-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all"
                                        placeholder="08xxxx">
                                    <p v-if="form.errors.no_telepon" class="text-red-500 text-xs mt-1">{{ form.errors.no_telepon }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">No. HP Orang Tua (Opsional)</label>
                                    <input v-model="form.no_wali" type="tel"
                                        class="w-full rounded-xl border-2 border-gray-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all">
                                    <p v-if="form.errors.no_wali" class="text-red-500 text-xs mt-1">{{ form.errors.no_wali }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 3: Pilihan Kursus -->
                        <div class="bg-blue-50 p-6 rounded-2xl space-y-4 border border-blue-100">
                            <h3 class="text-md font-bold text-[#3b6db3] uppercase tracking-wide">Pilihan Program Pelatihan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-blue-800 mb-1">Pilih Program Utama</label>
                                    <!-- Border dipertegas dengan warna biru yang selaras -->
                                    <select v-model="selectedProgramId"
                                        class="w-full rounded-xl border-2 border-blue-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all bg-white">
                                        <option value="">-- Pilih Program --</option>
                                        <option v-for="prog in programs" :key="prog.id_program" :value="prog.id_program">
                                            {{ prog.nama_program }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-blue-800 mb-1">Pilih Paket Spesifik</label>
                                    <select v-model="form.id_paket" :disabled="!selectedProgramId"
                                        class="w-full rounded-xl border-2 border-blue-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all bg-white disabled:bg-gray-100 disabled:border-gray-300">
                                        <option value="">-- Pilih Paket --</option>
                                        <option v-for="pkt in availablePakets" :key="pkt.id_paket" :value="pkt.id_paket">
                                            {{ pkt.nama_paket }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.id_paket" class="text-red-500 text-xs mt-1">{{ form.errors.id_paket }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section 4: Alamat -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap (Domisili) *</label>
                            <textarea v-model="form.alamat_peserta" rows="3"
                                class="w-full rounded-xl border-2 border-gray-400 focus:border-[#3b6db3] focus:ring-0 px-4 py-2.5 transition-all"
                                placeholder="Nama Jalan, No. Rumah, RT/RW, Kec, Kab..."></textarea>
                            <p v-if="form.errors.alamat_peserta" class="text-red-500 text-xs mt-1">{{ form.errors.alamat_peserta }}</p>
                        </div>

                        <div class="pt-4">
                            <button type="submit" :disabled="form.processing"
                                class="w-full bg-[#3b6db3] hover:bg-[#2d568f] text-white font-bold py-4 rounded-xl shadow-lg transition duration-200 disabled:opacity-50 active:scale-95">
                                <span v-if="form.processing">Sedang Mengirim Data...</span>
                                <span v-else>Kirim Pendaftaran & Konfirmasi WhatsApp</span>
                            </button>
                            <p class="text-center text-gray-500 text-xs mt-4 italic">
                                * Pastikan nomor WhatsApp aktif untuk menerima instruksi pembayaran dan jadwal.
                            </p>
                        </div>
                        <pre style="background:#eee; padding:10px; font-size:11px; overflow:auto;">
{{ JSON.stringify(programs, null, 2) }}
</pre>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>
