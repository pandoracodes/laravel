<script setup>
import LoginIllustration from '@/assets/login-tennis.png';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const showLoginForm = ref(false);
const showPassword = ref(false);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Login AMPR" />

    <div
        class="flex min-h-screen justify-center overflow-hidden bg-[#1A5F7A] font-sans antialiased"
    >
        <div
            class="relative flex h-full min-h-screen w-full max-w-[480px] flex-col bg-[#1A5F7A] shadow-2xl"
        >
            <!-- HALAMAN 1: WELCOME (Tetap sama, saya skip agar fokus ke perbaikan Login Form) -->
            <transition name="fade">
                <div
                    v-if="!showLoginForm"
                    class="absolute inset-0 z-10 flex flex-col items-center justify-between bg-[#1A5F7A] p-8 pb-12"
                >
                    <div
                        class="absolute top-[-60px] right-[-60px] h-64 w-64 rounded-full bg-[#BEF264] opacity-20 blur-[80px]"
                    ></div>
                    <div
                        class="absolute bottom-20 left-[-40px] h-48 w-48 rounded-full bg-[#BEF264] opacity-10 blur-[60px]"
                    ></div>

                    <div
                        class="relative mt-10 flex max-h-[45vh] w-full items-center justify-center"
                    >
                        <div
                            class="relative flex w-full max-w-md items-center justify-center"
                        >
                            <div
                                class="absolute inset-0 scale-90 animate-pulse rounded-full bg-white/5 blur-3xl"
                            ></div>
                            <img
                                :src="LoginIllustration"
                                class="relative z-10 w-full object-contain drop-shadow-2xl transition duration-500"
                                alt="Illustration"
                            />
                        </div>
                    </div>

                    <div class="relative z-20 w-full space-y-8 text-center">
                        <div class="space-y-1">
                            <p
                                class="text-[10px] font-bold tracking-[0.25em] text-[#BEF264] uppercase opacity-90"
                            >
                                Welcome to
                            </p>
                            <h1
                                class="text-4xl leading-none font-black tracking-tight text-white"
                            >
                                Mediterania <br /><span class="text-[#BEF264]"
                                    >Court</span
                                >
                            </h1>
                            <p
                                class="mt-4 px-6 text-sm leading-relaxed font-light text-slate-300"
                            >
                                A smart solution for easy and comfortable tennis
                                court booking.
                            </p>
                        </div>
                        <div class="-mt-4 space-y-4 px-4">
                            <button
                                @click="showLoginForm = true"
                                class="w-full transform rounded-[2rem] bg-[#BEF264] py-4 text-lg font-black text-[#1A5F7A] shadow-xl shadow-[#BEF264]/20 transition hover:scale-[1.02] hover:bg-[#aacc50] active:scale-95"
                            >
                                Log In
                            </button>
                        </div>
                        <p
                            class="mx-auto max-w-xs text-[10px] leading-tight text-slate-400 opacity-70"
                        >
                            By continuing, you agree to our
                            <span
                                class="text-[#BEF264] underline decoration-1 underline-offset-2"
                                >Terms</span
                            >
                            &
                            <span
                                class="text-[#BEF264] underline decoration-1 underline-offset-2"
                                >Privacy Policy</span
                            >.
                        </p>
                    </div>
                </div>
            </transition>

            <!-- HALAMAN 2: LOGIN FORM (PERBAIKAN DISINI) -->
            <transition name="slide-up">
                <div
                    v-if="showLoginForm"
                    class="absolute inset-0 z-20 flex h-full flex-col"
                >
                    <!-- BAGIAN ATAS: Header Gambar (Diperbesar & Posisi Gambar Diperbaiki) -->
                    <div
                        class="relative flex h-[40%] w-full items-end justify-center overflow-hidden bg-[#1A5F7A]"
                    >
                        <!-- Tombol Back -->
                        <button
                            @click="showLoginForm = false"
                            class="absolute top-8 left-6 z-30 flex items-center gap-2 rounded-full bg-black/20 px-5 py-2 text-xs font-bold text-white backdrop-blur-md transition hover:bg-black/40"
                        >
                            <svg
                                class="h-3 w-3"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="3"
                                    d="M15 19l-7-7 7-7"
                                ></path>
                            </svg>
                            Back
                        </button>

                        <!-- Gambar (FIX: Menggunakan h-85% dan bottom-0 agar pas di lantai header) -->
                        <img
                            :src="LoginIllustration"
                            class="relative z-10 h-[85%] w-auto object-contain drop-shadow-2xl"
                            alt="Illustration"
                        />

                        <!-- Cahaya Belakang -->
                        <div
                            class="absolute top-10 right-[-30px] h-40 w-40 rounded-full bg-[#BEF264] opacity-30 blur-[80px]"
                        ></div>
                    </div>

                    <!-- BAGIAN BAWAH: Form Card -->
                    <div
                        class="animate-slide-content relative z-20 -mt-10 flex h-[65%] flex-col rounded-t-[3rem] bg-white px-8 pt-12 pb-8 shadow-[0_-20px_60px_rgba(0,0,0,0.3)]"
                    >
                        <div class="mb-8 text-center">
                            <h2
                                class="text-3xl font-black tracking-tight text-[#1A5F7A]"
                            >
                                Welcome back!
                            </h2>
                            <p class="mt-1 text-sm font-medium text-slate-400">
                                Please enter your details.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Input Email -->
                            <div class="space-y-2">
                                <label
                                    class="ml-4 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                    >Email / Unit</label
                                >
                                <input
                                    v-model="form.email"
                                    type="text"
                                    placeholder=""
                                    class="h-14 w-full rounded-[2rem] border-none bg-[#EAEFF5] px-6 font-bold text-[#1A5F7A] placeholder-slate-400 shadow-inner transition-all focus:bg-white focus:ring-2 focus:ring-[#BEF264]"
                                />
                                <p
                                    v-if="form.errors.email"
                                    class="mt-1 ml-4 text-xs font-bold text-red-500"
                                >
                                    {{ form.errors.email }}
                                </p>
                            </div>

                            <!-- Input Password / PIN -->
                            <div class="space-y-2">
                                <label
                                    class="ml-4 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                                >
                                    Password / PIN Akses
                                </label>
                                <div class="relative">
                                    <!-- FIX: type dibuat dinamis menggunakan titik dua ( :type ) -->
                                    <input
                                        v-model="form.password"
                                        :type="
                                            showPassword ? 'text' : 'password'
                                        "
                                        placeholder=""
                                        class="h-14 w-full rounded-[2rem] border-none bg-[#EAEFF5] px-6 font-bold text-[#1A5F7A] placeholder-slate-400 shadow-inner transition-all focus:bg-white focus:ring-2 focus:ring-[#BEF264]"
                                    />

                                    <!-- FIX: Ikon yang bisa diklik (@click) -->
                                    <div
                                        @click="showPassword = !showPassword"
                                        class="absolute top-0 right-0 flex h-14 cursor-pointer items-center pr-6 text-slate-300 transition-colors hover:text-[#1A5F7A]"
                                    >
                                        <!-- Ikon Mata Terbuka (Muncul saat password terlihat) -->
                                        <svg
                                            v-if="showPassword"
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            ></path>
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            ></path>
                                        </svg>
                                        <!-- Ikon Mata Dicoret (Muncul saat password disembunyikan) -->
                                        <svg
                                            v-else
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"
                                            ></path>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Tampilkan error password jika ada -->
                                <p
                                    v-if="form.errors.password"
                                    class="mt-1 ml-4 text-xs font-bold text-red-500"
                                >
                                    {{ form.errors.password }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between px-4">
                                <label
                                    class="flex cursor-pointer items-center text-xs font-bold text-slate-400 transition hover:text-[#1A5F7A]"
                                >
                                    <input
                                        type="checkbox"
                                        v-model="form.remember"
                                        class="mr-2 h-4 w-4 rounded border-slate-300 bg-[#EAEFF5] text-[#1A5F7A] focus:ring-[#1A5F7A]"
                                    />
                                    Remember me
                                </label>
                                <a
                                    href="#"
                                    class="text-xs font-bold text-[#1A5F7A] transition hover:text-[#BEF264] hover:underline"
                                    >Forgot password?</a
                                >
                            </div>

                            <!-- Tombol Login (FIX: Menggunakan h-16 agar tebal/kokoh) -->
                            <button
                                :disabled="form.processing"
                                class="mt-4 flex h-16 w-full items-center justify-center gap-2 rounded-[2rem] bg-[#1A5F7A] text-lg font-bold text-white shadow-xl shadow-[#1A5F7A]/30 transition-all hover:bg-[#154c61] active:scale-95 disabled:opacity-70"
                            >
                                <svg
                                    v-if="form.processing"
                                    class="h-5 w-5 animate-spin text-white"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                                <span v-else>Log In</span>
                            </button>
                        </form>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-up-enter-active {
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-leave-active {
    transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.slide-up-enter-from {
    transform: translateY(100%);
}
.slide-up-leave-to {
    transform: translateY(100%);
}

@keyframes slideContent {
    from {
        transform: translateY(40px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
.animate-slide-content {
    animation: slideContent 0.6s ease-out 0.3s backwards;
}
</style>
