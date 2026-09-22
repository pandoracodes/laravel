<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    unit: Object,
    userUnit: String,
});

// --- STATE PIN MODAL ---
const showPinModal = ref(false);
const form = useForm({
    current_pin: '',
    new_pin: '',
    new_pin_confirmation: '',
});

const submitPin = () => {
    form.put(route('resident.update-pin'), {
        onSuccess: () => {
            showPinModal.value = false;
            form.reset();
            // Optional: Tambahkan toast notification disini
        },
        onError: () => {
            form.reset('current_pin', 'new_pin', 'new_pin_confirmation');
        },
    });
};

// --- LOGOUT ---
const logout = () => {
    router.post(route('logout'));
};

// --- RULES DATA ---
const rules = [
    {
        title: 'Weekly Quota',
        desc: 'Max 2 hours/week per unit. Resets every Monday.',
        icon: 'clock',
    },
    {
        title: 'Late Check-in',
        desc: 'Max 15 mins late. After that, ticket is void (No Show).',
        icon: 'exclamation',
    },
    {
        title: 'Ban Policy',
        desc: 'No Show = Automatic 7-day Ban from booking system.',
        icon: 'ban',
    },
    {
        title: 'Attire & Cleanliness',
        desc: 'Sports shoes required. No food/smoking in court area.',
        icon: 'trash',
    },
];
</script>

