<template>
  <div v-if="profile">
    <!-- Header -->
    <div class="bg-surface border border-line rounded-card shadow-sm p-8 mb-8">
      <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <!-- Avatar -->
        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-[#e7c8a8] via-[#c9a279] to-[#8e6a48] border-4 border-surface shadow-md grid place-items-center flex-shrink-0">
          <span class="font-serif italic text-3xl text-white drop-shadow-sm">{{ initials }}</span>
        </div>

        <div class="flex-1 text-center sm:text-left">
          <!-- Édition inline -->
          <div v-if="editing" class="space-y-3">
            <input
              v-model="editForm.name"
              class="w-full px-4 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink focus:outline-none focus:border-accent-2"
              placeholder="Votre nom"
            />
            <textarea
              v-model="editForm.bio"
              rows="3"
              class="w-full px-4 py-2 bg-bg-2 border border-line rounded-btn text-body text-ink focus:outline-none focus:border-accent-2"
              placeholder="Parlez-nous de vous…"
            />
            <div class="flex gap-2">
              <AppButton size="sm" @click="saveProfile" :disabled="saving">
                <Loader2 v-if="saving" class="w-3.5 h-3.5 animate-spin" />
                Enregistrer
              </AppButton>
              <AppButton size="sm" variant="ghost" @click="editing = false">Annuler</AppButton>
            </div>
          </div>

          <!-- Affichage -->
          <template v-else>
            <h1 class="font-serif text-3xl text-ink mb-1">{{ profile.name }}</h1>
            <p class="text-body text-ink-2 mb-3">{{ profile.bio || 'Aucune bio' }}</p>
            <p class="text-meta text-ink-3">
              Membre depuis {{ formatDate(profile.created_at) }}
            </p>
            <AppButton size="sm" variant="outline" class="mt-3" @click="startEdit">
              <Pencil class="w-3.5 h-3.5" />
              Modifier le profil
            </AppButton>
          </template>
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
          @click="activeTab = tab.value"
          :class="[
            'tab-underline pb-3 px-1 mr-6 text-body font-medium transition-colors',
            activeTab === tab.value ? 'active text-accent' : 'text-ink-3 hover:text-ink'
          ]"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Bibliothèque mini -->
    <div v-if="activeTab === 'books'" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
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
        description="Ajoutez des livres depuis le catalogue."
      />
    </div>

    <!-- Followers / Following -->
    <div v-if="activeTab === 'followers' || activeTab === 'following'" class="space-y-3">
      <div
        v-for="user in (activeTab === 'followers' ? followers : following)"
        :key="user.id"
        class="bg-surface border border-line rounded-card p-4 flex items-center gap-4"
      >
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#e7c8a8] to-[#8e6a48] grid place-items-center flex-shrink-0">
          <span class="font-serif italic text-sm text-white">{{ user.name?.charAt(0).toUpperCase() }}</span>
        </div>
        <div class="flex-1 min-w-0">
          <NuxtLink :to="`/profile/${user.id}`" class="font-medium text-ink hover:text-accent">
            {{ user.name }}
          </NuxtLink>
          <p class="text-meta text-ink-3 truncate">{{ user.bio || 'Aucune bio' }}</p>
        </div>
        <AppButton size="sm" :variant="user.is_following ? 'outline' : 'primary'" @click="toggleFollow(user)">
          {{ user.is_following ? 'Suivi' : 'Suivre' }}
        </AppButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Pencil, Loader2 } from 'lucide-vue-next'

definePageMeta({ layout: 'default', middleware: 'auth' })

const { getMyProfile, updateProfile, getFollowers, getFollowing, follow, unfollow } = useProfile()

const profile = ref(null)
const editing = ref(false)
const saving = ref(false)
const activeTab = ref('books')
const recentBooks = ref([])
const followers = ref([])
const following = ref([])

const editForm = reactive({ name: '', bio: '' })

const initials = computed(() => {
  return profile.value?.name?.charAt(0).toUpperCase() || '?'
})

const tabs = [
  { label: 'Ma bibliothèque', value: 'books' },
  { label: 'Abonnés', value: 'followers' },
  { label: 'Abonnements', value: 'following' }
]

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', { year: 'numeric', month: 'long' })
}

const loadProfile = async () => {
  try {
    const data = await getMyProfile()
    profile.value = data.user || data
    recentBooks.value = data.recent_books || []
  } catch (e) { console.error(e) }
}

const startEdit = () => {
  editForm.name = profile.value.name
  editForm.bio = profile.value.bio || ''
  editing.value = true
}

const saveProfile = async () => {
  saving.value = true
  try {
    const updated = await updateProfile({ name: editForm.name, bio: editForm.bio })
    profile.value = { ...profile.value, ...updated }
    editing.value = false
  } catch (e) { console.error(e) }
  finally { saving.value = false }
}

const loadFollow = async () => {
  try {
    const fRes = await getFollowers()
    followers.value = fRes.data?.users || fRes.data || []
    const gRes = await getFollowing()
    following.value = gRes.data?.users || gRes.data || []
  } catch (e) { /* ignore */ }
}

const toggleFollow = async (user) => {
  try {
    if (user.is_following) {
      await unfollow(user.id)
      user.is_following = false
    } else {
      await follow(user.id)
      user.is_following = true
    }
  } catch (e) { /* ignore */ }
}

watch(activeTab, () => {
  if (activeTab.value === 'followers' || activeTab.value === 'following') {
    loadFollow()
  }
})

onMounted(loadProfile)
</script>