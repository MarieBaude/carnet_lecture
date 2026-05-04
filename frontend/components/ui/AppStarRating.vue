<template>
  <div class="flex gap-0.5">
    <Star 
      v-for="star in 5" 
      :key="star"
      :class="[
        star <= rating ? 'fill-star text-star' : 'text-[#e1d6c5]',
        size === 'md' ? 'w-5 h-5' : 'w-3.5 h-3.5'
      ]"
    />
  </div>
</template>

<script setup>
import { Star } from 'lucide-vue-next'

defineProps({
  rating: { type: Number, default: 0 },
  size: { type: String, default: 'sm' }
})
</script>