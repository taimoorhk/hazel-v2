import posthog from 'posthog-js'

let isInitialized = false

export function usePostHog() {
  if (!isInitialized) {
    console.log('Initializing PostHog...')
    posthog.init('phc_ILzwVyXnW0ZFtXdIrkzEH3KAZmOiA4IM31S270hUZQL', {
      api_host: 'https://us.i.posthog.com',
      defaults: '2025-05-24',
      person_profiles: 'identified_only', // or 'always' to create profiles for anonymous users as well
      capture_pageview: true,
      capture_pageleave: true,
    })
    console.log('PostHog initialized successfully')
    isInitialized = true
  }

  return { posthog }
} 