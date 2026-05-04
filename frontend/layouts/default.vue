<template>
  <div class="min-h-screen bg-bg flex">
    <AppSidebar />
    
    <div class="flex-1 flex flex-col min-h-screen">
      <AppTopbar />
      
      <main class="flex-1 p-8">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
const authStore = useAuthStore()
authStore.init()
</script>