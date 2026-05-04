export const useBookCover = () => {
  const variants = Array.from({length: 10}, (_, i) => `cv-${i + 1}`)
  
  const getCoverClass = (variant?: number): string => {
    if (variant && variant >= 1 && variant <= 10) {
      return `cv-${variant}`
    }
    return variants[Math.floor(Math.random() * variants.length)]
  }
  
  return { getCoverClass }
}