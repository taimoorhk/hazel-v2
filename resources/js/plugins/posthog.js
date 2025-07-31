import { usePostHog } from '@/composables/usePostHog'

export function setupPostHog() {
  const { posthog } = usePostHog()
  
  // Track Inertia.js navigation events
  if (typeof window !== 'undefined') {
    // Track page views on Inertia navigation
    document.addEventListener('inertia:success', (event) => {
      const url = event.detail.page.url
      const title = event.detail.page.component
      
      posthog.capture('$pageview', {
        $current_url: url,
        $pathname: new URL(url).pathname,
        $title: title,
      })
    })
    
    // Track user identification when available
    if (window.user) {
      posthog.identify(window.user.id, {
        email: window.user.email,
        name: window.user.name,
      })
    }
  }
  
  return { posthog }
} 