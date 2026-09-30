<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();

// Mengambil data dari backend
const programKursus = computed(() => page.props.navbar_programs || []);

const menus = ref([
    { name: 'Beranda', url: '/' },
    { name: 'Program', url: '/program-kursus', hasDropdown: true }, 
    { name: 'Artikel', url: '/artikel' },
    { name: 'Galeri', url: '/galeri' },
    { name: 'Tentang Kami', url: '/tentang-kami' },
    { name: 'Hubungi Kami', url: '/hubungi-kami' }, 
]);

// State untuk dropdown desktop
const isDropdownOpen = ref(false);

// State UNTUK MOBILE
const isMobileMenuOpen = ref(false);
const isMobileSubmenuOpen = ref(false); // Untuk dropdown program di mobile
</script>

<template>
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                
                <!-- Logo -->
                <div class="flex items-center">
                    <Link href="/">
                        <img class="h-10 w-auto" src="/image/Logo Elsam.png" alt="Logo ELSAM" />
                    </Link>
                </div>

                <!-- Navigation Links (Desktop - Tersembunyi di Mobile) -->
                <div class="hidden md:flex space-x-8 items-center">
                    <template v-for="(menu, index) in menus" :key="index">
                        
                        <!-- JIKA MENU PROGRAM (DENGAN DROPDOWN) -->
                        <div v-if="menu.hasDropdown" 
                             class="relative h-full flex items-center"
                             @mouseenter="isDropdownOpen = true" 
                             @mouseleave="isDropdownOpen = false">
                            
                            <Link 
                                :href="menu.url"
                                class="flex items-center text-gray-700 hover:text-blue-600 font-medium transition text-sm uppercase tracking-wide h-full border-b-2 border-transparent"
                                :class="{ 'text-blue-600 border-blue-600': $page.url.startsWith('/program-kursus') }"
                            >
                                {{ menu.name }}
                                <svg class="ml-1 h-4 w-4 fill-current transition-transform duration-200" :class="{'rotate-180': isDropdownOpen}" viewBox="0 0 20 20">
                                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                                </svg>
                            </Link>

                            <!-- ISI DROPDOWN DESKTOP -->
                            <transition enter-active-class="..." leave-active-class="...">
                                <div v-show="isDropdownOpen" class="absolute left-0 top-[80px] w-64 bg-white border border-gray-100 rounded-b-lg shadow-xl z-50 overflow-hidden">
                                    <div class="py-1">
                                        <Link href="/program-kursus" class="block px-4 py-2 text-xs font-bold text-gray-400 hover:bg-gray-50 uppercase tracking-widest border-b">
                                            Lihat Semua Program
                                        </Link>
                                        <Link 
                                            v-for="prog in programKursus" 
                                            :key="prog.slug"
                                            :href="`/program-kursus/${prog.slug}`"
                                            class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-600 hover:text-white"
                                        >
                                            {{ prog.nama_program }}
                                        </Link>
                                    </div>
                                </div>
                            </transition>
                        </div>

                        <!-- MENU BIASA (Desktop) -->
                        <Link 
                            v-else
                            :href="menu.url" 
                            class="text-gray-700 hover:text-blue-600 font-medium transition text-sm uppercase tracking-wide border-b-2 border-transparent py-2"
                            :class="{ 'text-blue-600 font-bold border-blue-600': $page.url === menu.url }"
                        >
                            {{ menu.name }}
                        </Link>
                    </template>
                </div>

                <!-- Action Button & Mobile Toggle -->
                <div class="flex items-center space-x-4">
                    <Link 
                        href="/daftar" 
                        class="hidden sm:block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded shadow transition"
                    >
                        Daftar Sekarang
                    </Link>

                    <!-- TOMBOL HAMBURGER (Garis 3) -->
                    <div class="md:hidden flex items-center">
                        <button 
                            @click="isMobileMenuOpen = !isMobileMenuOpen" 
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-blue-600 hover:bg-gray-100 focus:outline-none transition"
                        >
                            <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <!-- Icon Garis 3 saat menu tutup, Icon X saat menu buka -->
                                <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MENU MOBILE PANEL (Akan muncul saat isMobileMenuOpen = true) -->
        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div v-show="isMobileMenuOpen" class="md:hidden bg-white border-t border-gray-100 shadow-inner">
                <div class="px-4 pt-2 pb-6 space-y-1">
                    <template v-for="menu in menus" :key="menu.name">
                        
                        <!-- Menu Program dengan Submenu di Mobile -->
                        <div v-if="menu.hasDropdown">
                            <div class="flex items-center justify-between">
                                <Link 
                                    :href="menu.url" 
                                    class="block py-3 text-base font-semibold text-gray-700 uppercase"
                                    @click="isMobileMenuOpen = false"
                                >
                                    {{ menu.name }}
                                </Link>
                                <!-- Tombol panah untuk buka submenu saja -->
                                <button 
                                    @click="isMobileSubmenuOpen = !isMobileSubmenuOpen"
                                    class="p-2 text-gray-400"
                                >
                                    <svg class="h-5 w-5 transition-transform" :class="{'rotate-180': isMobileSubmenuOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Daftar Program (Mobile Submenu) -->
                            <div v-show="isMobileSubmenuOpen" class="pl-4 space-y-1 bg-gray-50 rounded-lg">
                                <Link 
                                    v-for="prog in programKursus" 
                                    :key="prog.slug"
                                    :href="`/program-kursus/${prog.slug}`"
                                    class="block py-3 px-3 text-sm text-gray-600 border-l-2 border-transparent hover:border-blue-500"
                                    @click="isMobileMenuOpen = false"
                                >
                                    {{ prog.nama_program }}
                                </Link>
                            </div>
                        </div>

                        <!-- Menu Biasa (Mobile) -->
                        <Link 
                            v-else
                            :href="menu.url" 
                            class="block py-3 text-base font-semibold text-gray-700 uppercase"
                            :class="{ 'text-blue-600': $page.url === menu.url }"
                            @click="isMobileMenuOpen = false"
                        >
                            {{ menu.name }}
                        </Link>
                    </template>

                    <!-- Tombol Daftar (Mobile Only) -->
                    <div class="pt-4 border-t border-gray-100 mt-4">
                        <Link 
                            href="/daftar" 
                            class="block w-full text-center bg-blue-500 text-white font-bold py-3 rounded-lg"
                            @click="isMobileMenuOpen = false"
                        >
                            Daftar Sekarang
                        </Link>
                    </div>
                </div>
            </div>
        </transition>
    </nav>
</template>