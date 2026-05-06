<template>
  <div v-if="saga">
    <AppBreadcrumb :items="[{ label: 'Sagas', to: '/sagas' }, { label: saga.name }]" class="mb-6" />

    <div class="mb-8">
      <h1 class="font-serif text-book-title text-ink mb-3">{{ saga.name }}</h1>
      <p class="text-synopsis text-ink-2">{{ saga.description || 'Aucune description' }}</p>
    </div>

    <!-- Livres -->
    <div class="flex items-center justify-between mb-4">
      <h2 class="font-serif text-section text-ink">Tomes</h2>
      <button @click="showAddBook = !showAddBook" class="text-body text-accent hover:underline flex items-center gap-1">
        <Plus class="w-4 h-4" /> Ajouter un livre
      </button>
    </div>

    <!-- Ajout livre -->
    <div v-if="showAddBook" class="bg-surface-2 border border-line rounded-card p-4 mb-6">
      <div class="flex gap-3 items-end">
        <div class="flex-1 relative">
          <input
            v-model="bookSearch"
            type="text"
            placeholder="Rechercher un livre..."
            class="w-full px-4 py-2.5 bg-surface border border-line rounded-btn text-body text-ink text-sm"
            @input="searchBooks"
          />
          <div v-if="bookResults.length" class="absolute z-10 w-full bg-surface border border-line rounded-card mt-1 shadow-lg max-h-40 overflow-y-auto">
            <button
              v-for="b in bookResults"
              :key="b.id"
              type="button"
              @click="selectBook(b)"
              class="w-full text-left px-4 py-2 text-sm text-ink hover:bg-bg-2"
            >
              {{ b.title }} — {{ b.authors?.map(a => a.name).join(', ') }}
            </button>
          </div>
        </div>
        <input
          v-model.number="newTome"
          type="number"
          min="1"
          placeholder="Tome"
          class="w-20 px-3 py-2.5 bg-surface border border-line rounded-btn text-body text-ink text-sm"
        />
        <AppButton size="sm" @click="addBook" :disabled="!selectedBook">Ajouter</AppButton>
      </div>
      <p v-if="addError" class="text-sm text-tag-tw-fg mt-2">{{ addError }}</p>
    </div>

    <!-- Liste des tomes -->
    <div v-if="books.length" class="space-y-3">
      <div
        v-for="book in books"
        :key="book.id"
        class="bg-surface border border-line rounded-card p-4 flex items-center gap-4"
      >
        <span class="font-serif text-lg text-accent font-semibold w-10 text-center">T.{{ book.pivot?.tome_number }}</span>
        <NuxtLink :to="`/books/${book.id}`" class="flex-shrink-0">
          <AppBookCover :cover-url="book.cover_url" size="sm" />
        </NuxtLink>
        <div class="flex-1 min-w-0">
          <NuxtLink :to="`/books/${book.id}`">
            <p class="font-serif text-lg text-ink font-semibold truncate">{{ book.title }}</p>
          </NuxtLink>
          <p class="text-body text-ink-2">{{ book.authors?.map(a => a.name).join(', ') }}</p>
        </div>
        <div class="flex items-center gap-2">
          <input
            v-model.number="book._newTome"
            type="number"
            min="1"
            :placeholder="String(book.pivot?.tome_number)"
            class="w-16 px-2 py-1 text-xs bg-bg-2 border border-line rounded text-ink text-center"
            @keyup.enter="updateTome(book)"
          />
          <button
            @click="updateTome(book)"
            class="text-xs text-accent hover:underline"
          >
            Ok
          </button>
          <button
            @click="removeBook(book)"
            class="text-ink-3 hover:text-red-500 ml-2"
          >
            <Trash2 class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <AppEmptyState v-else title="Aucun livre dans cette saga" />
  </div>
</template>

<script setup>
import { Plus, Trash2 } from 'lucide-vue-next'

definePageMeta({ layout: 'default' })

const route = useRoute()
const { fetch } = useApi()

const saga = ref(null)
const books = ref([])
const showAddBook = ref(false)
const bookSearch = ref('')
const bookResults = ref([])
const selectedBook = ref(null)
const newTome = ref(1)
const addError = ref('')

const loadSaga = async () => {
  try {
    const res = await fetch(`/sagas/${route.params.id}`)
    saga.value = res.data?.saga || res.data
    books.value = (res.data?.books || saga.value?.books || []).map(b => ({ ...b, _newTome: null }))
  } catch (e) { console.error(e) }
}

const searchBooks = async () => {
  if (bookSearch.value.length < 2) { bookResults.value = []; return }
  try {
    const res = await fetch(`/books?search=${encodeURIComponent(bookSearch.value)}&per_page=5`)
    bookResults.value = res.data?.books || res.data || []
  } catch (e) { bookResults.value = [] }
}

const selectBook = (book) => {
  selectedBook.value = book
  bookSearch.value = book.title
  bookResults.value = []
}

const addBook = async () => {
  if (!selectedBook.value) return
  try {
    await fetch(`/sagas/${route.params.id}/books`, {
      method: 'POST',
      body: { book_id: selectedBook.value.id, tome_number: newTome.value || books.value.length + 1 }
    })
    showAddBook.value = false
    selectedBook.value = null
    bookSearch.value = ''
    newTome.value = 1
    addError.value = ''
    loadSaga()
  } catch (e) {
    addError.value = e.message || 'Erreur'
  }
}

const updateTome = async (book) => {
  if (!book._newTome && book._newTome !== 0) return
  try {
    await fetch(`/sagas/${route.params.id}/books/${book.id}`, {
      method: 'PATCH',
      body: { tome_number: book._newTome }
    })
    book.pivot.tome_number = book._newTome
    book._newTome = null
  } catch (e) { /* ignore */ }
}

const removeBook = async (book) => {
  try {
    await fetch(`/sagas/${route.params.id}/books/${book.id}`, { method: 'DELETE' })
    loadSaga()
  } catch (e) { /* ignore */ }
}

onMounted(loadSaga)
</script>