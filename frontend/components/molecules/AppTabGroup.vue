<template>
  <div class="border-b border-line relative">
    <div class="flex gap-0">
      <button 
        v-for="tab in tabs" 
        :key="tab.value"
        @click="$emit('update:modelValue', tab.value)"
        :class="[
          'tab-underline pb-3 px-1 mr-6 text-body font-medium transition-colors',
          modelValue === tab.value ? 'active text-accent' : 'text-ink-3 hover:text-ink'
        ]"
      >
        {{ tab.label }}
        <span 
          v-if="tab.count !== undefined"
          :class="[
            'text-[11px] rounded-full px-2 py-0.5 ml-1.5 font-medium',
            modelValue === tab.value ? 'bg-accent-soft text-accent' : 'bg-bg-2 text-ink-3'
          ]"
        >
          {{ tab.count }}
        </span>
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  tabs: { type: Array, required: true },
  modelValue: { type: String, required: true }
})

defineEmits(['update:modelValue'])
</script>