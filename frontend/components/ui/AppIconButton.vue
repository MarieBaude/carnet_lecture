<template>
  <button 
    class="w-10 h-10 flex items-center justify-center rounded-full bg-surface border border-line shadow-sm text-ink-2
           hover:text-accent hover:border-accent-soft hover:-translate-y-0.5 transition-all relative"
  >
    <component :is="icon" class="w-4 h-4" />
    <AppBadge v-if="badge" :count="badge" class="absolute -top-1 -right-1" />
  </button>
</template>

<script setup>
import * as LucideIcons from 'lucide-vue-next'

const props = defineProps({
  icon: { type: String, required: true },
  badge: { type: Number, default: 0 }
})

const icon = computed(() => LucideIcons[props.icon])
</script>