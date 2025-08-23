import { createRouter, createWebHistory } from 'vue-router'
import Contact from './views/Contact.vue'
import Login from './views/Login.vue'
import Register from './views/Register.vue'
import ForgotPassword from './views/ForgotPassword.vue'
import Files from './views/Files.vue'
import { useAuth } from './stores/auth'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/contact' },
    { path: '/contact', component: Contact, meta: { requiresAuth: true } },
    { path: '/files', component: Files, meta: { requiresAuth: true } },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    { path: '/forgot', component: ForgotPassword },
  ],
})

// Guard
router.beforeEach(async (to, from, next) => {
  const auth = useAuth()
  if (auth.me === null) {
    try {
      await auth.fetchMe()
    } catch {
      auth.me = { authenticated: false }
    }
  }

  if (to.meta.requiresAuth && !auth.isAuth) {
    return next('/login')
  }

  return next()
})
