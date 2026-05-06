<template>
  <div>
    <h1 class="font-serif text-section text-ink mb-8">Ajouter un livre</h1>

    <form @submit.prevent="handleSubmit" class="max-w-2xl space-y-8">
      <!-- Titre -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">
          Titre <span class="text-accent">*</span>
        </label>
        <input
          v-model="form.title"
          type="text"
          required
          placeholder="Le titre du livre"
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                 focus:outline-none focus:border-accent-2 focus:ring-2 focus:ring-accent-soft transition-all"
        />
      </div>

      <!-- Auteurs -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">
          Auteur(s) <span class="text-accent">*</span>
        </label>
        <div class="flex flex-wrap gap-2 mb-2">
          <span
            v-for="(author, i) in form.authors"
            :key="i"
            class="inline-flex items-center gap-1 bg-accent-soft text-accent text-sm px-3 py-1 rounded-full"
          >
            {{ author.name }}
            <button type="button" @click="form.authors.splice(i, 1)" class="hover:text-red-500">&times;</button>
          </span>
        </div>
        <div class="relative">
          <input
            v-model="authorSearch"
            type="text"
            placeholder="Rechercher un auteur..."
            class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                   focus:outline-none focus:border-accent-2"
            @input="searchAuthors"
          />
          <div v-if="authorResults.length" class="absolute z-10 w-full bg-surface border border-line rounded-card mt-1 shadow-lg max-h-40 overflow-y-auto">
            <button
              v-for="a in authorResults"
              :key="a.id"
              type="button"
              @click="selectAuthor(a); authorSearch = ''; authorResults = []"
              class="w-full text-left px-4 py-2 text-body text-ink hover:bg-bg-2"
            >
              {{ a.name }}
            </button>
            <button
              type="button"
              @click="showNewAuthor = true; authorSearch = ''; authorResults = []"
              class="w-full text-left px-4 py-2 text-body text-accent hover:bg-bg-2 italic"
            >
              + Créer un nouvel auteur
            </button>
          </div>
        </div>
        <div v-if="showNewAuthor" class="mt-3 p-4 bg-surface border border-line rounded-card space-y-3">
          <input
            v-model="newAuthor.name"
            type="text"
            placeholder="Nom de l'auteur"
            class="w-full px-4 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink"
          />
          <textarea
            v-model="newAuthor.biography"
            rows="2"
            placeholder="Biographie (optionnel)"
            class="w-full px-4 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink resize-none"
          />
          <div class="flex gap-2">
            <AppButton size="sm" @click="addNewAuthor">Ajouter</AppButton>
            <AppButton size="sm" variant="ghost" @click="showNewAuthor = false; newAuthor = { name: '', biography: '' }">Annuler</AppButton>
          </div>
        </div>
      </div>

      <!-- Genres -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Genres</label>
        <div class="flex flex-wrap gap-2">
          <label
            v-for="genre in genres"
            :key="genre.id"
            :class="[
              'px-3 py-1 rounded-full text-sm cursor-pointer border transition-colors',
              form.genres.includes(genre.id)
                ? 'bg-accent-soft text-accent border-accent'
                : 'bg-surface text-ink-2 border-line hover:border-accent-soft'
            ]"
          >
            <input
              v-model="form.genres"
              type="checkbox"
              :value="genre.id"
              class="hidden"
            />
            {{ genre.name }}
          </label>
        </div>
      </div>

      <!-- Synopsis -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Synopsis</label>
        <textarea
          v-model="form.summary"
          rows="5"
          placeholder="Résumé du livre..."
          class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink placeholder:text-ink-3/50
                 focus:outline-none focus:border-accent-2 resize-none"
        />
      </div>

      <!-- Saga -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Saga (optionnel)</label>
        <div class="relative">
          <input
            v-model="sagaSearch"
            type="text"
            placeholder="Rechercher une saga..."
            class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink"
            @input="searchSagas"
          />
          <div v-if="sagaResults.length" class="absolute z-10 w-full bg-surface border border-line rounded-card mt-1 shadow-lg max-h-40 overflow-y-auto">
            <button
              v-for="s in sagaResults"
              :key="s.id"
              type="button"
              @click="form.saga_id = s.id; sagaSearch = s.name; sagaResults = []"
              class="w-full text-left px-4 py-2 text-body text-ink hover:bg-bg-2"
            >
              {{ s.name }}
            </button>
            <button
              type="button"
              @click="showNewSaga = true; sagaSearch = ''; sagaResults = []"
              class="w-full text-left px-4 py-2 text-body text-accent hover:bg-bg-2 italic"
            >
              + Créer une nouvelle saga
            </button>
          </div>
        </div>
        <div v-if="showNewSaga" class="mt-3 p-4 bg-surface border border-line rounded-card space-y-3">
          <input
            v-model="newSaga.name"
            type="text"
            placeholder="Nom de la saga"
            class="w-full px-4 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink"
          />
          <div class="flex gap-2">
            <AppButton size="sm" @click="addNewSaga">Créer</AppButton>
            <AppButton size="sm" variant="ghost" @click="showNewSaga = false; newSaga = { name: '' }">Annuler</AppButton>
          </div>
        </div>
      </div>

      <!-- Infos -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">ISBN</label>
          <input v-model="form.isbn" type="text" class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink" />
          <p v-if="isbnError" class="text-sm text-tag-tw-fg mt-1">{{ isbnError }}</p>
        </div>
        <div>
          <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Éditeur</label>
          <input v-model="form.publisher" type="text" class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink" />
        </div>
        <div>
          <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Date de publication</label>
          <input v-model="form.published_date" type="date" class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink" />
        </div>
        <div>
          <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Pages</label>
          <input v-model.number="form.page_count" type="number" class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink" />
        </div>
        <div>
          <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Langue</label>
          <select v-model="form.language" class="w-full px-4 py-3 bg-surface border border-line rounded-btn text-body text-ink">
            <option value="fr">Français</option>
            <option value="en">English</option>
            <option value="es">Español</option>
            <option value="de">Deutsch</option>
            <option value="it">Italiano</option>
            <option value="other">Autre</option>
          </select>
        </div>
      </div>

      <!-- Couverture -->
      <div>
        <label class="block text-label text-ink-3 uppercase tracking-wider mb-2">Couverture</label>
        <input type="file" accept="image/*" @change="handleUpload" class="text-body text-ink-2" />
        <div v-if="coverPreview" class="mt-2 w-32">
          <img :src="coverPreview" class="rounded-sm shadow-md" />
        </div>
      </div>

      <!-- Édition initiale -->
      <div>
        <button type="button" @click="showEdition = !showEdition" class="text-body text-accent hover:underline">
          {{ showEdition ? '- Masquer' : '+ Créer une première édition' }}
        </button>
        <div v-if="showEdition" class="mt-3 grid grid-cols-2 gap-4 p-4 bg-surface border border-line rounded-card">
          <div>
            <label class="block text-label text-ink-3 mb-1">Format</label>
            <select v-model="edition.format" class="w-full px-3 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink">
              <option value="broché">Broché</option>
              <option value="poche">Poche</option>
              <option value="numérique">Numérique</option>
              <option value="audio">Audio</option>
            </select>
          </div>
          <div>
            <label class="block text-label text-ink-3 mb-1">ISBN</label>
            <input v-model="edition.isbn" type="text" class="w-full px-3 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink" />
          </div>
          <div>
            <label class="block text-label text-ink-3 mb-1">Pages</label>
            <input v-model.number="edition.page_count" type="number" class="w-full px-3 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink" />
          </div>
          <div>
            <label class="block text-label text-ink-3 mb-1">Langue</label>
            <select v-model="edition.language" class="w-full px-3 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink">
              <option value="fr">Français</option>
              <option value="en">English</option>
            </select>
          </div>
          <div>
            <label class="block text-label text-ink-3 mb-1">Date</label>
            <input v-model="edition.published_date" type="date" class="w-full px-3 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink" />
          </div>
          <div>
            <label class="block text-label text-ink-3 mb-1">Éditeur</label>
            <input v-model="edition.publisher" type="text" class="w-full px-3 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink" />
          </div>
          <div class="col-span-2">
            <label class="block text-label text-ink-3 mb-1">Traducteur</label>
            <input v-model="edition.translator" type="text" class="w-full px-3 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink" />
          </div>
        </div>
      </div>

      <!-- Erreur -->
      <div v-if="error" class="bg-tag-tw-bg text-tag-tw-fg p-4 rounded-btn text-body">{{ error }}</div>

      <!-- Submit -->
      <AppButton type="submit" :disabled="submitting" class="w-full">
        <Loader2 v-if="submitting" class="w-4 h-4 animate-spin" />
        {{ submitting ? 'Création en cours...' : 'Ajouter le livre' }}
      </AppButton>
    </form>
  </div>
