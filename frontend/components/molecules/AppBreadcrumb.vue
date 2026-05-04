<template>
  <nav class="flex items-center gap-2 text-meta text-ink-3">
    <template v-for="(item, i) in items">
      <ChevronRight v-if="i > 0" :key="'sep-' + i" class="w-3 h-3 text-line-2" />
      <NuxtLink 
        v-if="item.to && i < items.length - 1" 
        :key="'link-' + i"
        :to="item.to"
        class="text-ink-2 hover:text-accent transition-colors"
      >
        {{ item.label }}
      </NuxtLink>
      <span v-else :key="'current-' + i" class="font-serif italic text-ink text-sm">
        {{ item.label }}
      </span>
    </template>
  </nav>
</template>

<script setup>
import { ChevronRight } from 'lucide-vue-next'

defineProps({
  items: { type: Array, required: true }
})
</script>