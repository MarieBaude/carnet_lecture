export default defineNuxtRouteMiddleware((to) => {
  const authStore = useAuthStore()
  
  console.log('Middleware auth - path:', to.path, 'isAuthenticated:', authStore.isAuthenticated)
  
  const publicPages = ['/login', '/register', '/']
  
  if (!authStore.isAuthenticated && !publicPages.includes(to.path)) {
    return navigateTo('/login')
  }
  
  if (authStore.isAuthenticated && (to.path === '/login' || to.path === '/register')) {
    return navigateTo('/')
  }
})