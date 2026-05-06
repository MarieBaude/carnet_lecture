<template>
  <div v-if="book">
    <AppBreadcrumb :items="breadcrumbs" class="mb-6" />

    <div class="flex flex-col lg:flex-row gap-8 mb-10">
      <!-- Couverture -->
      <div class="flex-shrink-0">
        <AppBookCover :cover-url="book.cover_url" size="lg" :tome="book.saga?.tome_number" />
      </div>

      <!-- Infos -->
      <div class="flex-1">
        <h1 class="font-serif text-book-title text-ink mb-2">{{ book.title }}</h1>

        <div class="flex items-center gap-2 mb-4">
          <span class="text-synopsis text-ink-2">
            Par {{book.authors?.map(a => a.name).join(', ')}}
          </span>
        </div>

        <!-- Note moyenne -->
        <div class="flex items-center gap-3 mb-4">
          <AppStarRating :rating="Math.round(stats.average_rating || 0)" size="md" />
          <span class="text-body text-ink-2">
            {{ stats.average_rating || '-' }}
            <button @click="scrollToComments" class="text-ink-3 hover:text-accent underline underline-offset-2">
              ({{ stats.ratings_count || 0 }} avis)
            </button>
          </span>
        </div>

        <!-- Saga -->
        <div v-if="book.saga" class="mb-4">
          <span class="text-label text-ink-3 uppercase tracking-wider">Saga</span>
          <p class="text-body text-ink mt-1">
            <NuxtLink :to="`/sagas/${book.saga.id}`" class="text-accent hover:underline">
              {{ book.saga.name }}
            </NuxtLink>
            — Tome {{ book.saga.tome_number }}
          </p>
        </div>

        <!-- Métadonnées -->
        <div class="grid grid-cols-2 gap-4 mb-6">
          <div>
            <span class="text-label text-ink-3 uppercase">Éditeur</span>
            <p class="text-body text-ink">{{ book.publisher || 'Inconnu' }}</p>
          </div>
          <div>
            <span class="text-label text-ink-3 uppercase">Pages</span>
            <p class="text-body text-ink">{{ book.page_count }}</p>
          </div>
          <div>
            <span class="text-label text-ink-3 uppercase">Date de publication</span>
            <p class="text-body text-ink">{{ formatDate(book.published_date) }}</p>
          </div>
          <div>
            <span class="text-label text-ink-3 uppercase">Langue</span>
            <p class="text-body text-ink">{{ book.language === 'fr' ? 'Français' : book.language }}</p>
          </div>
        </div>

        <!-- Genres -->
        <div class="flex flex-wrap gap-2 mb-6">
          <AppTag v-for="genre in book.genres" :key="genre.id" :label="genre.name" category="genre" />
        </div>

        <!-- Actions -->
        <div class="flex gap-3 mb-6">
          <AppButton variant="primary" icon="BookmarkPlus" @click="showModal = true">
            {{ book.user_status ? 'Modifier' : 'Ajouter à ma bibliothèque' }}
          </AppButton>
          <AppButton variant="outline" icon="Bookmark" @click="toggleWishlist" :disabled="isInWishlist">
            {{ isInWishlist ? 'Dans votre wishlist ✓' : 'Ajouter à ma wishlist' }}
          </AppButton>
        </div>

        <!-- Notation -->
        <div class="bg-surface-2 border border-line rounded-card p-4">
          <p class="text-label text-ink-3 uppercase tracking-wider mb-2">{{ userRating ? 'Votre note' : 'Noter ce livre'
          }}</p>
          <AppStarRating :rating="userRating" size="md" interactive @rate="rateBook" />
          <p v-if="rateError" class="text-sm text-tag-tw-fg mt-1">{{ rateError }}</p>
        </div>
      </div>
    </div>

    <!-- Synopsis -->
    <section class="mb-10">
      <h2 class="font-serif text-section text-ink mb-4">Synopsis</h2>
      <p class="text-synopsis text-ink-2 leading-relaxed">{{ book.summary || 'Aucun résumé disponible.' }}</p>
    </section>

    <!-- Lien ajout édition -->
    <div class="mb-6">
      <NuxtLink :to="`/books/${book.id}/editions/create`"
        class="inline-flex items-center gap-2 text-body text-accent hover:text-[#7a2f2f] font-medium">
        <Plus class="w-4 h-4" />
        Ajouter une édition
      </NuxtLink>
    </div>

    <!-- Onglets -->
    <section id="comments">
      <AppTabGroup :tabs="[
        { label: 'Avis', count: comments.length, value: 'reviews' },
        { label: 'Détails', count: undefined, value: 'details' }
      ]" v-model="activeTab" />

      <div class="mt-6">
        <!-- Avis -->
        <div v-if="activeTab === 'reviews'" class="space-y-4">
          <CommentCard v-for="comment in comments" :key="comment.id" :comment="comment" />

          <AppEmptyState v-if="!comments.length && !commentsLoading" title="Aucun avis"
            description="Soyez le premier à donner votre avis." />

          <!-- Formulaire -->
          <div v-if="authStore.isAuthenticated">
            <CommentForm v-if="book.user_status" :book-id="book.id" @published="loadComments" />
            <p v-else class="text-body text-ink-3 italic">
              Ajoutez ce livre à votre bibliothèque pour donner votre avis.
            </p>
          </div>
          <p v-else class="text-body text-ink-3 italic">
            <NuxtLink to="/login" class="text-accent hover:underline">Connectez-vous</NuxtLink> pour donner votre avis.
          </p>
        </div>

        <!-- Détails -->
        <div v-if="activeTab === 'details'" class="grid grid-cols-2 gap-6">
          <div>
            <h3 class="font-serif text-lg text-ink mb-2">Informations</h3>
            <dl class="space-y-2 text-body">
              <div class="flex justify-between">
                <dt class="text-ink-3">ISBN</dt>
                <dd class="text-ink">{{ book.isbn || 'Inconnu' }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-ink-3">Format</dt>
                <dd class="text-ink">{{ book.format || 'Inconnu' }}</dd>
              </div>
            </dl>
          </div>
          <div>
            <h3 class="font-serif text-lg text-ink mb-2">Genres</h3>
            <div class="flex flex-wrap gap-2">
              <AppTag v-for="genre in book.genres" :key="genre.id" :label="genre.name" category="genre" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Modale bibliothèque -->
    <AddToLibraryModal :open="showModal" :book="book" @close="showModal = false" @saved="loadBook()" />
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })
import { Plus } from 'lucide-vue-next'

