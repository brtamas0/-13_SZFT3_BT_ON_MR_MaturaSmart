<script setup>
import { ref, onMounted } from 'vue'
import { RouterView, RouterLink, useRoute } from 'vue-router'
const route = useRoute()
const isActive = (path) => route.path === path
const theme = ref('dark')

onMounted(() => {
  theme.value = localStorage.getItem('theme') || 'dark'
  document.documentElement.setAttribute('data-theme', theme.value)
})

const toggleTheme = () => {
  theme.value = theme.value === 'dark' ? 'light' : 'dark'
  document.documentElement.setAttribute('data-theme', theme.value)
  localStorage.setItem('theme', theme.value)
}
</script>

<template>
  <div class="min-h-screen bg-[#0b102e] text-white flex font-sans"> 
    <aside class="w-64 bg-[#06091a] border-r border-white/5 flex flex-col fixed h-full z-40">
      <div class="p-6 border-b border-white/5">
        <div class="flex items-center justify-between gap-3">
          <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
            AdminPanel
          </h1>
          <button @click="toggleTheme" class="px-2.5 py-1.5 rounded-lg text-xs font-bold border border-white/10 hover:bg-white/10 transition">
            {{ theme === 'dark' ? '🌙' : '☀️' }}
          </button>
        </div>
      </div>
      
      <nav class="flex-1 p-4 space-y-2">
        <RouterLink to="/admin" class="nav-item" :class="{ 'active': isActive('/admin') }">
           📊 Vezérlőpult
        </RouterLink>
        <RouterLink to="/admin/subjects" class="nav-item" :class="{ 'active': isActive('/admin/subjects') }">
           📚 Tantárgyak
        </RouterLink>
        <RouterLink to="/admin/users" class="nav-item" :class="{ 'active': isActive('/admin/users') }">
           👥 Felhasználók
        </RouterLink>
      </nav>

      <div class="p-4 border-t border-white/5">
        <RouterLink to="/main" class="flex items-center gap-2 text-gray-400 hover:text-white transition text-sm">
          ← Vissza az oldalra
        </RouterLink>
      </div>
    </aside>

    <main class="flex-1 ml-64 p-8">
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border-radius: 8px;
    color: #94a3b8;
    transition: all 0.2s;
    font-weight: 500;
}
.nav-item:hover {
    background: rgba(255, 255, 255, 0.05);
    color: white;
}
.nav-item.active {
    background: linear-gradient(90deg, rgba(59, 130, 246, 0.1), transparent);
    color: #60a5fa;
    border-left: 3px solid #3b82f6;
}
</style>
