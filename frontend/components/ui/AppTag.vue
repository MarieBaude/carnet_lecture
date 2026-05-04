<template>
  <span :class="['tag', `tag-${category}`]">
    <slot>{{ label }}</slot>
  </span>
</template>

<script setup>
defineProps({
  label: { type: String, default: '' },
  category: { type: String, default: 'genre' }
})
</script>