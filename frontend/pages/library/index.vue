<template>
  <div>
    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-8">
      <div class="bg-surface border border-line rounded-card p-5 text-center">
        <div class="font-serif text-3xl text-accent font-semibold">{{ stats.total || 0 }}</div>
        <div class="text-meta text-ink-3 mt-1">Livres</div>
      </div>
      <div class="bg-surface border border-line rounded-card p-5 text-center">
        <div class="font-serif text-3xl text-accent font-semibold">{{ stats.pages_read || 0 }}</div>
        <div class="text-meta text-ink-3 mt-1">Pages lues</div>
      </div>
      <div class="bg-surface border border-line rounded-card p-5 text-center">
        <div class="font-serif text-3xl text-accent font-semibold">{{ stats.average_rating || '-' }}</div>
        <div class="text-meta text-ink-3 mt-1">Note moyenne</div>
      </div>
    </div>

    <h1 class="font-serif text-section text-ink mb-6">Ma Bibliothèque</h1>

    <!-- Onglets statuts -->
    <div class="border-b border-line mb-6">
      <div class="flex gap-0 overflow-x-auto">
        <button v-for="tab in tabs" :key="tab.value" @click="selectedStatus = tab.value; currentPage = 1; loadBooks()"
          :class="[
            'tab-underline pb-3 px-1 mr-6 text-body font-medium whitespace-nowrap transition-colors',
            selectedStatus === tab.value ? 'active text-accent' : 'text-ink-3 hover:text-ink'
          ]">
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Filtres -->
    <div class="flex flex-wrap gap-4 mb-6">
      <div class="flex-1 min-w-[250px]">
        <AppSearchInput v-model="searchQuery" @search="currentPage = 1; loadBooks()"
          placeholder="Rechercher dans ma bibliothèque…" />
      </div>
      <select v-model="sortBy" @change="currentPage = 1; loadBooks()"
        class="bg-surface border border-line rounded-btn px-4 py-2 text-body text-ink focus:outline-none focus:border-accent-2">
        <option value="created_at-desc">Ajoutés récemment</option>
        <option value="title-asc">Titre A-Z</option>
        <option value="rating-desc">Note ↑</option>
        <option value="rating-asc">Note ↓</option>
      </select>
    </div>

    <!-- Loader -->
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

    <!-- Grille -->
    <div v-if="!loading && books.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="item in books" :key="item.id" class="bg-surface border border-line rounded-card shadow-sm p-4
               hover:-translate-y-0.5 hover:shadow-md hover:border-accent-soft
               transition-all duration-200 flex gap-4 relative">
        <!-- Badge statut -->
        <span :class="[
          'absolute top-3 right-3 text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full z-10',
          statusBadgeClass(item.library.status)
        ]">
          {{ statusLabel(item.library.status) }}
        </span>

        <NuxtLink :to="`/books/${item.book?.id || item.id}`" class="flex-shrink-0">
          <AppBookCover :variant="item.book?.cover_variant || 1" size="md" />
        </NuxtLink>

        <div class="flex-1 min-w-0">
          <NuxtLink :to="`/books/${item.book?.id || item.id}`">
            <h3 class="font-serif text-xl text-ink line-clamp-2 pr-16">
              {{ item.title }}
            </h3>
          </NuxtLink>
          <p class="text-body text-ink-2 mt-1">
            {{item.book?.authors?.map(a => a.name).join(', ')}}
          </p>

          <div class="flex flex-wrap gap-1.5 mt-2">
            <AppTag v-for="genre in (item.book?.genres?.slice(0, 2) || [])" :key="genre.id" :label="genre.name"
              category="genre" />
          </div>

          <!-- Note si lu -->
          <div v-if="item.library.rating" class="flex items-center gap-2 mt-3">
            <AppStarRating :rating="item.library.rating" />
            <span class="text-meta text-ink-3">{{ item.library.rating }}/5</span>
          </div>

          <!-- Progression si en cours -->
          <div v-if="item.status === 'reading'" class="mt-3">
            <div class="flex justify-between text-meta text-ink-3 mb-1">
              <span>{{ item.current_page || 0 }} / {{ item.page_count || '?' }} pages</span>
              <span>{{ progressPercent(item) }}%</span>
            </div>
            <div class="w-full bg-bg-2 rounded-full h-2 overflow-hidden">
              <div class="bg-accent h-2 rounded-full transition-all duration-300"
                :style="{ width: progressPercent(item) + '%' }" />
            </div>
            <div class="flex gap-2 mt-2">
              <input v-model.number="item._newPage" type="number" min="0" :max="item.page_count"
                class="w-16 px-2 py-1 text-xs bg-bg-2 border border-line rounded text-ink text-center"
                @keyup.enter="updatePage(item)" />
              <button @click="updatePage(item)" :disabled="item._saving"
                class="text-xs text-accent hover:text-[#7a2f2f] font-medium disabled:opacity-50">
                <Check v-if="item._saved" class="w-3.5 h-3.5 text-green-600" />
                <Loader2 v-else-if="item._saving" class="w-3.5 h-3.5 animate-spin text-accent" />
                <span v-else>Ok</span>
              </button>
            </div>
          </div>

          <!-- Menu actions -->
          <div class="absolute bottom-3 right-3">
            <button @click="openActions(item)"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-line bg-surface hover:border-accent-soft hover:text-accent transition-colors">
              <MoreHorizontal class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty state -->
    <AppEmptyState v-if="!loading && !books.length" :title="emptyTitle"
      description="Ajoutez des livres depuis le catalogue pour commencer votre bibliothèque."
      action-label="Explorer le catalogue" />

    <!-- Pagination -->
    <AppPagination v-if="totalPages > 1" :current-page="currentPage" :last-page="totalPages" @page-changed="goToPage"
      class="mt-8" />

    <!-- Modal actions rapides -->
    <div v-if="showActions" class="fixed inset-0 z-50 flex items-center justify-center bg-ink/30"
      @click.self="showActions = false">
      <div class="bg-surface rounded-card shadow-lg border border-line p-6 w-80">
        <h3 class="font-serif text-lg text-ink mb-4">Actions</h3>
        <div class="space-y-2">
          <button v-for="status in statusOptions" :key="status.value"
            @click="changeStatus(status.value); showActions = false"
            class="w-full text-left px-3 py-2 rounded-btn hover:bg-bg-2 text-body text-ink transition-colors flex items-center gap-3">
            <component :is="status.icon" class="w-4 h-4 text-ink-3" />
            {{ status.label }}
          </button>
          <hr class="border-line my-2" />
          <button @click="removeFromLibrary(); showActions = false"
            class="w-full text-left px-3 py-2 rounded-btn hover:bg-tag-tw-bg text-body text-tag-tw-fg transition-colors flex items-center gap-3">
            <Trash2 class="w-4 h-4" />
            Retirer de la bibliothèque
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Bookmark, BookOpen, CheckCircle, XCircle, ShoppingBag, MoreHorizontal, Trash2, Check, Loader2 } from 'lucide-vue-next'

