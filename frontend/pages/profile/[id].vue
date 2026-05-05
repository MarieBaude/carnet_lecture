<template>
  <div v-if="profile">
    <!-- Header -->
    <div class="bg-surface border border-line rounded-card shadow-sm p-8 mb-8">
      <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-[#e7c8a8] via-[#c9a279] to-[#8e6a48] border-4 border-surface shadow-md grid place-items-center flex-shrink-0">
          <span class="font-serif italic text-3xl text-white drop-shadow-sm">{{ initials }}</span>
        </div>

        <div class="flex-1 text-center sm:text-left">
          <h1 class="font-serif text-3xl text-ink mb-1">{{ profile.name }}</h1>
          <p class="text-body text-ink-2 mb-3">{{ profile.bio || 'Aucune bio' }}</p>
          <p class="text-meta text-ink-3">Membre depuis {{ formatDate(profile.created_at) }}</p>

          <AppButton
            v-if="!isMe"
            class="mt-3"
            size="sm"
            :variant="isFollowing ? 'outline' : 'primary'"
            @click="toggleFollow"
            :disabled="followLoading"
          >
            <Loader2 v-if="followLoading" class="w-3.5 h-3.5 animate-spin" />
            {{ isFollowing ? 'Ne plus suivre' : 'Suivre' }}
          </AppButton>
        </div>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-8">
      <div class="bg-surface border border-line rounded-card p-5 text-center">
        <div class="font-serif text-3xl text-accent font-semibold">{{ profile.stats?.total_books || 0 }}</div>
        <div class="text-meta text-ink-3 mt-1">Livres</div>
      </div>
      <div class="bg-surface border border-line rounded-card p-5 text-center">
        <div class="font-serif text-3xl text-accent font-semibold">{{ profile.stats?.pages_read || 0 }}</div>
        <div class="text-meta text-ink-3 mt-1">Pages lues</div>
      </div>
      <div class="bg-surface border border-line rounded-card p-5 text-center">
        <div class="font-serif text-3xl text-accent font-semibold">{{ profile.stats?.average_rating || '-' }}</div>
        <div class="text-meta text-ink-3 mt-1">Note moyenne</div>
      </div>
    </div>

    <!-- Onglets -->
    <div class="border-b border-line mb-6">
      <div class="flex gap-0">
        <button
          v-for="tab in tabs"
          :key="tab.value"
          @click="switchTab(tab.value)"
          :class="tabClass(tab.value)"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Activité -->
    <div v-if="activeTab === 'activity'">
      <ActivityTimeline :activities="activities" :loading="loadingActivity" />
      <AppPagination
        v-if="activityTotalPages > 1"
        :current-page="activityPage"
        :last-page="activityTotalPages"
        @page-changed="goToActivityPage"
        class="mt-8"
      />
    </div>

    <!-- Bibliothèque -->
    <div v-if="activeTab === 'library'">
      <div v-if="isPrivate" class="text-center py-12">
        <p class="font-serif text-xl text-ink-3">Cette bibliothèque est privée</p>
      </div>
      <template v-else>
        <div class="flex gap-2 mb-4">
          <button
            v-for="s in libraryStatuses"
            :key="s.value"
            @click="libraryStatus = s.value; loadLibrary()"
            :class="[
              'px-3 py-1 text-xs rounded-full transition-colors',
              libraryStatus === s.value ? 'bg-accent text-white' : 'bg-bg-2 text-ink-2 hover:bg-line'
            ]"
          >
            {{ s.label }}
          </button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <BookCard v-for="book in libraryBooks" :key="book.id" :book="book" />
        </div>
        <AppEmptyState v-if="!libraryBooks.length && !loadingLibrary" title="Aucun livre" />
        <AppPagination
          v-if="libraryTotalPages > 1"
          :current-page="libraryPage"
          :last-page="libraryTotalPages"
          @page-changed="goToLibraryPage"
          class="mt-8"
        />
      </template>
    </div>

    <!-- Wishlist -->
    <div v-if="activeTab === 'wishlist'">
      <div v-if="isWishlistPrivate" class="text-center py-12">
        <p class="font-serif text-xl text-ink-3">Cette wishlist est privée</p>
      </div>
      <template v-else>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <BookCard v-for="book in wishlistBooks" :key="book.id" :book="book" />
        </div>
        <AppEmptyState v-if="!wishlistBooks.length && !loadingWishlist" title="Wishlist vide" />
        <AppPagination
          v-if="wishlistTotalPages > 1"
          :current-page="wishlistPage"
          :last-page="wishlistTotalPages"
          @page-changed="goToWishlistPage"
          class="mt-8"
        />
      </template>
    </div>
  </div>
</template>

