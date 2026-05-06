<template>
  <div :class="['book-cover rounded-sm overflow-hidden relative flex-shrink-0', sizeClass]">
    <img v-if="coverUrl && !imgError" :src="coverUrl" :alt="title || 'Couverture'" class="w-full h-full object-cover"
      @error="onImageError" />
    <img v-else src="http://localhost:9000/carnet-lecture/covers/No_Image_Available.jpg" alt="Pas de couverture"
      class="w-full h-full object-cover" />
    <span v-if="tome"
      class="absolute bottom-1 right-1 bg-ink/60 text-white text-[10px] font-medium px-1.5 py-0.5 rounded z-10">
      T.{{ tome }}
    </span>
  </div>
</template>

<script setup>
const props = defineProps({
  coverUrl: { type: String, default: null },
  title: { type: String, default: '' },
  size: { type: String, default: 'md' },
  tome: { type: Number, default: null }
})

const imgError = ref(false)

const onImageError = () => {
  imgError.value = true
}

const sizeClass = computed(() => {
  switch (props.size) {
    case 'sm': return 'w-12 h-[72px]'
    case 'lg': return 'w-40 h-[240px]'
    default: return 'w-24 h-[144px]'
  }
})
</script>