definePageMeta({ layout: 'default', middleware: 'auth' })

const { getBooks, getStats, updateBook, removeBook } = useLibrary()

const books = ref([])
const stats = ref({})
const loading = ref(true)
const currentPage = ref(1)
const totalPages = ref(1)
const searchQuery = ref('')
const selectedStatus = ref('')
const sortBy = ref('created_at-desc')
const showActions = ref(false)
const selectedBook = ref(null)

const tabs = [
  { label: 'Tous', value: '' },
  { label: 'À lire', value: 'wishlist' },
  { label: 'Possédés', value: 'owned' },
  { label: 'En cours', value: 'reading' },
  { label: 'Lus', value: 'read' },
  { label: 'Abandonnés', value: 'dropped' }
]

const statusOptions = [
  { label: 'À lire', value: 'wishlist', icon: Bookmark },
  { label: 'Possédé', value: 'owned', icon: ShoppingBag },
  { label: 'En cours', value: 'reading', icon: BookOpen },
  { label: 'Lu', value: 'read', icon: CheckCircle },
  { label: 'Abandonné', value: 'dropped', icon: XCircle }
]

const emptyTitle = computed(() => {
  const tab = tabs.find(t => t.value === selectedStatus.value)
  return tab ? `Aucun livre "${tab.label.toLowerCase()}"` : 'Aucun livre'
})

const statusBadgeClass = (status) => {
  const map = {
    wishlist: 'bg-[#ebe6f0] text-[#574d68]',
    owned: 'bg-[#e2ebe5] text-[#3d5a47]',
    reading: 'bg-[#f3e2dc] text-[#7d3a31]',
    read: 'bg-[#e2ebe5] text-[#3d5a47]',
    dropped: 'bg-bg-2 text-ink-3'
  }
  return map[status] || 'bg-bg-2 text-ink-3'
}

const statusLabel = (status) => {
  const map = {
    wishlist: 'À lire',
    owned: 'Possédé',
    reading: 'En cours',
    read: 'Lu',
    dropped: 'Abandonné'
  }
  return map[status] || status
}

const loadBooks = async () => {
  loading.value = true
  try {
    const [sort, order] = sortBy.value.split('-')
    const params = {
      page: currentPage.value,
      per_page: 12,
      sort,
      order
    }
    if (selectedStatus.value) params.status = selectedStatus.value
    if (searchQuery.value) params.search = searchQuery.value

    const response = await getBooks(params)
    books.value = (response.data?.books || response.data || []).map((b) => ({
      ...b,
      status: b.library?.status,
      rating: b.library?.rating,
      current_page: b.library?.current_page,
      page_count: b.page_count,
      _saving: false,
      _saved: false,
      _newPage: b.library?.status === 'reading' ? (b.library?.current_page || 0) : undefined
    }))
    totalPages.value = response.meta?.last_page || 1
  } catch (e) {
    books.value = []
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    stats.value = await getStats()
  } catch (e) { /* ignore */ }
}

const openActions = (book) => {
  selectedBook.value = book
  showActions.value = true
}

const changeStatus = async (status) => {
  if (!selectedBook.value) return
  try {
    await updateBook(selectedBook.value.id, { status })
    loadBooks()
    loadStats()
  } catch (e) { /* ignore */ }
}

const removeFromLibrary = async () => {
  if (!selectedBook.value) return
  try {
    await removeBook(selectedBook.value.id)
    loadBooks()
    loadStats()
  } catch (e) { /* ignore */ }
}

const progressPercent = (item) => {
  if (item.page_count && item.current_page) {
    return Math.round((item.current_page / item.page_count) * 100)
  }
  return 0
}

const updatePage = async (item) => {
  if (!item._newPage && item._newPage !== 0) return
  item._saving = true
  item._saved = false
  try {
    await updateBook(item.id, { current_page: item._newPage })
    item.current_page = item._newPage
    item._newPage = item._newPage
    item._saved = true
    setTimeout(() => { item._saved = false }, 2000)
    loadStats()
  } catch (e) { /* ignore */ }
  finally {
    item._saving = false
  }
}


const goToPage = (page) => {
  currentPage.value = page
  loadBooks()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(() => {
  loadStats()
  loadBooks()
})
</script>