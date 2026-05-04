<template>
  <div class="flex justify-center items-center gap-1">
    <button 
      :disabled="currentPage === 1"
      @click="$emit('pageChanged', currentPage - 1)"
      class="pg nav"
    >
      <ChevronLeft class="w-3.5 h-3.5" />
      Précédent
    </button>
    
    <template v-for="page in visiblePages">
      <span v-if="page === '...'" :key="'dots-' + page" class="pg dots">…</span>
      <button 
        v-else
        :key="page"
        @click="$emit('pageChanged', page)"
        :class="['pg', { active: page === currentPage }]"
      >
        {{ page }}
      </button>
    </template>
    
    <button 
      :disabled="currentPage === lastPage"
      @click="$emit('pageChanged', currentPage + 1)"
      class="pg nav"
    >
      Suivant
      <ChevronRight class="w-3.5 h-3.5" />
    </button>
  </div>
</template>

<script setup>
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

const props = defineProps({
  currentPage: { type: Number, required: true },
  lastPage: { type: Number, required: true }
})

defineEmits(['pageChanged'])

const visiblePages = computed(() => {
  const pages = []
  const total = props.lastPage
  const current = props.currentPage
  
  if (total <= 7) {
    for (let i = 1; i <= total; i++) pages.push(i)
  } else {
    pages.push(1)
    if (current > 3) pages.push('...')
    for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) {
      pages.push(i)
    }
    if (current < total - 2) pages.push('...')
    pages.push(total)
  }
  return pages
})
</script>

<style scoped>
.pg {
  min-width: 36px; height: 36px;
  padding: 0 10px;
  display: inline-flex; align-items: center; justify-content: center;
  border: 1px solid transparent;
  background: transparent;
  border-radius: 8px;
  font-size: 13px;
  color: var(--ink-2);
  cursor: pointer;
  font-variant-numeric: tabular-nums;
  transition: background .15s ease, border-color .15s ease, color .15s ease;
}
.pg:hover { background: var(--bg-2); }
.pg.active {
  background: var(--surface);
  border-color: var(--accent);
  color: var(--accent);
  font-weight: 600;
  box-shadow: var(--shadow-sm);
}
</style>