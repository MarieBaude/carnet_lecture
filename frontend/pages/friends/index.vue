<template>
  <div>
    <h1 class="font-serif text-section text-ink mb-6">Amis</h1>

    <!-- Onglets -->
    <div class="border-b border-line mb-6">
      <div class="flex gap-0">
        <button @click="switchTab('following')" :class="tabClass('following')">
          Abonnements
        </button>
        <button @click="switchTab('followers')" :class="tabClass('followers')">
          Abonnés
        </button>
      </div>
    </div>

    <div v-if="loading" class="space-y-3">
      <div v-for="i in 5" :key="i" class="bg-surface rounded-card p-4 flex items-center gap-4 animate-pulse">
        <div class="w-10 h-10 bg-bg-2 rounded-full" />
        <div class="flex-1 space-y-2">
          <div class="h-4 bg-bg-2 rounded w-1/3" />
          <div class="h-3 bg-bg-2 rounded w-2/3" />
        </div>
      </div>
    </div>

    <div v-else-if="users.length" class="space-y-3">
      <div v-for="user in users" :key="user.id"
        class="bg-surface border border-line rounded-card p-4 flex items-center gap-4">
        <NuxtLink :to="`/profile/${user.id}`" class="flex-shrink-0">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#e7c8a8] to-[#8e6a48] grid place-items-center">
            <span class="font-serif italic text-sm text-white">{{ user.name?.charAt(0).toUpperCase() }}</span>
          </div>
        </NuxtLink>
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

    <AppEmptyState v-else :title="activeTab === 'following' ? 'Aucun abonnement' : 'Aucun abonné'" />
  </div>
</template>

<script setup>
definePageMeta({ layout: 'default', middleware: 'auth' })

const { getFollowers, getFollowing, follow, unfollow } = useProfile()

const activeTab = ref('following')
const users = ref([])
const loading = ref(true)

const loadData = async () => {
  loading.value = true
  try {
    const res = activeTab.value === 'following'
      ? await getFollowing()
      : await getFollowers()
    users.value = res.data?.users || res.data || []
  } catch (e) {
    users.value = []
  } finally {
    loading.value = false
  }
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

const switchTab = (tab) => {
  activeTab.value = tab
  loadData()
}

const tabClass = (tab) => {
  const base = 'tab-underline pb-3 px-1 mr-6 text-body font-medium transition-colors'
  return activeTab.value === tab ? base + ' active text-accent' : base + ' text-ink-3 hover:text-ink'
}

onMounted(loadData)
</script>