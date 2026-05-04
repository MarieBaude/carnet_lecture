<template>
  <div class="flex flex-col items-center justify-center text-center py-16 px-6 border border-dashed border-line-2 rounded-card bg-gradient-to-b from-surface-2 to-transparent">
    <BookOpen v-if="!icon" class="w-16 h-16 text-accent/40 mb-4" />
    <component v-else :is="icon" class="w-16 h-16 text-accent/40 mb-4" />
    <h3 class="font-serif text-card-title text-ink mb-2">{{ title }}</h3>
    <p v-if="description" class="text-body text-ink-3 max-w-sm">{{ description }}</p>
    <AppButton v-if="actionLabel" :variant="actionVariant" class="mt-4">
      {{ actionLabel }}
    </AppButton>
  </div>
</template>

<script setup>
import { BookOpen } from 'lucide-vue-next'

defineProps({
  title: { type: String, required: true },
  description: { type: String, default: '' },
  icon: { type: String, default: '' },
  actionLabel: { type: String, default: '' },
  actionVariant: { type: String, default: 'primary' }
})
</script>