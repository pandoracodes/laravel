<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';

import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, GraduationCap, FileText, Image, Calendar } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: "/admin/dashboard",
        icon: LayoutGrid,
    },
    {
        title: 'Peserta',
        href: '/admin/peserta',
        icon: Users,
    },
    {
        title: 'Program Kursus',
        href: '/admin/program',
        icon: GraduationCap,
    },
    {
        title: 'Artikel',
        href: '/admin/artikel',
        icon: FileText,
    },
     {
        title: 'Kategori',
        href: '/admin/kategori',
        icon: FileText,
    },
    {
        title: 'Galeri',
        href: '/admin/galeri',
        icon: Image,
    },

  
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="'/admin/dashboard'">
                            <!-- PENGGANTI LOGO -->
                            <div class="flex items-center gap-3">
                                <!-- Fotonya di sini -->
                                <div class="flex aspect-square size-8 items-center justify-center rounded-lg overflow-hidden">
                                    <img 
                                        src="/image/Logo Elsam.png" 
                                        alt="Logo LKP ELSAM" 
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                                <!-- Teks Nama Aplikasi (Akan tersembunyi saat sidebar dikecilkan) -->
                                <div class="grid flex-1 text-left text-sm leading-tight group-data-[collapsible=icon]:hidden">
                                    <span class="truncate font-bold text-gray-900">LKP ELSAM</span>
                                    <span class="truncate text-[10px] text-gray-500 font-medium uppercase tracking-wider">Administrator</span>
                                </div>
                            </div>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>