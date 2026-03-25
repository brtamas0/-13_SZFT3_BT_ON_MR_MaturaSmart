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
  <div class="layout-wrapper min-h-screen flex flex-col relative selection:bg-blue-500/30 font-sans">
    
    <div class="layout-background fixed inset-0 z-0 pointer-events-none">
        <div class="layout-gradient absolute inset-0"></div>
        <div class="layout-noise absolute inset-0"></div>
        <div class="layout-glow absolute bottom-0 left-0 right-0 h-96 blur-[100px]"></div>
    </div>

    <GlobalNotification />

    <BaseHeader v-if="props.mode !== 'landing'" :mode="props.mode" />

    <main class="flex-grow relative z-10 flex flex-col w-full" :class="props.mode !== 'landing' ? 'pt-12' : 'pt-5'">
      <slot />
    </main>

    <footer class="layout-footer text-center py-8 text-xs font-medium relative z-10 mt-auto backdrop-blur-sm">
      <p>&copy; {{ new Date().getFullYear() }} MaturaSmart – A jövő érettségije</p>
    </footer>

  </div>
</template>

<style scoped>
.layout-wrapper {
  background: var(--bg-color);
  color: var(--text-primary);
}

.layout-gradient {
  background: var(--app-background-gradient);
}

.layout-noise {
  opacity: var(--app-noise-opacity);
  background-image: url('https://grainy-gradients.vercel.app/noise.svg');
}

.layout-glow {
  background: var(--app-glow-color);
}

.layout-footer {
  color: var(--text-secondary);
  border-top: 1px solid var(--glass-border);
  background: var(--app-footer-bg);
}
</style>
