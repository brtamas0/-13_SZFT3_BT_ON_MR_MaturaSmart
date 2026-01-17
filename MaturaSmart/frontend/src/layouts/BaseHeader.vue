<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

const props = defineProps({
  mode: {
    type: String,
    default: "app", // landing | app
  },
});

const router = useRouter();
const mobileOpen = ref(false);
const user = ref(null);

// Felhasználó adatainak betöltése
onMounted(() => {
  const storedUser = localStorage.getItem('user');
  const token = localStorage.getItem('token');

  if (token && storedUser) {
    try {
      user.value = JSON.parse(storedUser);
    } catch (e) {
      console.error("Hiba a user adatokban");
    }
  }
});

// Kijelentkezés
const handleLogout = async () => {
  const token = localStorage.getItem('token');
  
  if (token) {
    try {
      await fetch('http://backend.vm1.test/api/logout', {
        method: 'POST',
        headers: { 
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });
    } catch (error) {
      console.error(error);
    }
  }

  // Helyi törlés és átirányítás
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  user.value = null;
  mobileOpen.value = false; // Mobil menü bezárása
  router.push('/login');
};
</script>

<template>
  <header
    class="fixed top-0 left-0 w-full z-50
           backdrop-blur-xl bg-[#0b1029]/80
           border-b border-white/10 shadow-lg"
  >
    <div class="max-w-7xl mx-auto px-6 md:px-10 h-20 flex items-center justify-between">

      <RouterLink to="/" class="text-2xl font-bold tracking-wide flex items-center gap-2">
        <span>🚀</span>
        <span>Matura<span class="text-[#6CA6FF]">Smart</span></span>
      </RouterLink>

      <nav class="hidden md:flex items-center gap-6 text-sm text-gray-300 font-medium">
        
        <template v-if="props.mode === 'landing'">
          <RouterLink to="/#home" class="nav-item">Kezdőlap</RouterLink>
          <RouterLink to="/#features" class="nav-item">Funkciók</RouterLink>
          <RouterLink to="/#mission" class="nav-item">Célunk</RouterLink>
          <RouterLink to="/#faq" class="nav-item">GYIK</RouterLink>
        </template>

        <template v-else>
          <RouterLink
            to="/main"
            class="px-5 py-2 rounded-xl bg-blue-600 text-white shadow-lg shadow-blue-900/50 hover:bg-blue-500 transition hover:-translate-y-0.5"
          >
            Vezérlőpult
          </RouterLink>
          <RouterLink to="/tantargyak" class="nav-item">Tantárgyak</RouterLink>
          <RouterLink to="/calendar" class="nav-item opacity-50 cursor-not-allowed" title="Hamarosan">Naptár 📅</RouterLink>
          <RouterLink to="/profile" class="nav-item">Profil</RouterLink>
        </template>
      </nav>

      <div class="flex items-center gap-4">
        
        <template v-if="props.mode === 'landing'">
          <RouterLink
            to="/login"
            class="bg-[#6CA6FF] text-[#0b1029] font-bold px-5 py-2.5 rounded-xl shadow-md hover:bg-[#8bb8ff] transition text-sm hover:-translate-y-0.5"
          >
            Belépés
          </RouterLink>
        </template>

        <template v-else>
          <div class="hidden sm:flex px-4 py-2 bg-black/40 border border-white/5 rounded-2xl shadow-inner text-sm items-center gap-2 text-white">
            <span class="text-lg">🔥</span> 
            <span class="font-bold">{{ user?.current_streak || 0 }}</span>
          </div>

          <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center font-bold text-white border-2 border-[#0b1029] shadow-md">
            {{ user?.full_name?.charAt(0) || 'U' }}
          </div>

          <button 
            @click="handleLogout" 
            class="hidden md:flex items-center justify-center w-10 h-10 rounded-xl hover:bg-red-500/10 text-gray-400 hover:text-red-400 transition-colors"
            title="Kijelentkezés"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" x2="9" y1="12" y2="12"></line></svg>
          </button>
        </template>

        <button class="md:hidden text-3xl text-gray-300 hover:text-white transition" @click="mobileOpen = !mobileOpen">
          ☰
        </button>
      </div>
    </div>

    <transition name="fade">
      <div
        v-if="mobileOpen"
        class="md:hidden bg-[#0b1029] border-t border-white/10 px-6 py-6 space-y-4 absolute w-full shadow-2xl"
      >
        <template v-if="props.mode === 'landing'">
          <RouterLink to="/#home" class="block nav-item" @click="mobileOpen = false">Kezdőlap</RouterLink>
          <RouterLink to="/#features" class="block nav-item" @click="mobileOpen = false">Funkciók</RouterLink>
          <RouterLink to="/login" class="block nav-item text-[#6CA6FF]" @click="mobileOpen = false">Belépés</RouterLink>
        </template>

        <template v-else>
          <div class="flex items-center gap-3 border-b border-white/10 pb-4 mb-4">
             <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white text-xs">
                {{ user?.full_name?.charAt(0) || 'U' }}
             </div>
             <span class="text-white font-bold">{{ user?.full_name || 'Felhasználó' }}</span>
          </div>

          <RouterLink to="/main" class="block nav-item text-lg" @click="mobileOpen = false">📊 Vezérlőpult</RouterLink>
          <RouterLink to="/tantargyak" class="block nav-item text-lg" @click="mobileOpen = false">📚 Tantárgyak</RouterLink>
          <RouterLink to="/profile" class="block nav-item text-lg" @click="mobileOpen = false">👤 Profil</RouterLink>
          
          <button 
            @click="handleLogout" 
            class="w-full text-left text-red-400 hover:text-red-300 font-bold mt-6 pt-4 border-t border-white/10 flex items-center gap-2"
          >
            🚪 Kijelentkezés
          </button>
        </template>
      </div>
    </transition>
  </header>
</template>

<style scoped>
.nav-item {
  cursor: pointer;
  transition: all 0.2s ease;
  position: relative;
}
.nav-item:hover {
  color: white;
  text-shadow: 0 0 10px rgba(255,255,255,0.3);
}

.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>