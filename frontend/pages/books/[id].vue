<template>
  <div v-if="book">
    <AppBreadcrumb :items="[
      { label: 'Catalogue', to: '/books' },
      { label: book.title }
    ]" class="mb-6" />
    
    <div class="flex gap-8 mb-10">
      <div class="flex-shrink-0">
        <AppBookCover :variant="book.cover_variant" size="lg" :tome="book.saga?.tome_number" />
      </div>
      
      <div class="flex-1">
        <h1 class="font-serif text-book-title text-ink mb-2">{{ book.title }}</h1>
        
        <div class="flex items-center gap-2 mb-4">
          <span class="text-synopsis text-ink-2">
            Par {{ book.authors?.map(a => a.name).join(', ') }}
          </span>
        </div>
        
        <div class="flex items-center gap-3 mb-4">
          <AppStarRating :rating="book.stats?.average_rating || 0" size="md" />
          <span class="text-body text-ink-2">
            {{ book.stats?.average_rating || 'Aucune note' }} 
            ({{ book.stats?.readers_count || 0 }} lecteurs)
          </span>
        </div>
        
        <div v-if="book.saga" class="mb-4">
          <span class="text-label text-ink-3 uppercase tracking-wider">Saga</span>
          <p class="text-body text-ink mt-1">{{ book.saga.name }} — Tome {{ book.saga.tome_number }}</p>
        </div>
        
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
        
        <div class="flex flex-wrap gap-2 mb-6">
          <AppTag v-for="genre in book.genres" :key="genre.id" :label="genre.name" category="genre" />
        </div>
        
        <div class="flex gap-3">
          <AppButton variant="primary" icon="BookmarkPlus">
            Ajouter à ma bibliothèque
          </AppButton>
          <AppIconButton icon="Heart" />
          <AppIconButton icon="Share2" />
        </div>
      </div>
    </div>
    
    <section class="mb-10">
      <h2 class="font-serif text-section text-ink mb-4">Synopsis</h2>
      <p class="text-synopsis text-ink-2 leading-relaxed">{{ book.summary || 'Aucun résumé disponible.' }}</p>
    </section>
    
    <section>
      <AppTabGroup 
        :tabs="[
          { label: 'Avis', count: 0, value: 'reviews' },
          { label: 'Chroniques', count: 0, value: 'chronicles' }
        ]"
        v-model="activeTab"
      />
      <div class="mt-6">
        <AppEmptyState 
          v-if="activeTab === 'reviews'"
          title="Aucun avis pour le moment"
          description="Soyez le premier à donner votre avis."
        />
        <AppEmptyState 
          v-else
          title="Aucune chronique"
          description="Les chroniques apparaîtront ici."
        />
      </div>
    </section>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default' })

const route = useRoute()
const { fetch } = useApi()
const book = ref(null)
const activeTab = ref('reviews')

const formatDate = (date) => {
  if (!date) return 'Inconnue'
  return new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' })
}

const loadBook = async () => {
  try {
    const response = await fetch(`/books/${route.params.id}`)
    book.value = response.data.book
  } catch (e) {
    console.error(e)
  }
}

onMounted(loadBook)
</script>