<script setup>
import BaseHeader from '@/components/layout/BaseHeader.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col relative overflow-hidden">
    
    <div class="grid-bg"></div>

    <BaseHeader />

    <main class="flex-grow container mx-auto px-4 py-8 relative z-10">
      <slot />
    </main>

    <footer class="text-center py-6 text-sm opacity-60 relative z-10">
      <p>&copy; 2025 MaturaSmart - A jövő érettségije</p>
    </footer>

  </div>
</template>