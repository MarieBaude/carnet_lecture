<template>
  <div>
    <h1 class="font-serif text-section text-ink mb-6">Catalogue</h1>
    
    <div class="flex flex-wrap gap-4 mb-8">
      <div class="flex-1 min-w-[300px]">
        <AppSearchInput 
          v-model="searchQuery" 
          @search="performSearch"
          placeholder="Rechercher un livre, un auteur…"
        />
      </div>
      
      <select 
        v-model="selectedGenre"
        @change="applyFilters"
        class="bg-surface border border-line rounded-btn px-4 py-2 text-body text-ink focus:outline-none focus:border-accent-2"
      >
        <option value="">Tous les genres</option>
        <option v-for="genre in genres" :key="genre.id" :value="genre.slug">
          {{ genre.name }} ({{ genre.books_count }})
        </option>
      </select>
      
      <select 
        v-model="sortBy"
        @change="applyFilters"
        class="bg-surface border border-line rounded-btn px-4 py-2 text-body text-ink"
      >
        <option value="created_at-desc">Plus récents</option>
        <option value="title-asc">Titre A-Z</option>
        <option value="title-desc">Titre Z-A</option>
        <option value="published_date-desc">Date de publication ↓</option>
      </select>
    </div>
    
    <div v-if="!loading && books.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <BookCard v-for="book in books" :key="book.id" :book="book" />
    </div>
    
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="bg-surface rounded-card p-4 flex gap-4 animate-pulse">
        <div class="w-24 h-36 bg-bg-2 rounded-sm" />
        <div class="flex-1 space-y-3">
          <div class="h-6 bg-bg-2 rounded w-3/4" />
          <div class="h-4 bg-bg-2 rounded w-1/2" />
          <div class="h-5 bg-bg-2 rounded w-1/3" />
        </div>
      </div>
    </div>
    
    <AppEmptyState 
      v-if="!loading && !books.length" 
      title="Aucun livre trouvé"
      description="Essayez de modifier vos filtres ou votre recherche."
    />
    
    <AppPagination 
      v-if="totalPages > 1"
      :current-page="currentPage"
      :last-page="totalPages"
      @page-changed="goToPage"
      class="mt-8"
    />
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default', middleware: 'auth' })

const { fetch } = useApi()

const books = ref([])
const genres = ref([])
const loading = ref(true)
const currentPage = ref(1)
const totalPages = ref(1)
const searchQuery = ref('')
const selectedGenre = ref('')
const sortBy = ref('created_at-desc')

const loadGenres = async () => {
  try {
    const response = await fetch('/genres')
    genres.value = response.data
  } catch (e) { console.error(e) }
}

const loadBooks = async () => {
  loading.value = true
  try {
    const [sort, order] = sortBy.value.split('-')
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: 12,
      sort,
      order
    })
    if (selectedGenre.value) params.append('genre', selectedGenre.value)
    if (searchQuery.value) params.append('search', searchQuery.value)
    
    const response = await fetch(`/books?${params}`)
    books.value = response.data?.books || response.data || []
    totalPages.value = response.meta?.last_page || 1
  } catch (e) {
    books.value = []
  } finally {
    loading.value = false
  }
}

const performSearch = () => {
  currentPage.value = 1
  loadBooks()
}

const applyFilters = () => {
  currentPage.value = 1
  loadBooks()
}

const goToPage = (page) => {
  currentPage.value = page
  loadBooks()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  loadGenres()
  loadBooks()
})
</script>