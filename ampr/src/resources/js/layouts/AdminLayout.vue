<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// State untuk dropdown profil
const isProfileOpen = ref(false);

const menus = [
    {
        name: 'Dashboard',
        route: 'admin.dashboard',
        icon: 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z',
    },
    {
        name: 'Users',
        route: 'admin.users.index',
        icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    },
    {
        name: 'Resident',
        route: 'admin.units.index',
        icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m8-2a2 2 0 11-4 0 2 2 0 014 0z',
    },
    {
        name: 'Sanctions',
        route: 'admin.sanctions.index',
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
    },
    {
        name: 'Maintenance',
        route: 'admin.maintenance.index',
        icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    },
    {
        name: 'Laporan',
        route: 'admin.reports.index',
        icon: 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    },
];
</script>

<template>
    <!-- Background Gradient Soft -->
    <div
        class="flex min-h-screen bg-gradient-to-br from-[#EAEFF5] via-[#E8F1F2] to-[#DDE6ED] font-sans text-[#2D3A4B]"
    >
        <!-- SIDEBAR -->
        <aside class="fixed z-30 hidden h-full w-64 flex-col p-4 md:flex">
            <!-- Glassmorphism Sidebar Container -->
            <div
                class="flex flex-1 flex-col overflow-hidden rounded-[2rem] border border-white/60 bg-white/40 shadow-xl backdrop-blur-xl"
            >
                <!-- Brand -->
                <div class="p-8 pb-4">
                    <h1
                        class="text-2xl font-black tracking-tight text-[#1A5F7A]"
                    >
                        Mediterania Court
                    </h1>
                    <p
                        class="mt-1 text-[10px] font-bold tracking-widest text-slate-400 uppercase"
                    >
                        Management
                    </p>
                </div>

                <!-- Menu -->
                <nav class="mt-4 flex-1 space-y-1 px-4">
                    <Link
                        v-for="menu in menus"
                        :key="menu.name"
                        :href="route(menu.route)"
                        class="group relative flex items-center gap-4 overflow-hidden rounded-2xl px-5 py-4 transition-all duration-300"
                        :class="
                            route().current(menu.route)
                                ? 'bg-[#1A5F7A] text-white shadow-lg shadow-[#1A5F7A]/30'
                                : 'text-[#5A6A85] hover:bg-white/50 hover:text-[#1A5F7A]'
                        "
                    >
                        <!-- Glow Effect for Active -->
                        <div
                            v-if="route().current(menu.route)"
                            class="absolute top-0 bottom-0 left-0 w-1 bg-[#57C5B6]"
                        ></div>

                        <svg
                            class="h-5 w-5 transition-transform group-hover:scale-110"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                :d="menu.icon"
                            ></path>
                        </svg>
                        <span class="text-sm font-bold tracking-wide">{{
                            menu.name
                        }}</span>
                    </Link>
                </nav>
            </div>
        </aside>

        <!-- CONTENT AREA -->
        <div class="flex-1 overflow-y-auto p-4 md:ml-64 md:p-8">
            <!-- HEADER (With Profile & Logout) -->
            <header class="mb-8 flex items-center justify-between px-2">
                <!-- Left Side: Title -->
                <div>
                    <h2 class="text-3xl font-black text-[#1A5F7A]">
                        <slot name="header" />
                    </h2>
                    <p class="mt-1 text-sm font-medium text-[#5A6A85]">
                        Welcome back, {{ user.name }} 👋
                    </p>
                </div>

                <!-- Right Side: Profile Dropdown -->
                <div class="relative z-50">
                    <!-- Profile Button -->
                    <button
                        @click="isProfileOpen = !isProfileOpen"
                        class="flex items-center gap-3 rounded-full border border-white/60 bg-white/40 px-2 py-1.5 shadow-sm backdrop-blur-md transition-all hover:bg-white/60 hover:shadow-md"
                    >
                        <!-- Avatar -->
                        <img
                            :src="`https://ui-avatars.com/api/?name=${user.name}&background=1A5F7A&color=fff`"
                            alt="Admin"
                            class="h-10 w-10 rounded-full border-2 border-white object-cover shadow-sm"
                        />
                        <!-- Text (Hidden on mobile) -->
                        <div class="hidden text-left md:block">
                            <p class="text-xs font-bold text-[#1A5F7A]">
                                {{ user.name }}
                            </p>
                            <p class="text-[10px] font-medium text-slate-500">
                                Administrator
                            </p>
                        </div>
                        <!-- Chevron -->
                        <svg
                            class="mr-2 h-4 w-4 text-slate-400 transition-transform duration-300"
                            :class="isProfileOpen ? 'rotate-180' : ''"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M19 9l-7 7-7-7"
                            />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div
                            v-if="isProfileOpen"
                            class="absolute right-0 mt-4 w-56 origin-top-right overflow-hidden rounded-2xl border border-white/60 bg-white/90 p-2 shadow-xl ring-1 ring-black/5 backdrop-blur-xl"
                        >
                            <div
                                class="mb-2 border-b border-gray-100 px-3 py-2"
                            >
                                <p class="text-[10px] font-bold text-slate-400">
                                    SIGNED IN AS
                                </p>
                                <p
                                    class="truncate text-sm font-bold text-[#1A5F7A]"
                                >
                                    {{ user.email }}
                                </p>
                            </div>

                            <!-- Logout Link -->
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex w-full items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-bold text-rose-500 transition-colors hover:bg-rose-50"
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
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    />
                                </svg>
                                Log Out
                            </Link>
                        </div>
                    </transition>

                    <!-- Click Outside Overlay -->
                    <div
                        v-if="isProfileOpen"
                        @click="isProfileOpen = false"
                        class="fixed inset-0 z-[-1] cursor-default"
                    ></div>
                </div>
            </header>

            <main class="space-y-6">
                <slot />
            </main>
        </div>
    </div>
</template>
