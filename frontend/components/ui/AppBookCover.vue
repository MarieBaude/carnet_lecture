<template>
  <div :class="['book-cover rounded-sm relative flex-shrink-0', `cv-${variant}`, sizeClass]">
    <span v-if="tome" class="absolute bottom-2 right-2 text-white/70 text-meta font-medium z-10">
      T.{{ tome }}
    </span>
  </div>
</template>

<script setup>
const props = defineProps({
  variant: { type: Number, default: 1 },
  size: { type: String, default: 'md' },
  tome: { type: Number, default: null }
})

const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'w-12 h-18'
    case 'lg': return 'w-40 h-60'
    default: return 'w-24 h-36'
  }
})
</script>