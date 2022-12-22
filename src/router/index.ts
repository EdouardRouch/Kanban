import { createRouter, createWebHistory } from 'vue-router'
import { useUserStore } from '@/stores/user';
import Dashboard from '@/components/Dashboard.vue'
import Kanban from '@/components/kanban/Kanban.vue'
import App from '@/App.vue'
import Home from '@/components/Home.vue'
import Setup from '@/components/Setup.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { name: 'Root', path: '/', redirect: '/dashboard' },
    { name: 'Home', path: '/home', component: Home },
    { name: 'Dashboard', path: '/dashboard', component: Dashboard },
    { name: 'Kanban', path: '/kanban/:id', component: Kanban },
    { name: 'Setup', path: '/setup', component: Setup },
  ]
})

const protectedRoutes = [
  'Dashboard',
]
router.beforeEach(async (to, from) => {
  const userStore = useUserStore();

  if (protectedRoutes.includes(to.name as string)) {
    await userStore.setSessionStatus();
    if (userStore.user === '') {
      return '/home';
    }
  }
})

export default router

