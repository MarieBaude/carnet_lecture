export default {
  theme: {
    extend: {
      colors: {
        bg: '#faf7f2',
        'bg-2': '#f4efe6',
        surface: '#ffffff',
        'surface-2': '#fbf8f3',
        ink: '#2a2520',
        'ink-2': '#5a544c',
        'ink-3': '#8a8278',
        line: '#e9e3d8',
        'line-2': '#d9d2c3',
        accent: '#8b3a3a',
        'accent-2': '#b15c4a',
        'accent-soft': '#f3e6e1',
        'accent-softer': '#f8eee9',
        star: '#8b3a3a'
      },
      fontFamily: {
        serif: ['Cormorant Garamond', 'serif'],
        sans: ['Inter', 'sans-serif']
      },
      fontSize: {
        'book-title': ['64px', { lineHeight: '1.1', fontWeight: '600' }],
        'section': ['40px', { lineHeight: '1.2', fontWeight: '500' }],
        'card-title': ['24px', { lineHeight: '1.3', fontWeight: '600' }],
        'synopsis': ['18.5px', { lineHeight: '1.6' }],
        'body': ['14.5px', { lineHeight: '1.6' }],
        'meta': ['12px', { lineHeight: '1.5' }],
        'label': ['10.5px', { lineHeight: '1.4', letterSpacing: '0.05em' }]
      },
      boxShadow: {
        'sm': '0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06)',
        'md': '0 4px 12px rgba(0,0,0,0.04), 0 2px 4px rgba(0,0,0,0.04)',
        'lg': '0 12px 32px rgba(0,0,0,0.06), 0 4px 8px rgba(0,0,0,0.04)',
        'btn': '0 8px 18px -10px rgba(139,58,58,0.6)',
        'btn-hover': '0 12px 24px -8px rgba(139,58,58,0.7)'
      },
      borderRadius: {
        'card': '14px',
        'btn': '10px'
      },
      animation: {
        'fade-in-up': 'fadeInUp 0.3s ease-out',
        'slide-up': 'slideUp 0.2s ease-out'
      },
      keyframes: {
        fadeInUp: {
          '0%': { opacity: '0', transform: 'translateY(4px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' }
        },
        slideUp: {
          '0%': { transform: 'translateY(100%)' },
          '100%': { transform: 'translateY(0)' }
        }
      }
    }
  }
}