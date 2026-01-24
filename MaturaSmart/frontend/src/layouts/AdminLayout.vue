<script setup>
import { RouterView, RouterLink, useRoute } from 'vue-router'
const route = useRoute()
const isActive = (path) => route.path === path
</script>

<template>
  <div class="min-h-screen bg-[#0b102e] text-white flex font-sans">
    <aside class="w-64 bg-[#06091a] border-r border-white/5 flex flex-col fixed h-full z-40">
      <div class="p-6 border-b border-white/5">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-indigo-500 bg-clip-text text-transparent">
          AdminPanel
        </h1>
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
