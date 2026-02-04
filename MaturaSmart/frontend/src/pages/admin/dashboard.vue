<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({ users: 0, subjects: 0, topics: 0 })
const loading = ref(true)

onMounted(async () => {
    const token = localStorage.getItem('token')
    try {
        const res = await fetch('http://backend.maturasmart.hu/api/admin/stats', {
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        if (res.ok) stats.value = await res.json()
    } catch (e) {
        console.error("Hiba:", e)
    } finally {
        loading.value = false
    }
})
</script>

<template>
  <div>
    <h1 class="text-3xl font-bold mb-8 text-white">Vezérlőpult</h1>

    <div v-if="loading" class="text-blue-400 animate-pulse">Adatok betöltése...</div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-[#131b3d] p-6 rounded-2xl border border-gray-800 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">👥</div>
            <h3 class="text-gray-400 text-sm uppercase tracking-wider">Felhasználók</h3>
            <p class="text-4xl font-bold text-white mt-2">{{ stats.users }}</p>
        </div>

        <div class="bg-[#131b3d] p-6 rounded-2xl border border-gray-800 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">📚</div>
            <h3 class="text-gray-400 text-sm uppercase tracking-wider">Tantárgyak</h3>
            <p class="text-4xl font-bold text-white mt-2">{{ stats.subjects }}</p>
        </div>

        <div class="bg-[#131b3d] p-6 rounded-2xl border border-gray-800 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">📄</div>
            <h3 class="text-gray-400 text-sm uppercase tracking-wider">Leckék száma</h3>
            <p class="text-4xl font-bold text-white mt-2">{{ stats.topics }}</p>
        </div>
    </div>
    
    <div class="mt-10 bg-[#131b3d] p-6 rounded-2xl border border-gray-800">
        <h3 class="text-white font-bold mb-4">Gyorsműveletek</h3>
        <div class="flex gap-4">
            <button class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold transition text-sm">
                + Új Tantárgy
            </button>
            <button class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-bold transition text-sm">
                📢 Rendszerüzenet küldése
            </button>
        </div>
    </div>
  </div>
</template>