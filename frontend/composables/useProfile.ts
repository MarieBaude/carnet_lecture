export const useProfile = () => {
  const { fetch } = useApi()
  const loading = ref(false)

  const getMyProfile = async () => {
    const res = await fetch('/me')
    return res.data
  }

  const updateProfile = async (data: any) => {
    const res = await fetch('/me', { method: 'PATCH', body: data })
    return res.data
  }

  const getUserProfile = async (id: number) => {
    const res = await fetch(`/users/${id}`)
    return res.data
  }

  const follow = async (id: number) => {
    return await fetch(`/users/${id}/follow`, { method: 'POST' })
  }

  const unfollow = async (id: number) => {
    return await fetch(`/users/${id}/follow`, { method: 'DELETE' })
  }

  const getFollowers = async (params: any = {}) => {
    const query = new URLSearchParams(params).toString()
    return await fetch(`/me/followers?${query}`)
  }

  const getFollowing = async (params: any = {}) => {
    const query = new URLSearchParams(params).toString()
    return await fetch(`/me/following?${query}`)
  }

  const getActivity = async (page = 1) => {
    return await fetch(`/me/activity?page=${page}&per_page=20`)
  }

  const relativeTime = (date: string) => {
    const now = new Date()
    const then = new Date(date)
    const diff = Math.floor((now.getTime() - then.getTime()) / 1000)

    const intervals: Record<string, number> = {
      an: 31536000,
      mois: 2592000,
      semaine: 604800,
      jour: 86400,
      heure: 3600,
      minute: 60
    }

    for (const [unit, seconds] of Object.entries(intervals)) {
      const value = Math.floor(diff / seconds)
      if (value >= 1) {
        return `il y a ${value} ${unit}${value > 1 && unit !== 'mois' ? 's' : ''}`
      }
    }
    return "à l'instant"
  }

  return { loading, getMyProfile, updateProfile, getUserProfile, follow, unfollow, getFollowers, getFollowing, getActivity, relativeTime }
}