const route = useRoute()
const { fetch } = useApi()
const { addToLibrary } = useLibrary()
const authStore = useAuthStore()

const book = ref(null)
const stats = ref({})
const comments = ref([])
const commentsLoading = ref(true)
const activeTab = ref('reviews')
const showModal = ref(false)
const userRating = ref(0)
const rateError = ref('')

const breadcrumbs = computed(() => [
  { label: 'Catalogue', to: '/books' },
  { label: book.value?.title || '' }
])

const isInWishlist = computed(() => book.value?.user_status?.status === 'wishlist')

const formatDate = (date) => {
  if (!date) return 'Inconnue'
  return new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' })
}

const loadBook = async () => {
  try {
    const response = await fetch(`/books/${route.params.id}`)
    book.value = response.data?.book || response.data
    userRating.value = book.value?.user_status?.rating || 0
  } catch (e) { console.error(e) }
}

const loadStats = async () => {
  try {
    const res = await fetch(`/books/${route.params.id}/stats`)
    stats.value = res.data || res
  } catch (e) { /* ignore */ }
}

const loadComments = async () => {
  commentsLoading.value = true
  try {
    const res = await fetch(`/books/${route.params.id}/comments`)
    comments.value = res.data?.comments || res.data || []
  } catch (e) {
    comments.value = []
  } finally {
    commentsLoading.value = false
  }
}

const rateBook = async (rating) => {
  rateError.value = ''
  try {
    await fetch(`/books/${route.params.id}/rate`, {
      method: 'POST',
      body: { rating }
    })
    userRating.value = rating
    loadStats()
  } catch (e) {
    rateError.value = e.message || 'Erreur'
  }
}

const toggleWishlist = async () => {
  try {
    await addToLibrary(book.value.id, { status: 'wishlist' })
    loadBook()
  } catch (e) { /* ignore */ }
}

const scrollToComments = () => {
  activeTab.value = 'reviews'
  document.getElementById('comments')?.scrollIntoView({ behavior: 'smooth' })
}

onMounted(() => {
  loadBook()
  loadStats()
  loadComments()
})
</script>