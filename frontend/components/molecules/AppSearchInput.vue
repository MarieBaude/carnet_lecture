<template>
  <div class="flex items-center gap-3 bg-surface border border-line rounded-full px-4 py-2.5 shadow-sm
              focus-within:border-accent-2 focus-within:ring-2 focus-within:ring-accent-soft transition-all">
    <Search class="w-4 h-4 text-ink-3 flex-shrink-0" />
    <input 
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      @keyup.enter="$emit('search')"
      type="text"
      :placeholder="placeholder"
      class="flex-1 bg-transparent border-none outline-none text-body text-ink placeholder:text-ink-3/50 placeholder:italic placeholder:font-serif"
    />
    <kbd class="hidden sm:inline-flex items-center gap-1 px-1.5 py-0.5 text-[10px] text-ink-3 bg-bg-2 border border-line rounded font-mono">
      ⌘K
    </kbd>
  </div>
</template>

<script setup>
import { Search } from 'lucide-vue-next'

defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Rechercher…' }
})

defineEmits(['update:modelValue', 'search'])
</script>