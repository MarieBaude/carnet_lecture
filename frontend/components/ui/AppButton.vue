<template>
  <button 
    :class="[
      'inline-flex items-center justify-center gap-2 font-medium rounded-btn transition-all duration-200',
      variant === 'primary' ? 'bg-accent text-white shadow-btn hover:bg-[#7a2f2f] hover:shadow-btn-hover hover:-translate-y-0.5' : '',
      variant === 'outline' ? 'bg-transparent text-accent border border-accent hover:bg-accent-softer' : '',
      variant === 'ghost' ? 'bg-transparent text-ink-2 hover:bg-bg-2' : '',
      size === 'sm' ? 'px-3 py-1.5 text-meta' : 'px-5 py-2.5 text-body'
    ]"
  >
    <component v-if="icon" :is="icon" :class="size === 'sm' ? 'w-3.5 h-3.5' : 'w-4 h-4'" />
    <slot />
  </button>
</template>

<script setup>
import * as LucideIcons from 'lucide-vue-next'

const props = defineProps({
  variant: { type: String, default: 'primary' },
  size: { type: String, default: 'md' },
  icon: { type: String, default: '' }
})

const icon = computed(() => props.icon ? LucideIcons[props.icon] : null)
</script>