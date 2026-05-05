<template>
  <div>
    <!-- Non connecté -->
    <div v-if="!authStore.isAuthenticated" class="flex items-center justify-center min-h-[80vh]">
      <div class="text-center max-w-md">
        <h1 class="font-serif text-5xl text-ink mb-4">Carnet de Lecture</h1>
        <p class="text-synopsis text-ink-2 mb-2">Votre bibliothèque personnelle</p>
        <p class="text-body text-ink-3 mb-8">
          Suivez vos lectures, découvrez des livres, partagez vos avis et échangez avec une communauté de passionnés.
        </p>
        <div class="flex gap-3 justify-center">
          <NuxtLink to="/login" class="bg-accent text-white font-medium px-8 py-3 rounded-btn shadow-btn hover:shadow-btn-hover hover:-translate-y-0.5 transition-all inline-block">
            Créer un compte
          </NuxtLink>
          <NuxtLink to="/login" class="border border-accent text-accent font-medium px-8 py-3 rounded-btn hover:bg-accent-softer transition-all inline-block">
            Se connecter
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Connecté -->
    <div v-else>
      <!-- Activités des amis -->
      <section class="mb-10">
        <h2 class="font-serif text-section text-ink mb-4">Activités de vos amis</h2>
        <ActivityTimeline v-if="friendsActivity.length" :activities="friendsActivity" :loading="false" />
        <AppEmptyState
          v-else-if="!loading"
          title="Aucune activité"
          description="Ajoutez des amis pour voir leur activité."
          action-label="Découvrir des lecteurs"
        />
        <div v-if="loading" class="space-y-4">
          <div v-for="i in 3" :key="i" class="flex gap-4 animate-pulse">
            <div class="w-8 h-8 bg-bg-2 rounded-full" />
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-bg-2 rounded w-3/4" />
              <div class="h-3 bg-bg-2 rounded w-1/4" />
            </div>
          </div>
        </div>
      </section>

      <!-- Derniers livres -->
      <section class="mb-10">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-serif text-section text-ink">Derniers livres ajoutés</h2>
          <NuxtLink to="/books" class="text-body text-accent hover:text-[#7a2f2f] font-medium">
            Voir tout le catalogue →
          </NuxtLink>
        </div>
        <div v-if="latestBooks.length" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <BookCard v-for="book in latestBooks" :key="book.id" :book="book" />
        </div>
        <div v-else-if="loading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div v-for="i in 6" :key="i" class="bg-surface rounded-card p-3 animate-pulse">
            <div class="aspect-[2/3] bg-bg-2 rounded-sm" />
            <div class="h-4 bg-bg-2 rounded mt-2 w-3/4" />
            <div class="h-3 bg-bg-2 rounded mt-1 w-1/2" />
          </div>
        </div>
      </section>

      <!-- Mieux notés -->
      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-serif text-section text-ink">Les mieux notés</h2>
          <NuxtLink to="/books?sort=rating-desc" class="text-body text-accent hover:text-[#7a2f2f] font-medium">
            Voir tout le catalogue →
          </NuxtLink>
        </div>
        <div v-if="popularBooks.length" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <BookCard v-for="book in popularBooks" :key="book.id" :book="book" />
        </div>
        <div v-else-if="loading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          <div v-for="i in 6" :key="i" class="bg-surface rounded-card p-3 animate-pulse">
            <div class="aspect-[2/3] bg-bg-2 rounded-sm" />
            <div class="h-4 bg-bg-2 rounded mt-2 w-3/4" />
            <div class="h-3 bg-bg-2 rounded mt-1 w-1/2" />
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
const authStore = useAuthStore()
const { getHome } = useHome()

const loading = ref(true)
const friendsActivity = ref([])
const latestBooks = ref([])
const popularBooks = ref([])

const loadHome = async () => {
  if (!authStore.isAuthenticated) return
  loading.value = true
  try {
    const res = await getHome()
    const data = res.data || res
    friendsActivity.value = data.friends_activity || []
    latestBooks.value = data.latest_books?.slice(0, 6) || []
    popularBooks.value = data.popular_books?.slice(0, 6) || []
  } catch (e) {
    friendsActivity.value = []
    latestBooks.value = []
    popularBooks.value = []
  } finally {
    loading.value = false
  }
}

watch(() => authStore.isAuthenticated, (val) => {
  if (val) loadHome()
})

onMounted(() => {
  if (authStore.isAuthenticated) loadHome()
})
</script>