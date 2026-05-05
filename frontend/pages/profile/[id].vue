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
          <p class="text-meta text-ink-3">
            Membre depuis {{ formatDate(profile.created_at) }}
          </p>

          <!-- Follow button -->
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

    <!-- Derniers livres -->
    <section>
      <h2 class="font-serif text-2xl text-ink mb-4">Derniers livres lus</h2>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <NuxtLink
          v-for="book in recentBooks"
          :key="book.id"
          :to="`/books/${book.id}`"
          class="bg-surface border border-line rounded-card p-3 hover:-translate-y-0.5 hover:shadow-md transition-all"
        >
          <AppBookCover :variant="book.cover_variant" size="sm" />
          <h4 class="font-serif text-sm text-ink mt-2 truncate">{{ book.title }}</h4>
          <div class="flex items-center gap-1 mt-1">
            <AppStarRating v-if="book.library?.rating" :rating="book.library.rating" />
          </div>
        </NuxtLink>
        <AppEmptyState
          v-if="!recentBooks.length"
          title="Aucun livre récent"
        />
      </div>
    </section>
  </div>
</template>

<script setup>
import { Loader2 } from 'lucide-vue-next'

definePageMeta({ layout: 'default' })

const route = useRoute()
const { getUserProfile, follow, unfollow } = useProfile()
const authStore = useAuthStore()

const profile = ref(null)
const recentBooks = ref([])
const isFollowing = ref(false)
const followLoading = ref(false)

const isMe = computed(() => authStore.user?.id === profile.value?.id)

const initials = computed(() => profile.value?.name?.charAt(0).toUpperCase() || '?')

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long' })
}

const loadProfile = async () => {
  try {
    const data = await getUserProfile(route.params.id)
    profile.value = data.user || data
    recentBooks.value = data.recent_books || []
    isFollowing.value = data.is_following || false
  } catch (e) { console.error(e) }
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

onMounted(loadProfile)
</script>