# PostHog Analytics Integration

This project has been integrated with PostHog for analytics tracking. PostHog provides comprehensive analytics including user behavior tracking, feature flags, A/B testing, and more.

## Setup

The PostHog integration has been set up with the following configuration:

- **Project API Key**: `phc_ILzwVyXnW0ZFtXdIrkzEH3KAZmOiA4IM31S270hUZQL`
- **API Host**: `https://us.i.posthog.com`
- **Person Profiles**: `identified_only` (only creates profiles for authenticated users)

## Files Modified

### 1. `resources/js/composables/usePostHog.js`
- Created PostHog composable for Vue 3
- Handles initialization and provides PostHog instance

### 2. `resources/js/plugins/posthog.js`
- Created PostHog plugin for Inertia.js integration
- Tracks Inertia.js navigation events
- Handles user identification

### 3. `resources/js/app.ts`
- Integrated PostHog initialization in the main app setup
- Ensures PostHog is available throughout the application

### 4. `resources/js/pages/Dashboard.vue`
- Added PostHog tracking for dashboard page views
- Added tracking for "Add Details" button clicks

### 5. `resources/js/pages/Welcome.vue`
- Added PostHog tracking for welcome page views
- Added tracking for "Register" button clicks

### 6. `resources/js/pages/auth/Login.vue`
- Added PostHog tracking for login page views
- Added tracking for "Sign up here" link clicks

## Events Tracked

The following events are automatically tracked:

### Page Views
- `welcome_page_viewed` - When users visit the welcome page
- `login_page_viewed` - When users visit the login page
- `dashboard_viewed` - When users visit the dashboard

### User Actions
- `register_button_clicked` - When users click the register button on welcome page
- `sign_up_link_clicked` - When users click the sign up link on login page
- `onboarding_form_clicked` - When users click the "Add Details" button on dashboard

### Navigation Events
- `$pageview` - Automatic page view tracking for Inertia.js navigation

## Usage

To use PostHog in any component:

```javascript
import { usePostHog } from '@/composables/usePostHog'

const { posthog } = usePostHog()

// Track custom events
posthog.capture('custom_event', {
  property: 'value',
  user_email: user.value?.email,
  user_id: user.value?.id,
})

// Check feature flags
const isFeatureEnabled = posthog.isFeatureEnabled('feature_flag_name')

// Identify users
posthog.identify(userId, {
  email: userEmail,
  name: userName,
})
```

## Testing

To verify PostHog is working:

1. Open the browser console
2. Navigate to different pages
3. Look for "PostHog initialized successfully" message
4. Check the PostHog dashboard for incoming events

## Configuration

The PostHog configuration can be modified in `resources/js/composables/usePostHog.js`:

- Change `person_profiles` to `'always'` to track anonymous users
- Modify `capture_pageview` and `capture_pageleave` settings
- Add additional configuration options as needed

## Privacy

- Only authenticated users are tracked with person profiles
- Anonymous users are tracked for page views and events
- User identification is handled automatically when users log in

## Next Steps

Consider implementing:

1. **Feature Flags**: Use PostHog feature flags for A/B testing
2. **Surveys**: Add user feedback surveys
3. **Session Recording**: Enable session replay for debugging
4. **Cohorts**: Create user segments for targeted analysis
5. **Experiments**: Run A/B tests on different features

For more information, visit the [PostHog documentation](https://posthog.com/docs). 