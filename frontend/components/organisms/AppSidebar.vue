<template>
  <aside class="w-[264px] flex-shrink-0 sticky top-0 h-screen border-r border-line sidebar-gradient flex flex-col gap-5 p-6">
    <!-- Logo -->
    <div class="pb-4 border-b border-line">
      <NuxtLink to="/" class="font-serif italic font-semibold text-2xl text-accent">
        Carnet de lecture
      </NuxtLink>
    </div>

    <!-- Profil -->
    <div class="flex flex-col items-center text-center gap-2">
      <div class="w-16 h-16 rounded-full bg-gradient-to-br from-[#e7c8a8] via-[#c9a279] to-[#8e6a48] border-3 border-surface shadow-md grid place-items-center">
        <span class="font-serif italic text-xl text-white drop-shadow-sm">{{ userInitial }}</span>
      </div>
      <p class="font-serif text-lg font-semibold text-ink leading-tight">{{ authStore.user?.name }}</p>
      <p class="text-[11px] text-ink-3">Membre depuis mars 2022</p>
    </div>

    <!-- Stats rapides -->
    <div class="grid grid-cols-3 gap-1 py-3 border-y border-line">
      <div class="text-center">
        <div class="font-serif font-semibold text-accent">{{ stats.total || 0 }}</div>
        <div class="text-[10px] text-ink-3">livres</div>
      </div>
      <div class="text-center">
        <div class="font-serif font-semibold text-accent">{{ stats.reading || 0 }}</div>
        <div class="text-[10px] text-ink-3">en cours</div>
      </div>
      <div class="text-center">
        <div class="font-serif font-semibold text-accent">{{ stats.read || 0 }}</div>
        <div class="text-[10px] text-ink-3">lus</div>
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-col gap-0.5 mt-1">
      <span class="text-label text-ink-3 uppercase tracking-wider px-2 pb-2">Navigation</span>
      
      <NuxtLink 
        v-for="item in navItems" 
        :key="item.to"
        :to="item.to"
        :class="[
          'flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-sm font-medium transition-colors',
          route.path === item.to 
            ? 'bg-accent text-white shadow-md' 
            : 'text-ink-2 hover:bg-accent-soft/10 hover:text-ink'
        ]"
      >
        <component :is="item.icon" class="w-4 h-4" />
        {{ item.label }}
        <span 
          v-if="item.count !== undefined" 
          :class="[
            'ml-auto text-[11px]',
            route.path === item.to ? 'text-white/80' : 'text-ink-3'
          ]"
        >{{ item.count }}</span>
      </NuxtLink>
    </nav>

    <!-- Pied -->
    <div class="mt-auto flex items-center gap-2 text-[11px] text-ink-3">
      <span class="w-1.5 h-1.5 rounded-full bg-[#6a8f6a]" />
      En ligne
    </div>
  </aside>
</template>

<script setup>
import { BookOpen, Library, Bookmark, Heart, User, Users, Activity } from 'lucide-vue-next'

const authStore = useAuthStore()
const route = useRoute()
const { getStats } = useLibrary()

const stats = ref({})

const userInitial = computed(() => authStore.user?.name?.charAt(0).toUpperCase() || '?')

const navItems = computed(() => [
  { label: 'Accueil', to: '/', icon: BookOpen },
  { label: 'Catalogue', to: '/books', icon: Library },
  { label: 'Bibliothèque', to: '/library', icon: Bookmark, count: stats.value.total || undefined },
  { label: 'Profil', to: '/profile', icon: User },
  { label: 'Amis', to: '/friends', icon: Users },
  { label: 'Activité', to: '/activity', icon: Activity }
])

const loadStats = async () => {
  try {
    stats.value = await getStats()
  } catch (e) { /* ignore */ }
}

onMounted(loadStats)
</script>