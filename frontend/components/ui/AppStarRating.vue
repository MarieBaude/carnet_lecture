<template>
  <div class="flex gap-0.5">
    <button
      v-for="star in 5"
      :key="star"
      type="button"
      :disabled="!interactive"
      @click="$emit('rate', star)"
      @mouseenter="hoveredStar = star"
      @mouseleave="hoveredStar = 0"
      class="transition-colors"
      :class="interactive ? 'cursor-pointer' : 'cursor-default'"
    >
      <Star
        :class="[
          (hoveredStar || rating) >= star ? 'fill-star text-star' : 'text-[#e1d6c5]',
          size === 'md' ? 'w-5 h-5' : 'w-3.5 h-3.5'
        ]"
      />
    </button>
  </div>
</template>

<script setup>
import { Star } from 'lucide-vue-next'

const props = defineProps({
  rating: { type: Number, default: 0 },
  size: { type: String, default: 'sm' },
  interactive: { type: Boolean, default: false }
})

defineEmits(['rate'])

const hoveredStar = ref(0)
</script>