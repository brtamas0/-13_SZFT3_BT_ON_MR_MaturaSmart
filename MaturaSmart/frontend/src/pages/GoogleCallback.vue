<script setup>
import { onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

onMounted(() => {
  // Kivesszük az adatokat az URL-ből
  const token = route.query.token
  const userJson = route.query.user

  if (token && userJson) {
    try {
      // Token mentése
      localStorage.setItem('token', token)
      
      // User adat mentése (mivel JSON stringként jött, nem kell stringify)
      localStorage.setItem('user', userJson)
      
      // Átirányítás a vezérlőpultra
      // Késleltetés a jobb megjelenítésért
      setTimeout(() => {
          router.push('/main')
      }, 500)
      
    } catch (e) {
      console.error('Hiba a Google adatok feldolgozásakor:', e)
      router.push('/login?error=data_parse_error')
    }
  } else {
    // Ha nincs token, visszadobjuk a loginra
    router.push('/login?error=no_token')
  }
})
</script>

<template>
  <div class="min-h-screen bg-[#020617] flex items-center justify-center text-white">
    <div class="text-center p-8 bg-[#0f172a] border border-white/10 rounded-3xl shadow-2xl">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-500 mx-auto mb-6"></div>
      <h2 class="text-2xl font-bold mb-2">Bejelentkezés...</h2>
      <p class="text-slate-400">Kérlek várj, amíg feldolgozzuk az adataidat.</p>
    </div>
  </div>
</template>