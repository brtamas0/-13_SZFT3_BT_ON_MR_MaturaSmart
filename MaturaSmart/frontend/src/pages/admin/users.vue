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

    <div v-else class="bg-[#131b3d] border border-gray-800 rounded-2xl overflow-hidden shadow-lg animate-fade-in">
        <table class="w-full text-left border-collapse">
            <thead class="bg-[#0b102e] text-gray-400 uppercase text-xs font-bold tracking-wider">
                <tr>
                    <th class="p-4 border-b border-gray-800">Felhasználó</th>
                    <th class="p-4 border-b border-gray-800">Email Cím</th>
                    <th class="p-4 border-b border-gray-800">Szerepkör</th>
                    <th class="p-4 border-b border-gray-800">Csatlakozott</th>
                </tr>
            </thead>
            <tbody class="text-gray-300 divide-y divide-gray-800">
                <tr v-for="user in users" :key="user.id" class="hover:bg-white/5 transition duration-200 group">
                    
                    <td class="p-4 font-bold text-white flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-sm font-bold text-white shadow-lg shadow-blue-900/50 border border-white/10">
                            {{ user.full_name ? user.full_name.charAt(0).toUpperCase() : '?' }}
                        </div>
                        <div>
                            <div class="text-white">{{ user.full_name }}</div>
                            <div class="text-xs text-gray-500 font-normal">ID: #{{ user.id }}</div>
                        </div>
                    </td>

                    <td class="p-4 text-gray-400 font-mono text-sm">
                        {{ user.email }}
                    </td>

                    <td class="p-4">
                        <span 
                            class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border shadow-sm"
                            :class="user.role === 'admin' 
                                ? 'bg-red-500/10 text-red-400 border-red-500/30 shadow-red-900/20' 
                                : 'bg-blue-500/10 text-blue-400 border-blue-500/30 shadow-blue-900/20'"
                        >
                            {{ user.role === 'admin' ? '🛡️ Admin' : '🎓 Diák' }}
                        </span>
                    </td>

                    <td class="p-4 text-sm text-gray-500">
                        {{ new Date(user.created_at).toLocaleDateString('hu-HU') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div v-if="users.length === 0" class="p-12 text-center text-gray-500">
            Nincsenek felhasználók a rendszerben.
        </div>
    </div>
  </div>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>