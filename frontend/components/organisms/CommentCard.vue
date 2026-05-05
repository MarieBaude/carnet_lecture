<template>
  <div class="bg-surface border border-line rounded-card p-5">
    <div class="flex items-center gap-3 mb-3">
      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#e7c8a8] to-[#8e6a48] grid place-items-center">
        <span class="font-serif italic text-sm text-white">{{ comment.user?.name?.charAt(0).toUpperCase() }}</span>
      </div>
      <div>
        <p class="font-medium text-ink text-sm">{{ comment.user?.name }}</p>
        <p class="text-meta text-ink-3">{{ relativeTime(comment.created_at) }}</p>
      </div>
    </div>
    <p class="text-body text-ink-2">{{ comment.body }}</p>
    <button class="flex items-center gap-1 mt-3 text-meta text-ink-3 hover:text-accent transition-colors">
      <ThumbsUp class="w-3.5 h-3.5" />
      Utile
    </button>
  </div>
</template>

<script setup>
import { ThumbsUp } from 'lucide-vue-next'
const { relativeTime } = useProfile()

defineProps({
  comment: { type: Object, required: true }
})
</script>