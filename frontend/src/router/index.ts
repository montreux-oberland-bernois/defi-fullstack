import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue'),
      meta: { guest: true }
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/RegisterView.vue'),
      meta: { guest: true }
    },
    {
      path: '/routes',
      name: 'routes',
      component: () => import('@/views/RoutesView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/routes/new',
      name: 'new-route',
      component: () => import('@/views/NewRouteView.vue'),
      meta: { requiresAuth: true }
    },
    {
      path: '/stats',
      name: 'stats',
      component: () => import('@/views/StatsView.vue'),
      meta: { requiresAuth: true }
    },
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  console.log('[Router Guard]', {
    to: to.path,
    from: from.path,
    isAuthenticated: authStore.isAuthenticated,
    token: !!localStorage.getItem('token')
  })

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    console.log('[Router Guard] Redirecting to /login - not authenticated')
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    console.log('[Router Guard] Redirecting to / - already authenticated')
    next('/')
  } else {
    next()
  }
})

export default router
