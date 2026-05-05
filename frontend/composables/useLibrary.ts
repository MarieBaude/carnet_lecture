export const useLibrary = () => {
  const { fetch } = useApi()
  const loading = ref(false)
  const error = ref(null)

  const addToLibrary = async (bookId: number, data: any = {}) => {
    loading.value = true
    error.value = null
    try {
      return await fetch('/library/books', {
        method: 'POST',
        body: { book_id: bookId, ...data }
      })
    } catch (e: any) {
      error.value = e.message || 'Erreur'
      throw e
    } finally {
      loading.value = false
    }
  }

  const updateBook = async (bookId: number, data: any) => {
    return await fetch(`/library/books/${bookId}`, {
      method: 'PATCH',
      body: data
    })
  }

  const removeBook = async (bookId: number) => {
    return await fetch(`/library/books/${bookId}`, {
      method: 'DELETE'
    })
  }

  const getStats = async () => {
    const res = await fetch('/library/stats')
    return res.data
  }

  const getShelves = async () => {
    const res = await fetch('/library/shelves')
    return res.data
  }

  const getBooks = async (params: any = {}) => {
    const query = new URLSearchParams(params).toString()
    return await fetch(`/library/books?${query}`)
  }

  return { loading, error, addToLibrary, updateBook, removeBook, getStats, getShelves, getBooks }
}