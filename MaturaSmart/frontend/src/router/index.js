import { createRouter, createWebHistory } from 'vue-router'
import { setTitle } from '@/router/guards/SetTitleGuard.mjs'
import { routes as autoRoutes } from 'vue-router/auto-routes' 

const customRoutes = [
  {
    path: '/test-chat',
    name: 'test-chat',
    component: () => import('@/views/TestChat.vue') 
  },
  {
      path: '/register',
      name: 'register',
      component: () => import('../pages/register.vue')
    },
]

export const router = createRouter({
  history: createWebHistory(),
  routes: [...autoRoutes, ...customRoutes],

scrollBehavior(to) {
    if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
        top: 80 
      }
    }

    return { top: 0 }
  }
})


router.beforeEach(setTitle)