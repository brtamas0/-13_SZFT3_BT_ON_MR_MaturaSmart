<script setup>
// Nincsenek prop-ok, mert minden formázást az aloldalak kezelnek
</script>

<template>
  <div class="layout-wrapper min-h-screen flex flex-col relative bg-[#020617] text-white selection:bg-blue-500/30">
    
    <div class="fixed inset-0 z-5 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-b from-[#0b1029] via-[#05091a] to-[#020617]"></div>
        
        <div class="absolute inset-0 opacity-[0.15]" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg');"></div>
        
        <div class="absolute bottom-0 left-0 right-0 h-96 bg-blue-900/10 blur-[100px]"></div>
    </div>

    <main class="flex-grow relative z-10 flex flex-col w-full">
      <slot />
    </main>

    <footer class="text-center py-8 text-xs font-medium text-slate-500 relative z-5 border-t border-white/5 bg-[#020617]/50 mt-auto">
      <p>&copy; {{ new Date().getFullYear() }} MaturaSmart – A jövő érettségije</p>
    </footer>

  </div>
</template>