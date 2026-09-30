<script setup lang="ts">
import { Image, PlayCircle } from 'lucide-vue-next';

defineProps<{
    image: string;
    title: string;
    category: string;
}>();
</script>

<template>
    <!-- Gunakan div, jangan Link, agar tidak pindah halaman -->
    <div class="group relative bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 cursor-pointer">
        
        <!-- Media Container -->
        <div class="relative aspect-[4/3] overflow-hidden bg-gray-200">
            
            <!-- LOGIKA: JIKA FOTO -->
            <img 
                v-if="category === 'FOTO'"
                :src="image" 
                :alt="title"
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            />

            <!-- LOGIKA: JIKA VIDEO (Pakai trik #t=0.1 supaya muncul thumbnail) -->
            <video 
                v-else
                :src="image + '#t=0.1'"
                preload="metadata"
                muted
                playsinline
                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            ></video>
            
            <!-- Overlay Gelap saat Hover -->
            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center justify-center">
                <div class="p-3 bg-white/20 backdrop-blur-md rounded-full border border-white/30 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                    <!-- Icon beda antara foto/video -->
                    <PlayCircle v-if="category !== 'FOTO'" class="w-8 h-8 text-white" />
                    <Image v-else class="w-8 h-8 text-white" />
                </div>
            </div>

            <!-- Badge Kategori -->
            <div class="absolute top-4 left-4 z-10">
                <span 
                    class="px-3 py-1 text-[10px] font-bold tracking-widest uppercase rounded-lg backdrop-blur-md border shadow-sm"
                    :class="category === 'FOTO' 
                        ? 'bg-emerald-500 text-white border-emerald-400' 
                        : 'bg-blue-600 text-white border-blue-400'"
                >
                    {{ category }}
                </span>
            </div>
        </div>

        <!-- Title Content -->
        <div class="p-5">
            <h3 class="text-gray-800 font-semibold line-clamp-2 group-hover:text-orange-600 transition-colors duration-300">
                {{ title }}
            </h3>
        </div>
    </div>
</template>