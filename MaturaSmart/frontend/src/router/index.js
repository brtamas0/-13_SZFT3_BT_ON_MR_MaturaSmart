import { createRouter, createWebHistory } from 'vue-router'
import { setTitle } from '@/router/guards/SetTitleGuard.mjs'
import { routes as autoRoutes } from 'vue-router/auto-routes' 

const customRoutes = [
  {
    path: '/test-chat',
    name: 'test-chat',
    component: () => import('@/views/TestChat.vue') 
  }
]

export const router = createRouter({
  history: createWebHistory(),
  routes: [...autoRoutes, ...customRoutes]
})

router.beforeEach(setTitle)