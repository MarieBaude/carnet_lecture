<template>
  <div>
    <h1 class="font-serif text-section text-ink mb-8">Sagas</h1>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <div v-for="i in 6" :key="i" class="bg-surface rounded-card p-5 animate-pulse">
        <div class="h-6 bg-bg-2 rounded w-2/3 mb-3" />
        <div class="h-4 bg-bg-2 rounded w-full mb-2" />
        <div class="h-4 bg-bg-2 rounded w-1/2" />
      </div>
    </div>

    <div v-else-if="sagas.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <NuxtLink
        v-for="saga in sagas"
        :key="saga.id"
        :to="`/sagas/${saga.id}`"
        class="bg-surface border border-line rounded-card shadow-sm p-5 hover:-translate-y-0.5 hover:shadow-md hover:border-accent-soft transition-all"
      >
        <h2 class="font-serif text-card-title text-ink mb-2">{{ saga.name }}</h2>
        <p class="text-body text-ink-2 line-clamp-2 mb-3">{{ saga.description || 'Aucune description' }}</p>
        <span class="text-meta text-ink-3">{{ saga.books_count || 0 }} tome(s)</span>
      </NuxtLink>
    </div>

    <AppEmptyState v-else title="Aucune saga" description="Créez la première saga !" action-label="Créer une saga" />

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
definePageMeta({ layout: 'default' })

const { fetch } = useApi()

const sagas = ref([])
const loading = ref(true)
const currentPage = ref(1)
const totalPages = ref(1)

const loadSagas = async () => {
  loading.value = true
  try {
    const res = await fetch(`/sagas?page=${currentPage.value}&per_page=12`)
    sagas.value = res.data?.sagas || res.data || []
    totalPages.value = res.meta?.last_page || 1
  } catch (e) {
    sagas.value = []
  } finally {
    loading.value = false
  }
}

const goToPage = (page) => {
  currentPage.value = page
  loadSagas()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(loadSagas)
</script>