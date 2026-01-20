<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

const props = defineProps({
  mode: {
    type: String,
    default: "app", 
  },
});

const router = useRouter();
const mobileOpen = ref(false);
const user = ref(null);

onMounted(() => {
  const storedUser = localStorage.getItem('user');
  const token = localStorage.getItem('token');

  if (token && storedUser) {
    try {
      user.value = JSON.parse(storedUser);
    } catch (e) {
      console.error("Hiba a user adatok betöltésekor");
    }
  }
});

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

  localStorage.removeItem('token');
  localStorage.removeItem('user');
  user.value = null;
  mobileOpen.value = false;
  router.push('/login');
};

const scrollToId = (id) => {
    mobileOpen.value = false;
    const element = document.getElementById(id);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    } else {
        router.push('/').then(() => {
            setTimeout(() => {
                const el = document.getElementById(id);
                if(el) el.scrollIntoView({ behavior: 'smooth' });
            }, 100);
        });
    }
}
</script>

<template>
  <header class="fixed top-0 left-0 w-full z-50 backdrop-blur-xl bg-[#0b1029]/90 border-b border-white/10 shadow-lg">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">

      <RouterLink to="/" class="text-2xl font-bold tracking-wide flex items-center gap-2 text-white group">
        <span class="group-hover:scale-110 transition-transform">🚀</span>
        <span>Matura<span class="text-blue-400">Smart</span></span>
      </RouterLink>

      <nav class="hidden md:flex items-center gap-8 text-sm text-gray-300 font-medium">
        
        <template v-if="props.mode === 'landing'">
          <a @click.prevent="scrollToId('home')" href="#home" class="nav-item">Kezdőlap</a>
          <a @click.prevent="scrollToId('features')" href="#features" class="nav-item">Funkciók</a>
          <a @click.prevent="scrollToId('mission')" href="#mission" class="nav-item">Célunk</a>
        </template>

        <template v-else>
          <RouterLink to="/main" class="nav-item flex items-center gap-2">
            <span>📊</span> Vezérlőpult
          </RouterLink>
          <RouterLink to="/tantargyak" class="nav-item flex items-center gap-2">
            <span>📚</span> Tantárgyak
          </RouterLink>
          <RouterLink to="/ranglista" class="nav-item flex items-center gap-2 text-yellow-400 hover:text-yellow-300">
            <span>🏆</span> Ranglista
          </RouterLink>
        </template>
      </nav>

      <div class="flex items-center gap-4">
        
        <template v-if="props.mode === 'landing'">
            <RouterLink 
              to="/login" 
              class="hidden md:inline-block px-5 py-2 rounded-xl bg-blue-600 text-white font-bold shadow-lg shadow-blue-900/30 hover:bg-blue-500 hover:-translate-y-0.5 transition-all"
            >
              Belépés
            </RouterLink>
        </template>

        <template v-else-if="props.mode === 'app' && user">
          <div class="hidden sm:flex px-3 py-1.5 bg-white/5 border border-white/10 rounded-full text-xs font-bold items-center gap-1 text-white" title="Napi sorozat">
            <span>🔥</span> {{ user.current_streak || 0 }}
          </div>

          <RouterLink to="/profile" class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-white border-2 border-[#0b1029] shadow-md hover:scale-105 transition-transform" title="Profil">
            {{ user.full_name?.charAt(0) || 'U' }}
          </RouterLink>

          <button @click="handleLogout" class="hidden md:block text-gray-400 hover:text-red-400 transition" title="Kilépés">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
            </svg>
          </button>
        </template>

        <button class="md:hidden text-2xl text-white p-2" @click="mobileOpen = !mobileOpen">
          ☰
        </button>
      </div>
    </div>

    <transition name="slide">
      <div v-if="mobileOpen" class="md:hidden bg-[#0b1029] border-t border-white/10 px-6 py-6 absolute w-full shadow-2xl min-h-screen">
        
        <template v-if="props.mode === 'app'">
           <div class="flex items-center gap-3 border-b border-white/10 pb-6 mb-6">
             <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white">
                {{ user?.full_name?.charAt(0) || 'U' }}
             </div>
             <div>
                <div class="text-white font-bold">{{ user?.full_name }}</div>
                <div class="text-xs text-gray-400">{{ user?.email }}</div>
             </div>
           </div>

           <div class="space-y-6 text-lg">
             <RouterLink to="/main" class="mobile-link" @click="mobileOpen = false">📊 Vezérlőpult</RouterLink>
             <RouterLink to="/tantargyak" class="mobile-link" @click="mobileOpen = false">📚 Tantárgyak</RouterLink>
             <RouterLink to="/ranglista" class="mobile-link text-yellow-400" @click="mobileOpen = false">🏆 Ranglista</RouterLink>
             <RouterLink to="/profile" class="mobile-link" @click="mobileOpen = false">👤 Profil</RouterLink>
           </div>

           <button @click="handleLogout" class="w-full text-left text-red-400 font-bold mt-8 pt-6 border-t border-white/10 flex items-center gap-2">
             🚪 Kijelentkezés
           </button>
        </template>

        <template v-else>
           <RouterLink to="/login" class="block w-full text-center bg-blue-600 text-white font-bold py-3 rounded-xl mb-8 shadow-lg shadow-blue-900/40" @click="mobileOpen = false">
             Belépés
           </RouterLink>
           <div class="space-y-6 text-lg text-center">
             <a @click.prevent="scrollToId('home')" href="#home" class="mobile-link block">Kezdőlap</a>
             <a @click.prevent="scrollToId('features')" href="#features" class="mobile-link block">Funkciók</a>
             <a @click.prevent="scrollToId('mission')" href="#mission" class="mobile-link block">Célunk</a>
           </div>
        </template>

      </div>
    </transition>
  </header>
</template>

<style scoped>
.nav-item {
  position: relative;
  transition: all 0.2s;
  cursor: pointer;
}
.nav-item:hover {
  color: white;
  text-shadow: 0 0 10px rgba(255,255,255,0.3);
}

.mobile-link {
  display: block;
  font-weight: 600;
  color: #94a3b8;
  transition: color 0.2s;
}
.mobile-link:hover {
  color: white;
}

.slide-enter-active, .slide-leave-active { transition: all 0.3s ease; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-10px); }
</style>