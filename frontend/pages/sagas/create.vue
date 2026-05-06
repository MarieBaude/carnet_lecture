<template>
  <div>
    <h1 class="font-serif text-section text-ink mb-8">Ajouter une saga</h1>

    <form @submit.prevent="handleSubmit" class="max-w-2xl space-y-8">
      <!-- Nom -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">
          Nom de la saga <span class="text-accent">*</span>
        </label>
        <input
          v-model="form.name"
          type="text"
          required
          placeholder="Ex: Les Brumes d'Avalor"
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                 focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
        />
      </div>

      <!-- Description -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Description</label>
        <textarea
          v-model="form.description"
          rows="4"
          placeholder="Décrivez l'univers de la saga..."
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                 focus:outline-none focus:border-accent-2 resize-none"
        />
      </div>

      <!-- Livres de la saga -->
      <div>
        <div class="flex items-center justify-between mb-3">
          <label class="text-label text-ink-3 uppercase tracking-wider">Livres</label>
          <button type="button" @click="showBookSearch = !showBookSearch" class="text-sm text-accent hover:underline">
            + Ajouter un livre
          </button>
        </div>

        <!-- Liste des livres ajoutés -->
        <div v-if="form.books.length" class="space-y-2 mb-4">
          <div
            v-for="(book, i) in form.books"
            :key="i"
            class="flex items-center gap-3 bg-surface border border-line rounded-card p-3"
          >
            <span class="font-serif text-sm text-accent font-semibold w-8 text-center">T.{{ book.tome_number || i + 1 }}</span>
            <div class="flex-1 min-w-0">
              <p class="text-body text-ink font-medium truncate">{{ book.title }}</p>
              <p class="text-meta text-ink-3">{{ book.author || '' }}</p>
            </div>
            <input
              v-model.number="book.tome_number"
              type="number"
              min="1"
              placeholder="Tome"
              class="w-16 px-2 py-1 text-xs bg-bg-2 border border-line rounded text-ink text-center"
            />
            <button type="button" @click="form.books.splice(i, 1)" class="text-ink-3 hover:text-red-500">&times;</button>
          </div>
        </div>

        <!-- Recherche de livre -->
        <div v-if="showBookSearch" class="bg-surface-2 border border-line rounded-card p-4 space-y-3">
          <div class="relative">
            <input
              v-model="bookSearch"
              type="text"
              placeholder="Rechercher un livre existant..."
              class="w-full px-4 py-2.5 bg-surface border border-line rounded-btn text-body text-ink text-sm"
              @input="searchBooks"
            />
            <div v-if="bookResults.length" class="absolute z-10 w-full bg-surface border border-line rounded-card mt-1 shadow-lg max-h-40 overflow-y-auto">
              <button
                v-for="b in bookResults"
                :key="b.id"
                type="button"
                @click="addBook(b); bookSearch = ''; bookResults = []; showBookSearch = false"
                class="w-full text-left px-4 py-2 text-sm text-ink hover:bg-bg-2"
              >
                {{ b.title }} — {{ b.authors?.map(a => a.name).join(', ') }}
              </button>
            </div>
          </div>

          <button type="button" @click="showNewBook = true; showBookSearch = false" class="text-sm text-accent hover:underline italic">
            Ou créer un nouveau livre
          </button>
        </div>

        <!-- Création rapide d'un livre -->
        <div v-if="showNewBook" class="bg-surface-2 border border-line rounded-card p-4 space-y-3">
          <input
            v-model="newBook.title"
            type="text"
            placeholder="Titre du livre"
            class="w-full px-4 py-2 bg-surface border border-line rounded-btn text-body text-ink text-sm"
          />
          <input
            v-model="newBook.author"
            type="text"
            placeholder="Auteur"
            class="w-full px-4 py-2 bg-surface border border-line rounded-btn text-body text-ink text-sm"
          />
          <div class="flex gap-2">
            <AppButton size="sm" @click="addNewBook">Ajouter</AppButton>
            <AppButton size="sm" variant="ghost" @click="showNewBook = false; newBook = { title: '', author: '' }">Annuler</AppButton>
          </div>
        </div>
      </div>

      <!-- Erreur -->
      <div v-if="error" class="bg-tag-tw-bg text-tag-tw-fg p-4 rounded-btn text-body">{{ error }}</div>

      <!-- Submit -->
      <div class="flex gap-3">
        <AppButton type="submit" :disabled="submitting">
          <Loader2 v-if="submitting" class="w-4 h-4 animate-spin" />
          {{ submitting ? 'Création...' : 'Ajouter la saga' }}
        </AppButton>
        <AppButton variant="ghost" @click="router.push('/')">Annuler</AppButton>
      </div>
    </form>
  </div>
</template>

<script setup>
import { Loader2 } from 'lucide-vue-next'

definePageMeta({ layout: 'default', middleware: 'auth' })

const { fetch } = useApi()
const router = useRouter()

const form = reactive({
  name: '',
  description: '',
  books: []
})

const bookSearch = ref('')
const bookResults = ref([])
const showBookSearch = ref(false)
const showNewBook = ref(false)
const newBook = reactive({ title: '', author: '' })
const submitting = ref(false)
const error = ref('')

const searchBooks = async () => {
  if (bookSearch.value.length < 2) { bookResults.value = []; return }
  try {
    const res = await fetch(`/books?q=${encodeURIComponent(bookSearch.value)}&per_page=5`)
    bookResults.value = res.data?.books || res.data || []
  } catch (e) { bookResults.value = [] }
}

const addBook = (book) => {
  form.books.push({
    id: book.id,
    title: book.title,
    author: book.authors?.map(a => a.name).join(', '),
    tome_number: form.books.length + 1
  })
}

const addNewBook = () => {
  if (newBook.title.trim()) {
    form.books.push({
      title: newBook.title,
      author: newBook.author,
      tome_number: form.books.length + 1,
      _new: true
    })
    showNewBook.value = false
    newBook.title = ''
    newBook.author = ''
  }
}

const handleSubmit = async () => {
  if (!form.name.trim()) { error.value = 'Le nom de la saga est obligatoire'; return }

  submitting.value = true
  error.value = ''

  try {
    const body = {
      name: form.name,
      description: form.description,
      books: form.books.map(b => ({
        id: b._new ? undefined : b.id,
        title: b._new ? b.title : undefined,
        author: b._new ? b.author : undefined,
        tome_number: b.tome_number
      }))
    }

    const res = await fetch('/sagas', { method: 'POST', body })
    const saga = res.data?.saga || res.data || res
    if (saga?.id) {
      router.push(`/sagas/${saga.id}`)
    } else {
      router.push('/')
    }
  } catch (e) {
    error.value = e.message || 'Erreur lors de la création'
  } finally {
    submitting.value = false
  }
}
</script>