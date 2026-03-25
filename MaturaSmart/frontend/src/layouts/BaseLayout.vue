<script setup>
import BaseHeader from './BaseHeader.vue'
import GlobalNotification from '../components/GlobalNotification.vue'

const props = defineProps({
  mode: {
    type: String,
    default: 'app' // 'landing' vagy 'app'
  }
})
</script>

<template>
  <div class="layout-wrapper min-h-screen flex flex-col relative bg-[#020617] text-white selection:bg-blue-500/30 font-sans">
    
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-b from-[#0b1029] via-[#05091a] to-[#020617]"></div>
        <div class="absolute inset-0 opacity-[0.15]" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg');"></div>
        <div class="absolute bottom-0 left-0 right-0 h-96 bg-blue-900/10 blur-[100px]"></div>
    </div>

    <GlobalNotification />

    <BaseHeader v-if="props.mode !== 'landing'" :mode="props.mode" />

    <main class="flex-grow relative z-10 flex flex-col w-full" :class="props.mode !== 'landing' ? 'pt-12' : 'pt-5'">
      <slot />
    </main>

    <footer class="text-center py-8 text-xs font-medium text-slate-500 relative z-10 border-t border-white/5 bg-[#020617]/80 mt-auto backdrop-blur-sm">
      <p>&copy; {{ new Date().getFullYear() }} MaturaSmart – A jövő érettségije</p>
    </footer>

  </div>
</template>