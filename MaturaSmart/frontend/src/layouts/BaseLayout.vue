<script setup>
import BaseHeader from '@/components/layout/BaseHeader.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col relative overflow-hidden bg-gradient-to-b from-[#0b1029] to-[#031625] text-white">

    <BaseHeader />

    <main class="flex-grow max-w-6xl mx-auto px-6 md:px-0 py-10 relative z-10">
      <slot />
    </main>

    <footer class="text-center py-6 text-sm opacity-60 relative z-10">
      <p>&copy; 2025 MaturaSmart – A jövő érettségije</p>
    </footer>

  </div>
</template>
