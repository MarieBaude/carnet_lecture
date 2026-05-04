<template>
  <header class="sticky top-0 z-10 topbar-blur px-8 py-4 flex items-center gap-4">
    <!-- Breadcrumb / Titre -->
    <div class="flex-1">
      <slot name="breadcrumb" />
    </div>

    <!-- Search -->
    <div class="flex-1 max-w-md">
      <AppSearchInput v-model="searchQuery" placeholder="Rechercher un livre, un auteur…" />
    </div>

    <div class="flex-1" />

    <!-- Actions -->
    <AppIconButton icon="Bell" :badge="3" />
    
    <!-- Profil -->
    <div class="flex items-center gap-2 pl-3 pr-4 py-1.5 bg-surface border border-line rounded-full shadow-sm">
      <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#e7c8a8] via-[#c9a279] to-[#8e6a48] grid place-items-center">
        <span class="font-serif italic text-xs text-white">{{ userInitial }}</span>
      </div>
      <span class="text-sm font-medium text-ink">{{ authStore.user?.name?.split(' ')[0] }}</span>
    </div>
  </header>
</template>

<script setup>
const authStore = useAuthStore()
const searchQuery = ref('')

const userInitial = computed(() => authStore.user?.name?.charAt(0).toUpperCase() || '?')
</script>