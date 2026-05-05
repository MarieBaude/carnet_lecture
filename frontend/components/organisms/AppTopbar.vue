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

  </header>
</template>

<script setup>
const authStore = useAuthStore()
const searchQuery = ref('')

const userInitial = computed(() => authStore.user?.name?.charAt(0).toUpperCase() || '?')
</script>