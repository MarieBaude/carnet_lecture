<template>
  <aside class="w-[264px] flex-shrink-0 sticky top-0 h-screen border-r border-line sidebar-gradient flex flex-col p-6">
    <!-- Logo -->
    <div class="pb-4 border-b border-line">
      <NuxtLink to="/" class="font-serif italic font-semibold text-2xl text-accent">
        Carnet de lecture
      </NuxtLink>
    </div>

    <!-- Pseudo + Avatar -->
    <div class="flex flex-col items-center text-center gap-2 py-4">
      <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#e7c8a8] via-[#c9a279] to-[#8e6a48] border-3 border-surface shadow-md grid place-items-center">
        <span class="font-serif italic text-xl text-white drop-shadow-sm">{{ userInitial }}</span>
      </div>
      <p class="font-serif text-lg font-semibold text-ink leading-tight">{{ authStore.user?.name }}</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-1 py-3 border-y border-line mb-4">
      <div class="text-center">
        <div class="font-serif font-semibold text-accent">{{ stats.pal || 0 }}</div>
        <div class="text-[10px] text-ink-3 flex items-center justify-center gap-1">
          <Bookmark class="w-3 h-3" /> PAL
        </div>
      </div>
      <div class="text-center">
        <div class="font-serif font-semibold text-accent">{{ stats.catalogue || 0 }}</div>
        <div class="text-[10px] text-ink-3 flex items-center justify-center gap-1">
          <Library class="w-3 h-3" /> Catalogue
        </div>
      </div>
      <div class="text-center">
        <div class="font-serif font-semibold text-accent">{{ stats.friends || 0 }}</div>
        <div class="text-[10px] text-ink-3 flex items-center justify-center gap-1">
          <Users class="w-3 h-3" /> Amis
        </div>
      </div>
    </div>

    <!-- Lecture en cours -->
    <div class="mb-4">
      <div v-if="featuredReading" class="bg-surface border border-line rounded-card p-3">
        <div class="flex gap-3">
          <NuxtLink :to="`/books/${featuredReading.book?.id || featuredReading.id}`" class="flex-shrink-0">
            <AppBookCover :variant="featuredReading.book?.cover_variant || 1" size="sm" />
          </NuxtLink>
          <div class="flex-1 min-w-0">
            <NuxtLink :to="`/books/${featuredReading.book?.id || featuredReading.id}`">
              <p class="font-serif text-sm text-ink font-semibold truncate">{{ featuredReading.book?.title || featuredReading.title }}</p>
            </NuxtLink>
            <p class="text-[11px] text-ink-3 truncate">{{ featuredReading.book?.authors?.map(a => a.name).join(', ') }}</p>
            <div class="mt-2">
              <div class="flex justify-between text-[10px] text-ink-3 mb-0.5">
                <span>{{ featuredReading.current_page || 0 }} / {{ featuredReading.book?.page_count || '?' }}</span>
                <span>{{ readingPercent }}%</span>
              </div>
              <div class="w-full h-1 bg-bg-2 rounded-full overflow-hidden">
                <div class="h-full bg-accent rounded-full transition-all" :style="{ width: readingPercent + '%' }" />
              </div>
            </div>
            <button
              @click="finishReading"
              :disabled="finishing"
              class="mt-2 w-full text-[11px] text-accent hover:text-[#7a2f2f] font-medium transition-colors"
            >
              <Loader2 v-if="finishing" class="w-3 h-3 animate-spin inline" />
              <span v-else>Marquer comme lu</span>
            </button>
          </div>
        </div>
      </div>
      <NuxtLink
        v-else
        to="/library"
        class="block text-center text-sm text-ink-3 hover:text-accent transition-colors italic"
      >
        Ajouter une lecture en cours
      </NuxtLink>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-col gap-0.5">
      <span class="text-label text-ink-3 uppercase tracking-wider px-2 pb-2">Navigation</span>

      <NuxtLink v-for="item in navItems" :key="item.to" :to="item.to" :class="[
        'flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-colors',
        route.path === item.to
          ? 'bg-accent text-white shadow-md'
          : 'text-ink-2 hover:bg-accent-soft/10 hover:text-ink'
      ]">
        <component :is="item.icon" class="w-4 h-4" />
        {{ item.label }}
        <span v-if="item.count !== undefined" :class="[
          'ml-auto text-[11px]',
          route.path === item.to ? 'text-white/80' : 'text-ink-3'
        ]">{{ item.count }}</span>
      </NuxtLink>
    </nav>

    <!-- Boutons d'ajout -->
    <div class="mt-4 space-y-2">
      <NuxtLink to="/books/create" class="flex items-center justify-center gap-2 w-full py-2 rounded-btn border border-line text-sm text-ink-2 hover:border-accent-soft hover:text-accent transition-colors">
        <Plus class="w-3.5 h-3.5" />
        Ajouter un livre
      </NuxtLink>
      <NuxtLink to="/sagas/create" class="flex items-center justify-center gap-2 w-full py-2 rounded-btn border border-line text-sm text-ink-2 hover:border-accent-soft hover:text-accent transition-colors">
        <Plus class="w-3.5 h-3.5" />
        Ajouter une saga
      </NuxtLink>
    </div>

    <!-- Déconnexion -->
    <button
      @click="authStore.logout()"
      class="mt-auto flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm text-ink-3 hover:text-accent hover:bg-accent-soft/10 transition-colors"
    >
      <LogOut class="w-4 h-4" />
      Déconnexion
    </button>
  </aside>
