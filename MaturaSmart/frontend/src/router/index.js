import { createRouter, createWebHistory } from 'vue-router'
import { setTitle } from '@/router/guards/SetTitleGuard.mjs'
import { routes as autoRoutes } from 'vue-router/auto-routes'

const customRoutes = [
  // --- PUBLIKUS OLDALAK ---
  {
    path: '/',
    name: 'Landing',
    component: () => import('@/pages/index.vue'),
    meta: { title: 'Kezdőlap' }
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/pages/login.vue'),
    meta: { title: 'Belépés' }
  },
  {
    path: '/register',
    name: 'Register',
    component: () => import('@/pages/register.vue'),
    meta: { title: 'Regisztráció' }
  },

  // --- VÉDETT OLDALAK ---
  {
    path: '/main',
    name: 'Main',
    component: () => import('@/pages/main.vue'),
    meta: { 
        requiresAuth: true,
        title: 'Vezérlőpult'
    }
  },
  {
    path: '/profile',
    name: 'Profile',
    component: () => import('@/pages/profile.vue'),
    meta: { 
        requiresAuth: true,
        title: 'Profilom' 
    }
  },
  {
    path: '/ranglista', 
    name: 'Leaderboard',
    component: () => import('@/pages/leaderboard.vue'),
    meta: { 
        requiresAuth: true,
        title: 'Ranglista' 
    }
  }
]

export const router = createRouter({
  history: createWebHistory(),
  routes: [...autoRoutes, ...customRoutes],

  scrollBehavior(to) {
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth', top: 80 }
    }
    return { top: 0 }
  }
})

router.beforeEach(setTitle)

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else {
    next()
  }
})