<template>
    <Head title="Info & Profile" />

    <div
        class="flex min-h-screen justify-center bg-[#EAEFF5] font-sans text-[#1A5F7A] antialiased"
    >
        <div
            class="relative flex min-h-screen w-full max-w-[480px] flex-col overflow-hidden bg-[#F8FAFC] shadow-2xl"
        >
            <!-- 1. HEADER (Curved Deep Teal) -->
            <header
                class="relative z-10 overflow-hidden rounded-b-[3rem] bg-[#1A5F7A] px-6 pt-12 pb-20 shadow-xl"
            >
                <!-- Abstract Pattern -->
                <svg
                    class="pointer-events-none absolute -top-6 -right-6 h-48 w-48 rotate-12 text-[#BEF264] opacity-[0.08]"
                    fill="currentColor"
                    viewBox="0 0 24 24"
                >
                    <circle cx="12" cy="12" r="10" />
                    <path
                        d="M2 12 C 7 12 11 8 11 2"
                        fill="none"
                        stroke="#155E75"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        opacity="0.6"
                    />
                    <path
                        d="M22 12 C 17 12 13 16 13 22"
                        fill="none"
                        stroke="#155E75"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        opacity="0.6"
                    />
                </svg>

                <div
                    class="relative z-10 ml-7 flex items-start justify-between"
                >
                    <div>
                        <p
                            class="mb-1 text-[10px] font-bold tracking-[0.2em] text-[#BEF264] uppercase"
                        >
                            Mediterania Court
                        </p>
                        <h1
                            class="text-3xl font-black tracking-tight text-white"
                        >
                            Profile
                        </h1>
                    </div>
                </div>
            </header>

            <!-- 2. CONTENT -->
            <main
                class="no-scrollbar relative z-20 -mt-12 flex-1 overflow-y-auto px-5 pb-32"
            >
                <!-- HERO CARD (Floating) -->
                <div
                    class="group relative mb-6 overflow-hidden rounded-[2rem] border border-slate-100 bg-white p-6 shadow-xl shadow-slate-200/50"
                >
                    <div
                        class="absolute top-0 right-0 h-20 w-20 rounded-bl-[4rem] bg-[#BEF264]/20 transition-transform group-hover:scale-110"
                    ></div>

                    <div class="flex items-center gap-5">
                        <div
                            class="flex h-16 w-16 items-center justify-center rounded-full bg-[#1A5F7A] text-3xl text-white shadow-lg"
                        >
                            🏢
                        </div>
                        <div>
                            <p
                                class="mb-1 text-[10px] font-bold tracking-wider text-slate-400 uppercase"
                            >
                                UNIT NUMBER
                            </p>
                            <h2
                                class="text-3xl leading-none font-black text-[#1A5F7A]"
                            >
                                {{ unit.unit_number }}
                            </h2>
                            <p class="mt-1 text-xs font-bold text-[#1A5F7A]/60">
                                {{ unit.owner_name || 'Resident' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- MENU: CHANGE PIN -->
                <button
                    @click="showPinModal = true"
                    class="group mb-6 flex w-full items-center justify-between rounded-[1.5rem] border border-slate-100 bg-white p-4 shadow-sm transition-all hover:border-[#1A5F7A]/30 active:scale-[0.98]"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EAEFF5] text-[#1A5F7A] transition-colors group-hover:bg-[#1A5F7A] group-hover:text-[#BEF264]"
                        >
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                ></path>
                            </svg>
                        </div>
                        <div class="text-left">
                            <h4 class="text-sm font-bold text-[#1A5F7A]">
                                Access PIN
                            </h4>
                            <p class="text-[10px] text-slate-400">
                                Change your security code
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-[#F1F5F9] text-slate-400 transition-colors group-hover:bg-[#BEF264] group-hover:text-[#1A5F7A]"
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
                                d="M9 5l7 7-7 7"
                            ></path>
                        </svg>
                    </div>
                </button>

                <!-- RULES LIST -->
                <h3
                    class="mb-4 ml-2 text-xs font-black tracking-widest text-slate-400 uppercase"
                >
                    Court Rules
                </h3>
                <div class="mb-8 space-y-3">
                    <div
                        v-for="(rule, idx) in rules"
                        :key="idx"
                        class="flex items-start gap-4 rounded-[1.5rem] border border-slate-100 bg-white p-4"
                    >
                        <div
                            class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#EAEFF5] text-[#1A5F7A]"
                        >
                            <svg
                                v-if="rule.icon === 'clock'"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                ></path>
                            </svg>
                            <svg
                                v-if="rule.icon === 'exclamation'"
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                ></path>
                            </svg>
                            <svg
                                v-if="rule.icon === 'ban'"
                                class="h-5 w-5 text-red-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"
                                ></path>
                            </svg>
                            <svg
                                v-if="rule.icon === 'trash'"
                                class="h-5 w-5"
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
                        </div>
                        <div>
                            <h4 class="mb-0.5 text-sm font-bold text-[#1A5F7A]">
                                {{ rule.title }}
                            </h4>
                            <p class="text-xs leading-relaxed text-slate-500">
                                {{ rule.desc }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- LOGOUT BUTTON -->
                <button
                    @click="logout"
                    class="flex w-full items-center justify-center gap-2 rounded-[1.5rem] border border-red-100 bg-white py-4 font-bold text-red-500 shadow-sm transition-colors hover:bg-red-50"
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
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                        ></path>
                    </svg>
                    Sign Out
                </button>

                <p
                    class="mt-6 text-center text-[10px] font-bold tracking-widest text-slate-300 uppercase"
                >
                    AMPR System v1.0
                </p>
            </main>

            <!-- 3. BOTTOM NAV (Floating) -->
            <div
                class="pointer-events-none fixed right-0 bottom-8 left-0 z-30 flex justify-center"
            >
                <nav
                    class="pointer-events-auto flex w-[85%] max-w-[340px] items-center justify-around rounded-full bg-white px-2 py-2 shadow-[0_8px_30px_rgb(0,0,0,0.12)]"
                >
                    <!-- HOME -->
                    <Link
                        href="/booking"
                        class="group flex flex-1 flex-col items-center gap-1"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full transition-colors"
                        >
                            <svg
                                class="h-6 w-6 text-[#1A5F7A] transition-colors"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                                ></path>
                            </svg>
                        </div>
                        <span class="-mt-1 text-[9px] font-bold text-[#1A5F7A]"
                            >Home</span
                        >
                    </Link>

                    <!-- TICKET -->
                    <Link
                        href="/my-tickets"
                        class="group flex flex-1 flex-col items-center gap-1"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full transition-colors"
                        >
                            <svg
                                class="h-6 w-6 text-[#1A5F7A] transition-colors"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"
                                ></path>
                            </svg>
                        </div>
                        <span class="-mt-1 text-[9px] font-bold text-[#1A5F7A]"
                            >Ticket</span
                        >
                    </Link>

                    <!-- PROFILE (Active) -->
                    <Link
                        href="/info"
                        class="group flex flex-1 flex-col items-center gap-1"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-[#1A5F7A] shadow-lg shadow-[#1A5F7A]/30 transition-transform group-active:scale-95"
                        >
                            <svg
                                class="h-5 w-5 text-[#BEF264]"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <span class="-mt-1 text-[9px] font-bold text-[#1A5F7A]"
                            >Profile</span
                        >
                    </Link>
                </nav>
            </div>

            <!-- MODAL GANTI PIN -->
            <transition name="modal">
                <div
                    v-if="showPinModal"
                    class="fixed inset-0 z-[60] flex items-end justify-center p-0 sm:items-center sm:p-4"
                >
                    <div
                        @click="showPinModal = false"
                        class="absolute inset-0 bg-[#1A5F7A]/80 backdrop-blur-sm"
                    ></div>

                    <div
                        class="animate-slide-up relative w-full max-w-[480px] rounded-t-[2.5rem] bg-white p-8 shadow-2xl sm:rounded-[2.5rem]"
                    >
                        <div
                            class="mx-auto mb-6 h-1.5 w-12 rounded-full bg-slate-200"
                        ></div>

                        <h2 class="mb-6 text-2xl font-black text-[#1A5F7A]">
                            Change PIN
                        </h2>

                        <form @submit.prevent="submitPin" class="space-y-5">
                            <!-- Field Old PIN -->
                            <div>
                                <label
                                    class="mb-2 ml-1 block text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                                    >Current PIN</label
                                >
                                <input
                                    v-model="form.current_pin"
                                    type="password"
                                    class="w-full rounded-2xl border-none bg-[#F1F5F9] px-5 py-4 font-bold text-[#1A5F7A] placeholder-slate-400 transition-all focus:ring-2 focus:ring-[#BEF264]"
                                    placeholder="••••"
                                />
                                <p
                                    v-if="form.errors.current_pin"
                                    class="mt-1 ml-1 text-xs font-bold text-red-500"
                                >
                                    {{ form.errors.current_pin }}
                                </p>
                            </div>

                            <!-- Field New PIN -->
                            <div>
                                <label
                                    class="mb-2 ml-1 block text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                                    >New PIN</label
                                >
                                <input
                                    v-model="form.new_pin"
                                    type="password"
                                    class="w-full rounded-2xl border-none bg-[#F1F5F9] px-5 py-4 font-bold text-[#1A5F7A] placeholder-slate-400 transition-all focus:ring-2 focus:ring-[#BEF264]"
                                    placeholder="••••"
                                />
                                <p
                                    v-if="form.errors.new_pin"
                                    class="mt-1 ml-1 text-xs font-bold text-red-500"
                                >
                                    {{ form.errors.new_pin }}
                                </p>
                            </div>

                            <!-- Field Confirm PIN -->
                            <div>
                                <label
                                    class="mb-2 ml-1 block text-[11px] font-bold tracking-wider text-slate-400 uppercase"
                                    >Confirm New PIN</label
                                >
                                <input
                                    v-model="form.new_pin_confirmation"
                                    type="password"
                                    class="w-full rounded-2xl border-none bg-[#F1F5F9] px-5 py-4 font-bold text-[#1A5F7A] placeholder-slate-400 transition-all focus:ring-2 focus:ring-[#BEF264]"
                                    placeholder="••••"
                                />
                            </div>

                            <button
                                :disabled="form.processing"
                                class="mt-2 w-full rounded-2xl bg-[#1A5F7A] py-4 text-lg font-bold text-white shadow-xl shadow-[#1A5F7A]/30 transition-all hover:bg-[#164e63] active:scale-[0.98] disabled:opacity-70"
                            >
                                {{
                                    form.processing ? 'Saving...' : 'Update PIN'
                                }}
                            </button>
                        </form>
                    </div>
                </div>
            </transition>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
@keyframes slide-up {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}
.animate-slide-up {
    animation: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
