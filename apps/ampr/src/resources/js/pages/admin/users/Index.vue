<script setup>
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';

const props = defineProps({ users: Object, filters: Object });
const search = ref(props.filters.search || '');

// State Modal
const showModal = ref(false);
const isEditing = ref(false);

// Form
const form = useForm({
    id: null,
    name: '',
    email: '',
    role: 'security', // Default role
    password: '',
});

// Search Logic
watch(
    search,
    debounce((val) => {
        router.get(
            route('admin.users.index'),
            { search: val },
            { preserveState: true, replace: true },
        );
    }, 300),
);

// Open Modal Tambah
const openAddModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    showModal.value = true;
};

// Open Modal Edit
const openEditModal = (user) => {
    isEditing.value = true;
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = ''; // Kosongkan password saat edit
    form.clearErrors();
    showModal.value = true;
};

// Submit Action
const submit = () => {
    if (isEditing.value) {
        form.put(route('admin.users.update', form.id), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => {
                showModal.value = false;
                form.reset();
            },
        });
    }
};

// Delete Action
const deleteUser = (id) => {
    if (confirm('Hapus user ini? Akses login mereka akan hilang.')) {
        router.delete(route('admin.users.destroy', id));
    }
};

// Helper No Urut
const getRowNumber = (index) =>
    (props.users.current_page - 1) * props.users.per_page + index + 1;
</script>

