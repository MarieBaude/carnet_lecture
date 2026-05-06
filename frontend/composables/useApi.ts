export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase

  const fetch = async (endpoint: string, opts: any = {}) => {
    const token = useCookie('auth_token').value
    
    const headers: any = {
      Accept: 'application/json',
      ...opts.headers
    }

    // Ne pas ajouter Content-Type si body est FormData (laisser le navigateur gérer le boundary)
    if (!(opts.body instanceof FormData)) {
      headers['Content-Type'] = 'application/json'
    }
    
    if (token) {
      headers.Authorization = `Bearer ${token}`
    }
    
    try {
      const response = await $fetch(`${baseURL}${endpoint}`, {
        ...opts,
        headers
      })
      return response
    } catch (error: any) {
      throw error.data || error
    }
  }

  return { fetch }
}