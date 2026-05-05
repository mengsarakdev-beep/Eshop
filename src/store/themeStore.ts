import { ref } from 'vue'

const THEME_KEY = 'eshop-theme'

// Global theme state
export const isDarkMode = ref(false)

// Initialize theme from localStorage or system preference
export function initializeTheme() {
  const savedTheme = localStorage.getItem(THEME_KEY)
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  const shouldBeDark = savedTheme === 'dark' || (!savedTheme && prefersDark)

  applyTheme(shouldBeDark ? 'dark' : 'light')
}

// Apply theme to DOM and update state
export function applyTheme(mode: 'dark' | 'light') {
  isDarkMode.value = mode === 'dark'
  localStorage.setItem(THEME_KEY, mode)
  document.documentElement.style.colorScheme = mode
  document.documentElement.classList.toggle('dark', isDarkMode.value)
  document.body.style.backgroundColor = isDarkMode.value ? '#020617' : ''
  document.body.style.color = isDarkMode.value ? '#e2e8f0' : ''
}

// Toggle between dark and light
export function toggleTheme() {
  applyTheme(isDarkMode.value ? 'light' : 'dark')
}

// Watch for changes and sync across tabs
if (typeof window !== 'undefined') {
  window.addEventListener('storage', (event: StorageEvent) => {
    if (event.key === THEME_KEY && event.newValue) {
      applyTheme(event.newValue as 'dark' | 'light')
    }
  })
}

export function useTheme() {
  return {
    isDarkMode,
    applyTheme,
    toggleTheme,
    initializeTheme,
  }
}
