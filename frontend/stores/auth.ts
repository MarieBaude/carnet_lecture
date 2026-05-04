export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const isAuthenticated = computed(() => !!token.value)
  const { fetch } = useApi()

  const init = () => {
    const savedToken = useCookie('auth_token').value
    if (savedToken) {
      token.value = savedToken
      fetchUser()
    }
  }

  const fetchUser = async () => {
    try {
      const response = await fetch('/user')
      user.value = response.data
    } catch (error) {
      logout()
    }
  }

  const register = async (name: string, email: string, password: string, password_confirmation: string) => {
    const response = await fetch('/register', {
      method: 'POST',
      body: { name, email, password, password_confirmation }
    })
    token.value = response.data.token
    user.value = response.data.user
    useCookie('auth_token').value = response.data.token
    return response
  }

  const login = async (email: string, password: string) => {
    const response = await fetch('/login', {
      method: 'POST',
      body: { email, password }
    })
    token.value = response.data.token
    user.value = response.data.user
    useCookie('auth_token').value = response.data.token
    return response
  }

  const logout = async () => {
    try {
      await fetch('/logout', { method: 'POST' })
    } catch (e) {
      // ignore
    }
    user.value = null
    token.value = null
    if (process.client) {
      useCookie('auth_token').value = null
    }
    navigateTo('/login')
  }

  return {
    user,
    token,
    isAuthenticated,
    init,
    fetchUser,
    register,
    login,
    logout
  }
})