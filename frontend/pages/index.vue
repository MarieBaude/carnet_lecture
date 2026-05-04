<template>
  <div class="min-h-screen bg-bg">
    <div v-if="authStore.isAuthenticated" class="p-8">
      <h1 class="font-serif text-3xl text-ink mb-4">Bienvenue, {{ authStore.user?.name }} !</h1>
      <p class="text-ink-2 mb-6">Vous êtes connecté.</p>
      <button 
        @click="authStore.logout()"
        class="bg-accent text-white px-6 py-2 rounded-btn hover:bg-[#7a2f2f] transition-colors"
      >
        Déconnexion
      </button>
    </div>
    <div v-else class="flex items-center justify-center min-h-screen">
      <div class="text-center">
        <h1 class="font-serif text-4xl text-ink mb-4">Carnet de Lecture</h1>
        <p class="text-ink-2 mb-8">Votre bibliothèque personnelle</p>
        <NuxtLink 
          to="/login"
          class="bg-accent text-white font-medium px-8 py-3 rounded-btn shadow-btn hover:shadow-btn-hover hover:-translate-y-0.5 transition-all inline-block"
        >
          Se connecter
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
const authStore = useAuthStore()
</script>