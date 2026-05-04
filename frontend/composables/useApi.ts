export const useApi = () => {
  const fetch = async (endpoint: string, opts: any = {}) => {
    const token = useCookie('auth_token').value
    const config = useRuntimeConfig()
    const baseURL = config.public.apiBase
    
    const headers: any = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...opts.headers
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