</template>

<script setup>
import { BookOpen, Library, Bookmark, User, Users, Activity, LogOut, Plus, Loader2 } from 'lucide-vue-next'

const authStore = useAuthStore()
const route = useRoute()
const { getStats, getBooks, updateBook } = useLibrary()
const { getFollowing, getMyProfile } = useProfile()
const { fetch } = useApi()

const stats = ref({ pal: 0, catalogue: 0, friends: 0 })
const featuredReading = ref(null)
const finishing = ref(false)

const userInitial = computed(() => authStore.user?.name?.charAt(0).toUpperCase() || '?')

const readingPercent = computed(() => {
  if (!featuredReading.value) return 0
  const current = featuredReading.value.current_page || 0
  const total = featuredReading.value.book?.page_count || 1
  return Math.round((current / total) * 100)
})

const navItems = [
  { label: 'Accueil', to: '/', icon: BookOpen },
  { label: 'Catalogue', to: '/books', icon: Library },
  { label: 'Bibliothèque', to: '/library', icon: Bookmark },
  { label: 'Profil', to: '/profile', icon: User },
  { label: 'Amis', to: '/friends', icon: Users },
  { label: 'Activité', to: '/activity', icon: Activity }
]

const loadStats = async () => {
  try {
    const [wishlistRes, booksRes, followingRes] = await Promise.all([
      getBooks({ status: 'wishlist', per_page: 1 }),
      fetch('/books?per_page=1'),
      getFollowing({ per_page: 1 })
    ])
    stats.value = {
      pal: wishlistRes.meta?.total || 0,
      catalogue: booksRes.meta?.total || 0,
      friends: followingRes.meta?.total || 0
    }
  } catch (e) { /* ignore */ }
}

const loadFeaturedReading = async () => {
  try {
    const data = await getMyProfile()
    featuredReading.value = data.featured_reading || null
  } catch (e) { /* ignore */ }
}

const finishReading = async () => {
  if (!featuredReading.value) return
  finishing.value = true
  try {
    await updateBook(featuredReading.value.id, { status: 'read' })
    featuredReading.value = null
    loadStats()
  } catch (e) { /* ignore */ }
  finally { finishing.value = false }
}

onMounted(() => {
  loadStats()
  loadFeaturedReading()
})
</script>