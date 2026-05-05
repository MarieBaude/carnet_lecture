export const useHome = () => {
  const { fetch } = useApi()

  const getHome = async () => {
    return await fetch('/home')
  }

  return { getHome }
}