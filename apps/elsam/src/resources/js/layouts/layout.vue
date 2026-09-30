<script setup lang="ts">
import Header from  '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <Header/>
    
        <slot />
    <Footer/>
    
</template>
