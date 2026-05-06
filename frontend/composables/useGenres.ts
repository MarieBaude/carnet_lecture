export const useGenres = () => {
  const { fetch } = useApi()

  const getGenres = async () => {
    const res = await fetch('/genres')
    return res.data || []
  }

  return { getGenres }
}