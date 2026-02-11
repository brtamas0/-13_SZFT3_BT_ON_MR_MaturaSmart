import { createRouter, createWebHistory } from 'vue-router'
import { setTitle } from '@/router/guards/SetTitleGuard.mjs'
import { routes as autoRoutes } from 'vue-router/auto-routes'
import GoogleCallback from '@/pages/GoogleCallback.vue'
import ForgotPassword from '../pages/forgotpassword.vue'
import ResetPassword from '../pages/resetpassword.vue'

const customRoutes = [
  // PUBLIKUS OLDALAK
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
  {
    path: '/google-callback',
    component: GoogleCallback
  },
  {
    path: '/forgot-password',
    name: 'forgotpassword',
    component: ForgotPassword
  },

  {
    // A :token --> URL-ben változó 
    path: '/reset-password/:token',
    name: 'ResetPassword',
    component: ResetPassword
  },

  // VÉDETT OLDALAK (Diák nézet)
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
    path: '/leaderboard',
    name: 'Leaderboard',
    component: () => import('@/pages/leaderboard.vue'),
    meta: {
      requiresAuth: true,
      title: 'Ranglista'
    }
  },

  // ADMIN
  {
    path: '/admin',
    component: () => import('@/layouts/AdminLayout.vue'),
    meta: {
      requiresAuth: true,
      requiresAdmin: true, // Csak admin léphet be
      title: 'Adminisztráció'
    },
    children: [
      {
        path: '',
        name: 'AdminDashboard',
        component: () => import('@/pages/admin/dashboard.vue')
      },
      {
        path: 'users',
        name: 'AdminUsers',
        component: () => import('@/pages/admin/users.vue')
      },
      {
        path: 'subjects',
        name: 'AdminSubjects',
        component: () => import('@/pages/admin/subjects.vue')
      },
      {
        path: 'subjects/:id/builder',
        name: 'CourseBuilder',
        component: () => import('@/pages/admin/course-builder.vue'),
        props: true
      }
    ]
  },
  {
    path: '/404',
    name: 'NotFound',
    component: () => import('@/pages/notfound.vue'),
    meta: { title: 'Nem található' }
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/404'
  }
]

export const router = createRouter({
  history: createWebHistory(),
  routes: [...customRoutes, ...autoRoutes],

  scrollBehavior(to) {
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth', top: 80 }
    }
    return { top: 0 }
  }
})

// Cím beállítása
router.beforeEach(setTitle)

// AUTH guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  let user = {}
  try {
    user = JSON.parse(localStorage.getItem('user') || '{}')
  } catch (e) {
    console.error("Hibás user adat a tárolóban")
    user = {}
  }

  if (to.meta.requiresAuth && !token) {
    return next('login')
  }

  if (to.meta.requiresAdmin && user.role !== 'admin') {
    return next('main')
  }

  next()
})