<script setup>
import { Loader2 } from 'lucide-vue-next'

definePageMeta({ layout: 'default' })

const route = useRoute()
const { getUserProfile, follow, unfollow, getActivity, relativeTime } = useProfile()
const { fetch } = useApi()
const authStore = useAuthStore()

const profile = ref(null)
const isFollowing = ref(false)
const followLoading = ref(false)
const activeTab = ref('activity')

// Activité
const activities = ref([])
const loadingActivity = ref(true)
const activityPage = ref(1)
const activityTotalPages = ref(1)

// Bibliothèque
const libraryBooks = ref([])
const loadingLibrary = ref(false)
const libraryPage = ref(1)
const libraryTotalPages = ref(1)
const libraryStatus = ref('')
const isPrivate = ref(false)

// Wishlist
const wishlistBooks = ref([])
const loadingWishlist = ref(false)
const wishlistPage = ref(1)
const wishlistTotalPages = ref(1)
const isWishlistPrivate = ref(false)

const tabs = [
  { label: 'Activité', value: 'activity' },
  { label: 'Bibliothèque', value: 'library' },
  { label: 'Wishlist', value: 'wishlist' }
]

const libraryStatuses = [
  { label: 'Tous', value: '' },
  { label: 'En cours', value: 'reading' },
  { label: 'Lus', value: 'read' },
  { label: 'Abandonnés', value: 'dropped' }
]

const isMe = computed(() => authStore.user?.id === profile.value?.id)
const initials = computed(() => profile.value?.name?.charAt(0).toUpperCase() || '?')

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long' })
}

const tabClass = (tab) => {
  const base = 'tab-underline pb-3 px-1 mr-6 text-body font-medium transition-colors'
  return activeTab.value === tab ? `${base} active text-accent` : `${base} text-ink-3 hover:text-ink`
}

const switchTab = (tab) => {
  activeTab.value = tab
  if (tab === 'activity') loadActivity()
  if (tab === 'library') loadLibrary()
  if (tab === 'wishlist') loadWishlist()
}

const loadProfile = async () => {
  try {
    const data = await getUserProfile(route.params.id)
    profile.value = data.user || data
    isFollowing.value = data.is_following || false
  } catch (e) { console.error(e) }
}

const loadActivity = async () => {
  loadingActivity.value = true
  try {
    const res = await fetch(`/users/${route.params.id}/activity?page=${activityPage.value}&per_page=20`)
    activities.value = res.data?.activities || res.data || []
    activityTotalPages.value = res.meta?.last_page || 1
  } catch (e) {
    activities.value = []
  } finally {
    loadingActivity.value = false
  }
}

const loadLibrary = async () => {
  loadingLibrary.value = true
  try {
    const params = new URLSearchParams({ page: libraryPage.value, per_page: 12 })
    if (libraryStatus.value) params.append('status', libraryStatus.value)
    const res = await fetch(`/users/${route.params.id}/library?${params}`)
    if (res.data?.private) {
      isPrivate.value = true
      libraryBooks.value = []
    } else {
      isPrivate.value = false
      libraryBooks.value = res.data?.books || res.data || []
      libraryTotalPages.value = res.meta?.last_page || 1
    }
  } catch (e) {
    libraryBooks.value = []
  } finally {
    loadingLibrary.value = false
  }
}

const loadWishlist = async () => {
  loadingWishlist.value = true
  try {
    const res = await fetch(`/users/${route.params.id}/wishlist?page=${wishlistPage.value}&per_page=12`)
    if (res.data?.private) {
      isWishlistPrivate.value = true
      wishlistBooks.value = []
    } else {
      isWishlistPrivate.value = false
      wishlistBooks.value = res.data?.books || res.data || []
      wishlistTotalPages.value = res.meta?.last_page || 1
    }
  } catch (e) {
    wishlistBooks.value = []
  } finally {
    loadingWishlist.value = false
  }
}

const toggleFollow = async () => {
  followLoading.value = true
  try {
    if (isFollowing.value) {
      await unfollow(profile.value.id)
      isFollowing.value = false
    } else {
      await follow(profile.value.id)
      isFollowing.value = true
    }
  } catch (e) { /* ignore */ }
  finally { followLoading.value = false }
}

const goToActivityPage = (page) => { activityPage.value = page; loadActivity(); window.scrollTo({ top: 0, behavior: 'smooth' }) }
const goToLibraryPage = (page) => { libraryPage.value = page; loadLibrary(); window.scrollTo({ top: 0, behavior: 'smooth' }) }
const goToWishlistPage = (page) => { wishlistPage.value = page; loadWishlist(); window.scrollTo({ top: 0, behavior: 'smooth' }) }

onMounted(() => {
  loadProfile()
  loadActivity()
})
</script>