</template>

<script setup>
import { Loader2 } from 'lucide-vue-next'

definePageMeta({ layout: 'default', middleware: 'auth' })

const { fetch } = useApi()
const { getGenres } = useGenres()
const router = useRouter()
const coverFile = ref(null)
const isbnError = ref('')

const handleUpload = (e) => {
  const file = e.target.files?.[0]
  if (!file) return
  coverFile.value = file
  coverPreview.value = URL.createObjectURL(file)
}

const form = reactive({
  title: '',
  authors: [],
  genres: [] ,
  summary: '',
  saga_id: null ,
  isbn: '',
  publisher: '',
  published_date: '',
  page_count: null ,
  language: 'fr',
  cover_image: null 
})

const edition = reactive({
  format: 'broché',
  isbn: '',
  page_count: null,
  language: 'fr',
  published_date: '',
  publisher: '',
  translator: ''
})

const genres = ref([])
const authorSearch = ref('')
const authorResults = ref([])
const showNewAuthor = ref(false)
const newAuthor = reactive({ name: '', biography: '' })
const sagaSearch = ref('')
const sagaResults = ref([])
const showNewSaga = ref(false)
const newSaga = reactive({ name: '' })
const showEdition = ref(false)
const coverPreview = ref('')
const submitting = ref(false)
const error = ref('')

