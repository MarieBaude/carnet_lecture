<template>
  <span 
    v-if="count" 
    class="bg-accent text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-sm"
  >
    {{ count > 9 ? '9+' : count }}
  </span>
</template>

<script setup>
defineProps({
  count: { type: Number, default: 0 }
})
</script>