<template>
  <div>
    <h1 class="font-serif text-section text-ink mb-6">Fil d'activité</h1>

    <!-- Filtres -->
    <div class="flex gap-2 mb-6 overflow-x-auto">
      <button
        v-for="f in filters"
        :key="f.value"
        @click="activeFilter = f.value; currentPage = 1; loadActivity()"
        :class="[
          'px-3 py-1.5 text-sm rounded-full transition-colors whitespace-nowrap',
          activeFilter === f.value ? 'bg-accent text-white' : 'bg-bg-2 text-ink-2 hover:bg-line'
        ]"
      >
        {{ f.label }}
      </button>
    </div>

    <ActivityTimeline :activities="activities" :loading="loading" />

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

const { getActivity } = useProfile()
const { fetch } = useApi()

const activities = ref([])
const loading = ref(true)
const currentPage = ref(1)
const totalPages = ref(1)
const activeFilter = ref('')

const filters = [
  { label: 'Tous', value: '' },
  { label: 'Lectures terminées', value: 'reading_finished' },
  { label: 'Notes', value: 'rated' },
  { label: 'Commentaires', value: 'commented' }
]

const loadActivity = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams({ page: currentPage.value, per_page: 20 })
    if (activeFilter.value) params.append('type', activeFilter.value)
    const res = await fetch(`/me/activity?${params}`)
    activities.value = res.data?.activities || res.data || []
    totalPages.value = res.meta?.last_page || 1
  } catch (e) {
    activities.value = []
  } finally {
    loading.value = false
  }
}

const goToPage = (page) => {
  currentPage.value = page
  loadActivity()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

onMounted(loadActivity)
</script>