const searchAuthors = async () => {
  if (authorSearch.value.length < 2) { authorResults.value = []; return }
  try {
    const res = await fetch(`/authors?q=${encodeURIComponent(authorSearch.value)}`)
    authorResults.value = (res.data || []).slice(0, 5)
  } catch (e) { authorResults.value = [] }
}

const selectAuthor = (author) => {
  if (!form.authors.find(a => a.id === author.id)) {
    form.authors.push(author)
  }
}

const addNewAuthor = () => {
  if (newAuthor.name.trim()) {
    form.authors.push({ name: newAuthor.name, biography: newAuthor.biography, _new: true })
    showNewAuthor.value = false
    newAuthor.name = ''
    newAuthor.biography = ''
  }
}

const searchSagas = async () => {
  if (sagaSearch.value.length < 2) { sagaResults.value = []; return }
  try {
    const res = await fetch(`/sagas?q=${encodeURIComponent(sagaSearch.value)}`)
    sagaResults.value = (res.data || []).slice(0, 5)
  } catch (e) { sagaResults.value = [] }
}

const addNewSaga = () => {
  if (newSaga.name.trim()) {
    form.saga_id = null // sera créé côté back
    sagaSearch.value = newSaga.name
    form._newSaga = newSaga.name
    showNewSaga.value = false
    newSaga.name = ''
  }
}

const handleSubmit = async () => {
  if (!form.title.trim()) { error.value = 'Le titre est obligatoire'; return }
  if (!form.authors.length) { error.value = 'Au moins un auteur est requis'; return }

  submitting.value = true
  error.value = ''
  isbnError.value = ''

  try {
    let coverUrl = null
    if (coverFile.value) {
      const uploadFormData = new FormData()
      uploadFormData.append('file', coverFile.value)
      const uploadRes = await fetch('/upload', { method: 'POST', body: uploadFormData })
      coverUrl = uploadRes.data?.url || uploadRes.url
    }

    const body = { ...form }
    if (coverUrl) body.cover_url = coverUrl
    if (form._newSaga) {
      body.saga_name = form._newSaga
      delete body._newSaga
    }
    if (showEdition.value) {
      body.edition = { ...edition }
    }
    body.authors = form.authors.map(a => a._new ? a.name : a.id)
    body.genres = form.genres || []

    const res = await fetch('/books', { method: 'POST', body })
    const book = res.data?.book || res.data || res
    if (book?.id) {
      router.push(`/books/${book.id}`)
    } else {
      router.push('/books')
    }
  } catch (e) {
    error.value = e.message || 'Erreur lors de la création'
    if (e.errors) {
      if (e.errors.isbn) isbnError.value = e.errors.isbn[0]
      if (e.errors.title) error.value = e.errors.title[0]
    }
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  genres.value = await getGenres()
})
</script>