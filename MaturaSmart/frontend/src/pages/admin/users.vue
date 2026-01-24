<script setup>
import { ref, onMounted } from 'vue'

const users = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
    await fetchUsers()
})

const fetchUsers = async () => {
    loading.value = true
    error.value = null
    const token = localStorage.getItem('token')

    try {
        const res = await fetch('http://backend.vm1.test/api/admin/users', {
            headers: { 
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })

        if (!res.ok) {
            throw new Error(`Hiba: ${res.status}`)
        }

        users.value = await res.json()
    } catch (e) {
        console.error(e)
        error.value = "Nem sikerült betölteni a felhasználókat."
    } finally {
        loading.value = false
    }
}
</script>

<template>
  <div>
    <div class="flex justify-between items-end mb-8 border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Felhasználók</h1>
            <p class="text-gray-400">A rendszerben regisztrált összes fiók kezelése.</p>
        </div>
        <div class="text-right">
            <span class="bg-gray-800 text-gray-300 px-3 py-1 rounded-lg text-sm font-bold border border-gray-700">
                Összesen: {{ users.length }}
            </span>
        </div>
    </div>

    <div v-if="error" class="bg-red-900/50 border border-red-500 text-red-200 p-4 rounded-xl mb-6 flex items-center gap-3">
        <span>⚠️</span>
        <span>{{ error }}</span>
        <button @click="fetchUsers" class="ml-auto bg-red-800 hover:bg-red-700 px-3 py-1 rounded text-sm font-bold">Újrapróbálás</button>
    </div>

    <div v-if="loading" class="text-center py-12">
        <div class="text-blue-400 text-xl font-bold animate-pulse">Adatok betöltése...</div>
    </div>
    </div>
</template>