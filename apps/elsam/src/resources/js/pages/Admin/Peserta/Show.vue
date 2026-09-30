<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
// Tambahkan RefreshCcw untuk ikon daftar ulang (opsional, tapi bagus untuk visual)
import { ChevronLeft, User, Mail, Phone, CreditCard, BookOpen, MapPin, Calendar, Users, RefreshCcw } from 'lucide-vue-next';

const props = defineProps({
    peserta: Object
});

const formatDate = (dateString) => {
    if (!dateString) return '-'; // Handle jika tanggal kosong
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
};
</script>

<template>
    <Head :title="'Detail - ' + peserta.nama_peserta" />

    <AppLayout>
        <div class="w-full p-6 lg:p-8">
            <!-- Header & Back Button -->
            <div class="flex items-center gap-4 mb-8">
                <Link href="/admin/peserta" class="p-2 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    <ChevronLeft class="w-5 h-5 text-gray-600" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Peserta</h1>
                    <p class="text-sm text-gray-500">Informasi lengkap pendaftaran peserta.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Kolom Kiri: Ringkasan Profil -->
                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 rounded-3xl p-8 shadow-sm text-center">
                        <div class="w-24 h-24 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <User class="w-12 h-12" />
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ peserta.nama_peserta }}</h2>
                        <p class="text-sm text-indigo-600 font-mono font-bold mt-1">{{ peserta.no_pendaftaran }}</p>
                        
                        <div class="mt-6 flex justify-center">
                            <span :class="[
                                'px-4 py-1 rounded-full text-xs font-bold uppercase border',
                                peserta.status_peserta === 'Aktif' ? 'bg-green-50 text-green-700 border-green-100' : 
                                peserta.status_peserta === 'Lulus' ? 'bg-blue-50 text-blue-700 border-blue-100' :
                                'bg-yellow-50 text-yellow-700 border-yellow-100'
                            ]">
                                {{ peserta.status_peserta }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail Informasi -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Informasi Pribadi -->
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 uppercase tracking-wider text-xs">Informasi Pribadi</h3>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="flex items-start gap-4">
                                <Mail class="w-5 h-5 text-gray-400 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Email</p>
                                    <p class="text-sm text-gray-900 font-medium">{{ peserta.email }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <Phone class="w-5 h-5 text-gray-400 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">No. Telepon</p>
                                    <p class="text-sm text-gray-900 font-medium">{{ peserta.no_telepon }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <Users class="w-5 h-5 text-gray-400 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Nama Orang Tua</p>
                                    <p class="text-sm text-gray-900 font-medium">{{ peserta.nama_orang_tua }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <Phone class="w-5 h-5 text-gray-400 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">No. Telepon Wali</p>
                                    <p class="text-sm text-gray-900 font-medium">{{ peserta.no_wali }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <CreditCard class="w-5 h-5 text-gray-400 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">NIK (KTP)</p>
                                    <p class="text-sm text-gray-900 font-medium font-mono">{{ peserta.no_ktp || '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <User class="w-5 h-5 text-gray-400 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Jenis Kelamin</p>
                                    <p class="text-sm text-gray-900 font-medium">{{ peserta.jenis_kelamin }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Kursus -->
                    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 uppercase tracking-wider text-xs">Informasi Kursus</h3>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="flex items-start gap-4">
                                <BookOpen class="w-5 h-5 text-gray-400 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Program Pilihan</p>
                                    <p class="text-sm text-indigo-600 font-bold">
                                        {{ peserta.paket?.program?.nama_program || 'Umum' }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 italic">
                                        Paket: {{ peserta.paket?.nama_paket || '-' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <Calendar class="w-5 h-5 text-gray-400 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Tanggal Daftar</p>
                                    <p class="text-sm text-gray-900 font-medium">{{ formatDate(peserta.tanggal_pendaftaran) }}</p>
                                </div>
                            </div>

                            <!-- [BARU] Bagian Tanggal Daftar Ulang -->
                            <div class="flex items-start gap-4">
                                <RefreshCcw class="w-5 h-5 text-emerald-500 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Tanggal Daftar Ulang</p>
                                    <p :class="[
                                        'text-sm font-medium',
                                        peserta.tanggal_daftar_ulang ? 'text-emerald-600' : 'text-gray-400 italic'
                                    ]">
                                        {{ peserta.tanggal_daftar_ulang ? formatDate(peserta.tanggal_daftar_ulang) : 'Belum Daftar Ulang' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4 md:col-span-2">
                                <MapPin class="w-5 h-5 text-gray-400 mt-1" />
                                <div>
                                    <p class="text-xs text-gray-400 font-bold uppercase">Alamat Lengkap</p>
                                    <p class="text-sm text-gray-900 font-medium leading-relaxed">{{ peserta.alamat_peserta }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>