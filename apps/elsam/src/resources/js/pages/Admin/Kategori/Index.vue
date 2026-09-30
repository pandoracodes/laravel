<script setup>
import { useForm, router, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({ kategoris: Array });

const form = useForm({
    nama_kategori: '',
});

const submit = () => {
    form.post('/admin/kategori', {
        onSuccess: () => form.reset(),
    });
};

const deleteKategori = (id) => {
    if (confirm('Hapus kategori ini? Artikel di kategori ini juga akan terhapus.')) {
        router.delete('/admin/kategori/' + id);
    }
};
</script>

<template>
    <Head title="Manajemen Kategori" />
    <AppLayout>
        <div class="p-6 max-w-2xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Kelola Kategori Artikel</h1>

            <!-- Form Tambah Kategori -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 mb-8">
                <form @submit.prevent="submit" class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tambah Kategori Baru</label>
                        <input v-model="form.nama_kategori" type="text" placeholder="Contoh: Tutorial, Berita..." 
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-500 transition">
                    </div>
                    <button type="submit" :disabled="form.processing"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-700 transition">
                        Tambah
                    </button>
                </form>
                <div v-if="form.errors.nama_kategori" class="text-red-500 text-xs mt-2 ml-2">{{ form.errors.nama_kategori }}</div>
            </div>

            <!-- Daftar Kategori -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-50 uppercase text-xs text-gray-400 font-bold">
                        <tr>
                            <th class="px-6 py-4">Nama Kategori</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="kat in kategoris" :key="kat.id_kategori" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-700">{{ kat.nama_kategori }}</td>
                            <td class="px-6 py-4 text-right">
                                <button @click="deleteKategori(kat.id_kategori)" class="text-red-500 hover:underline text-sm font-bold">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>