<template>
    <AdminLayout>
        <template #header>User Management</template>
        <Head title="Users" />

        <!-- ACTION BAR -->
        <div
            class="mb-8 flex flex-col justify-between gap-4 md:flex-row md:items-center"
        >
            <!-- Search -->
            <div class="group relative w-full md:w-80">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Cari Nama / Email..."
                    class="w-full rounded-[20px] border-none bg-white py-4 pr-4 pl-12 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-100 transition-all group-hover:shadow-md focus:ring-2 focus:ring-indigo-500"
                />
                <svg
                    class="absolute top-3.5 left-4 h-5 w-5 text-slate-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    ></path>
                </svg>
            </div>

            <!-- Add Button -->
            <button
                @click="openAddModal"
                class="flex items-center gap-2 rounded-[20px] bg-[#1A5F7A] px-6 py-3 font-bold text-white shadow-lg shadow-[#1A5F7A]/30 transition-all hover:bg-[#164e63] hover:shadow-xl hover:shadow-[#1A5F7A]/40 active:scale-95"
            >
                <svg
                    class="h-5 w-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4v16m8-8H4"
                    ></path>
                </svg>
                <span>Tambah User</span>
            </button>
        </div>

        <!-- TABLE LIST -->
        <div
            class="overflow-hidden rounded-[2.5rem] border border-white/60 bg-white shadow-sm ring-1 ring-slate-100"
        >
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                No.
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Nama Lengkap
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Email
                            </th>
                            <th
                                class="px-8 py-6 text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Role
                            </th>
                            <th
                                class="px-8 py-6 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase"
                            >
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr v-if="users.data.length === 0">
                            <td
                                colspan="5"
                                class="px-8 py-12 text-center font-medium text-slate-400"
                            >
                                Belum ada data user.
                            </td>
                        </tr>
                        <tr
                            v-for="(user, index) in users.data"
                            :key="user.id"
                            class="group transition hover:bg-slate-50/50"
                        >
                            <td
                                class="px-8 py-5 text-sm font-bold text-slate-400"
                            >
                                {{
                                    String(getRowNumber(index)).padStart(2, '0')
                                }}
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-lg"
                                    >
                                        👤
                                    </div>
                                    <span class="font-bold text-slate-700">{{
                                        user.name
                                    }}</span>
                                </div>
                            </td>
                            <td
                                class="px-8 py-5 text-sm font-medium text-slate-600"
                            >
                                {{ user.email }}
                            </td>
                            <td class="px-8 py-5">
                                <span
                                    v-if="user.role === 'admin'"
                                    class="inline-flex items-center rounded-lg border border-indigo-100 bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-600"
                                >
                                    🛡️ Admin
                                </span>
                                <span
                                    v-else-if="user.role === 'security'"
                                    class="inline-flex items-center rounded-lg border border-emerald-100 bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600"
                                >
                                    👮 Security
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <div class="flex justify-center gap-2">
                                    <button
                                        @click="openEditModal(user)"
                                        class="rounded-xl bg-blue-50 p-2 text-blue-600 transition hover:bg-blue-100"
                                        title="Edit"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                            ></path>
                                        </svg>
                                    </button>
                                    <button
                                        @click="deleteUser(user.id)"
                                        class="rounded-xl bg-rose-50 p-2 text-rose-600 transition hover:bg-rose-100"
                                        title="Hapus"
                                    >
                                        <svg
                                            class="h-4 w-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            ></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PAGINATION -->
        <div
            class="mt-6 flex justify-center gap-2"
            v-if="users.links.length > 3"
        >
            <Link
                v-for="(link, k) in users.links"
                :key="k"
                :href="link.url ?? '#'"
                v-html="link.label"
                class="flex h-10 min-w-[40px] items-center justify-center rounded-xl text-xs font-bold shadow-sm transition-all"
                :class="
                    link.active
                        ? 'scale-105 bg-indigo-600 text-white'
                        : 'border border-slate-100 bg-white text-slate-500 hover:bg-slate-50'
                "
            />
        </div>

        <!-- MODAL FORM (ADD/EDIT) -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm transition-opacity"
        >
            <div
                class="w-full max-w-md overflow-hidden rounded-[2.5rem] bg-white p-8 shadow-2xl ring-1 ring-slate-100"
            >
                <div class="mb-6 text-center">
                    <h3 class="text-xl font-black text-slate-800">
                        {{ isEditing ? 'Edit User' : 'Tambah User Baru' }}
                    </h3>
                    <p class="text-sm text-slate-500">
                        Kelola akun akses sistem.
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Nama -->
                    <div>
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Nama Lengkap</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="John Doe"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-3.5 font-bold text-slate-700 transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                            required
                        />
                        <p
                            v-if="form.errors.name"
                            class="mt-1 text-xs text-rose-500"
                        >
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Email Login</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="email@contoh.com"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-3.5 font-bold text-slate-700 transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                            required
                        />
                        <p
                            v-if="form.errors.email"
                            class="mt-1 text-xs text-rose-500"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Role -->
                    <div>
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                            >Role / Jabatan</label
                        >
                        <div class="flex gap-4">
                            <label class="flex-1 cursor-pointer">
                                <input
                                    type="radio"
                                    v-model="form.role"
                                    value="admin"
                                    class="peer hidden"
                                />
                                <div
                                    class="flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 py-3 font-bold text-slate-500 transition-all peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:text-indigo-600"
                                >
                                    🛡️ Admin
                                </div>
                            </label>
                            <label class="flex-1 cursor-pointer">
                                <input
                                    type="radio"
                                    v-model="form.role"
                                    value="security"
                                    class="peer hidden"
                                />
                                <div
                                    class="flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 py-3 font-bold text-slate-500 transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-600"
                                >
                                    👮 Security
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label
                            class="mb-2 block text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                        >
                            Password
                            {{
                                isEditing ? '(Kosongkan jika tidak diubah)' : ''
                            }}
                        </label>
                        <input
                            v-model="form.password"
                            type="password"
                            placeholder="********"
                            class="w-full rounded-2xl border-slate-200 bg-slate-50 px-5 py-3.5 font-bold text-slate-700 transition-all focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/20"
                            :required="!isEditing"
                        />
                        <p
                            v-if="form.errors.password"
                            class="mt-1 text-xs text-rose-500"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-4">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="flex-1 rounded-2xl py-3.5 font-bold text-slate-400 transition-colors hover:bg-slate-50"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 rounded-2xl bg-indigo-600 py-3.5 font-bold text-white shadow-lg shadow-indigo-200 transition-all hover:bg-indigo-700 disabled:opacity-70"
                        >
                            {{ isEditing ? 'Simpan Perubahan' : 'Buat User' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
