<template>
  <AppLayout>
    <!-- Loading state -->
    <div v-if="loading" class="min-h-screen bg-gray-50 flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-4 text-gray-600">Loading...</p>
      </div>
    </div>

    <!-- Main content -->
    <div v-else class="min-h-screen bg-gray-50">
      <!-- Header -->
      <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="py-6">
            <h1 class="text-3xl font-bold text-gray-900">Custom Settings</h1>
            <p class="mt-2 text-sm text-gray-600">
              Configure your audio processing parameters for optimal speech recognition
            </p>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <!-- Settings Form -->
          <form @submit.prevent="saveSettings" class="p-6 space-y-8">
            <!-- Endpointing Delays Section -->
            <div class="space-y-6">
              <div class="border-b border-gray-200 pb-4">
                <h2 class="text-xl font-semibold text-gray-900">Endpointing Delays</h2>
                <p class="mt-1 text-sm text-gray-600">
                  Control when the system stops listening for speech
                </p>
              </div>

              <!-- Min Endpointing Delay -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700">
                    Minimum Endpointing Delay
                  </label>
                  <span class="text-sm text-gray-500">{{ form.min_endpointing_delay }}s</span>
                </div>
                <input
                  type="range"
                  v-model="form.min_endpointing_delay"
                  min="0.0"
                  max="2.0"
                  step="0.1"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                />
                <div class="flex justify-between text-xs text-gray-500">
                  <span>0.0s</span>
                  <span>1.0s</span>
                  <span>2.0s</span>
                </div>
                <p class="text-xs text-gray-500">
                  Minimum time to wait before stopping audio capture
                </p>
              </div>

              <!-- Max Endpointing Delay -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700">
                    Maximum Endpointing Delay
                  </label>
                  <span class="text-sm text-gray-500">{{ form.max_endpointing_delay }}s</span>
                </div>
                <input
                  type="range"
                  v-model="form.max_endpointing_delay"
                  min="3.0"
                  max="9.0"
                  step="0.1"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                />
                <div class="flex justify-between text-xs text-gray-500">
                  <span>3.0s</span>
                  <span>6.0s</span>
                  <span>9.0s</span>
                </div>
                <p class="text-xs text-gray-500">
                  Maximum time to wait before stopping audio capture
                </p>
                <!-- Validation warning -->
                <div v-if="form.min_endpointing_delay >= form.max_endpointing_delay" class="text-xs text-red-600 bg-red-50 p-2 rounded-md">
                  ⚠️ Maximum delay must be greater than minimum delay
                </div>
              </div>
            </div>

            <!-- Speech Duration Section -->
            <div class="space-y-6">
              <div class="border-b border-gray-200 pb-4">
                <h2 class="text-xl font-semibold text-gray-900">Speech Duration Control</h2>
                <p class="mt-1 text-sm text-gray-600">
                  Configure speech recognition sensitivity and timing
                </p>
              </div>

              <!-- Min Speech Duration -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700">
                    Minimum Speech Duration
                  </label>
                  <span class="text-sm text-gray-500">{{ form.min_speech_duration }}s</span>
                </div>
                <input
                  type="range"
                  v-model="form.min_speech_duration"
                  min="0.02"
                  max="0.35"
                  step="0.01"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                />
                <div class="flex justify-between text-xs text-gray-500">
                  <span>0.02s</span>
                  <span>0.18s</span>
                  <span>0.35s</span>
                </div>
                <p class="text-xs text-gray-500">
                  Minimum duration for speech to be considered valid
                </p>
              </div>

              <!-- Min Silence Duration -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700">
                    Minimum Silence Duration
                  </label>
                  <span class="text-sm text-gray-500">{{ form.min_silence_duration }}s</span>
                </div>
                <input
                  type="range"
                  v-model="form.min_silence_duration"
                  min="0.25"
                  max="1.4"
                  step="0.01"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                />
                <div class="flex justify-between text-xs text-gray-500">
                  <span>0.25s</span>
                  <span>0.82s</span>
                  <span>1.4s</span>
                </div>
                <p class="text-xs text-gray-500">
                  Minimum silence duration to trigger endpointing
                </p>
              </div>
            </div>

            <!-- Padding & Buffering Section -->
            <div class="space-y-6">
              <div class="border-b border-gray-200 pb-4">
                <h2 class="text-xl font-semibold text-gray-900">Padding & Buffering</h2>
                <p class="mt-1 text-sm text-gray-600">
                  Control buffer timing and speech processing
                </p>
              </div>

              <!-- Prefix Padding Duration -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700">
                    Prefix Padding Duration
                  </label>
                  <span class="text-sm text-gray-500">{{ form.prefix_padding_duration }}s</span>
                </div>
                <input
                  type="range"
                  v-model="form.prefix_padding_duration"
                  min="0.2"
                  max="1.2"
                  step="0.01"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                />
                <div class="flex justify-between text-xs text-gray-500">
                  <span>0.2s</span>
                  <span>0.7s</span>
                  <span>1.2s</span>
                </div>
                <p class="text-xs text-gray-500">
                  Buffer time added before processing speech
                </p>
              </div>

              <!-- Max Buffered Speech -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700">
                    Maximum Buffered Speech
                  </label>
                  <span class="text-sm text-gray-500">{{ form.max_buffered_speech }}s</span>
                </div>
                <input
                  type="range"
                  v-model="form.max_buffered_speech"
                  min="40"
                  max="80"
                  step="1"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                />
                <div class="flex justify-between text-xs text-gray-500">
                  <span>40s</span>
                  <span>60s</span>
                  <span>80s</span>
                </div>
                <p class="text-xs text-gray-500">
                  Maximum amount of speech data to buffer before processing
                </p>
              </div>
            </div>

            <!-- Activation Section -->
            <div class="space-y-6">
              <div class="border-b border-gray-200 pb-4">
                <h2 class="text-xl font-semibold text-gray-900">Activation Control</h2>
                <p class="mt-1 text-sm text-gray-600">
                  Configure speech processing sensitivity
                </p>
              </div>

              <!-- Activation Threshold -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700">
                    Activation Threshold
                  </label>
                  <span class="text-sm text-gray-500">{{ form.activation_threshold }}</span>
                </div>
                <input
                  type="range"
                  v-model="form.activation_threshold"
                  min="0.1"
                  max="0.9"
                  step="0.01"
                  class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                />
                <div class="flex justify-between text-xs text-gray-500">
                  <span>0.1</span>
                  <span>0.5</span>
                  <span>0.9</span>
                </div>
                <p class="text-xs text-gray-500">
                  Sensitivity threshold for triggering speech processing
                </p>
              </div>
            </div>

            <!-- Voice Preferences Section -->
            <div class="space-y-6">
              <div class="border-b border-gray-200 pb-4">
                <h2 class="text-xl font-semibold text-gray-900">Voice Preferences</h2>
                <p class="mt-1 text-sm text-gray-600">
                  Choose your preferred voice for text-to-speech interactions
                </p>
              </div>

              <!-- Preferred Voice -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-sm font-medium text-gray-700">
                    Preferred Voice
                  </label>
                  <span class="text-sm text-gray-500 capitalize">{{ form.preferred_voice }}</span>
                </div>
                <select
                  v-model="form.preferred_voice"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                >
                  <option value="sage">Sage</option>
                  <option value="alloy">Alloy</option>
                  <option value="echo">Echo</option>
                  <option value="fable">Fable</option>
                  <option value="onyx">Onyx</option>
                  <option value="nova">Nova</option>
                  <option value="shimmer">Shimmer</option>
                </select>
                <p class="text-xs text-gray-500">
                  Select the voice that will be used for text-to-speech responses
                </p>
              </div>
            </div>

            <!-- Save Button -->
            <div class="pt-6 border-t border-gray-200">
              <div class="flex items-center justify-between">
                <button
                  type="submit"
                  :disabled="isSaving"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg
                    v-if="isSaving"
                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      class="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      stroke-width="4"
                    ></circle>
                    <path
                      class="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                  </svg>
                  {{ isSaving ? 'Saving...' : 'Save Settings' }}
                </button>

                <div v-if="lastSaved" class="text-sm text-green-600 flex items-center">
                  <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Last saved: {{ lastSaved }}
                </div>
                
                <!-- Real-time sync status -->
                <div class="text-xs text-blue-600 bg-blue-50 p-2 rounded-md flex items-center">
                  <svg class="w-3 h-3 mr-2 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Real-time sync enabled
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Current Values Display -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
          <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Current Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div class="bg-gray-50 p-3 rounded-md">
                <div class="text-sm font-medium text-gray-700">Min Endpointing Delay</div>
                <div class="text-lg font-semibold text-blue-600">{{ form.min_endpointing_delay }}s</div>
              </div>
              <div class="bg-gray-50 p-3 rounded-md">
                <div class="text-sm font-medium text-gray-700">Max Endpointing Delay</div>
                <div class="text-lg font-semibold text-blue-600">{{ form.max_endpointing_delay }}s</div>
              </div>
              <div class="bg-gray-50 p-3 rounded-md">
                <div class="text-sm font-medium text-gray-700">Min Speech Duration</div>
                <div class="text-lg font-semibold text-blue-600">{{ form.min_speech_duration }}s</div>
              </div>
              <div class="bg-gray-50 p-3 rounded-md">
                <div class="text-sm font-medium text-gray-700">Min Silence Duration</div>
                <div class="text-lg font-semibold text-blue-600">{{ form.min_silence_duration }}s</div>
              </div>
              <div class="bg-gray-50 p-3 rounded-md">
                <div class="text-sm font-medium text-gray-700">Prefix Padding Duration</div>
                <div class="text-lg font-semibold text-blue-600">{{ form.prefix_padding_duration }}s</div>
              </div>
              <div class="bg-gray-50 p-3 rounded-md">
                <div class="text-sm font-medium text-gray-700">Max Buffered Speech</div>
                <div class="text-lg font-semibold text-blue-600">{{ form.max_buffered_speech }}s</div>
              </div>
              <div class="bg-gray-50 p-3 rounded-md">
                <div class="text-sm font-medium text-gray-700">Activation Threshold</div>
                <div class="text-lg font-semibold text-blue-600">{{ form.activation_threshold }}</div>
              </div>
              <div class="bg-gray-50 p-3 rounded-md">
                <div class="text-sm font-medium text-gray-700">Preferred Voice</div>
                <div class="text-lg font-semibold text-blue-600 capitalize">{{ form.preferred_voice }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useAuthGuard } from '@/composables/useAuthGuard'
import { useSupabaseUser } from '@/composables/useSupabaseUser'
import { supabase } from '@/lib/supabaseClient'

const { loading } = useAuthGuard()
const { user, session } = useSupabaseUser()

// Form state
const form = reactive({
  min_endpointing_delay: 0.5,
  max_endpointing_delay: 6.0,
  min_speech_duration: 0.05,
  min_silence_duration: 0.55,
  prefix_padding_duration: 0.5,
  max_buffered_speech: 60,
  activation_threshold: 0.5,
  preferred_voice: 'sage'
})

// UI state
const isSaving = ref(false)
const lastSaved = ref(null)

// Load current settings from user data
const loadCurrentSettings = async () => {
  if (user.value && session.value) {
    try {
      // First try to get settings from public.users table
      const { data: userData, error: userError } = await supabase
        .from('users')
        .select('*')
        .eq('supabase_id', user.value.id)
        .single()

      if (!userError && userData) {
        form.min_endpointing_delay = userData.min_endpointing_delay || 0.5
        form.max_endpointing_delay = userData.max_endpointing_delay || 6.0
        form.min_speech_duration = userData.min_speech_duration || 0.05
        form.min_silence_duration = userData.min_silence_duration || 0.55
        form.prefix_padding_duration = userData.prefix_padding_duration || 0.5
        form.max_buffered_speech = userData.max_buffered_speech || 60
        form.activation_threshold = userData.activation_threshold || 0.5
        form.preferred_voice = userData.preferred_voice || 'sage'
      } else {
        // Fallback to user metadata from Supabase auth
        const { data: { user: supabaseUser }, error } = await supabase.auth.getUser()
        
        if (supabaseUser && supabaseUser.user_metadata) {
          const metadata = supabaseUser.user_metadata
          
          form.min_endpointing_delay = metadata.min_endpointing_delay || 0.5
          form.max_endpointing_delay = metadata.max_endpointing_delay || 6.0
          form.min_speech_duration = metadata.min_speech_duration || 0.05
          form.min_silence_duration = metadata.min_silence_duration || 0.55
          form.prefix_padding_duration = metadata.prefix_padding_duration || 0.5
          form.max_buffered_speech = metadata.max_buffered_speech || 60
          form.activation_threshold = metadata.activation_threshold || 0.5
          form.preferred_voice = metadata.preferred_voice || 'sage'
        }
      }
    } catch (error) {
      console.error('Error loading user settings:', error)
    }
  }
}

// Save settings to Supabase
const saveSettings = async () => {
  if (!user.value || !session.value) return

  isSaving.value = true
  
  try {
    // Update user metadata in Supabase auth.users
    const { error: authError } = await supabase.auth.updateUser({
      data: {
        min_endpointing_delay: form.min_endpointing_delay,
        max_endpointing_delay: form.max_endpointing_delay,
        min_speech_duration: form.min_speech_duration,
        min_silence_duration: form.min_silence_duration,
        prefix_padding_duration: form.prefix_padding_duration,
        max_buffered_speech: form.max_buffered_speech,
        activation_threshold: form.activation_threshold,
        preferred_voice: form.preferred_voice
      }
    })

    if (authError) throw authError

    // Update public.users table
    const { error: publicError } = await supabase
      .from('users')
      .update({
        min_endpointing_delay: form.min_endpointing_delay,
        max_endpointing_delay: form.max_endpointing_delay,
        min_speech_duration: form.min_speech_duration,
        min_silence_duration: form.min_silence_duration,
        prefix_padding_duration: form.prefix_padding_duration,
        max_buffered_speech: form.max_buffered_speech,
        activation_threshold: form.activation_threshold,
        preferred_voice: form.preferred_voice,
        updated_at: new Date().toISOString()
      })
      .eq('supabase_id', user.value.id)

    if (publicError) throw publicError

    // Update last saved timestamp
    lastSaved.value = new Date().toLocaleTimeString()
    
    // Show success message
    console.log('Settings saved successfully')
    
  } catch (error) {
    console.error('Error saving settings:', error)
    alert('Error saving settings: ' + error.message)
  } finally {
    isSaving.value = false
  }
}

// Watch for form changes and auto-save after a delay
let saveTimeout = null
watch(form, () => {
  // Clear existing timeout
  if (saveTimeout) clearTimeout(saveTimeout)
  
  // Validate form before auto-saving
  if (validateForm()) {
    // Set new timeout for auto-save
    saveTimeout = setTimeout(() => {
      saveSettings()
    }, 2000) // Auto-save after 2 seconds of inactivity
  }
}, { deep: true })

// Form validation
const validateForm = () => {
  // Ensure min endpointing delay is less than max endpointing delay
  if (form.min_endpointing_delay >= form.max_endpointing_delay) {
    return false
  }
  
  // All other validations are handled by the HTML input constraints
  return true
}

// Load settings on mount
onMounted(async () => {
  // Wait for session to be available
  const waitForSession = async () => {
    if (session.value) {
      await loadCurrentSettings()
    } else {
      setTimeout(waitForSession, 500)
    }
  }
  
  await waitForSession()
})
</script>

<style scoped>
.slider {
  -webkit-appearance: none;
  appearance: none;
}

.slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.slider::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #3b82f6;
  cursor: pointer;
  border: 2px solid #ffffff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.slider::-webkit-slider-track {
  background: #e5e7eb;
  height: 8px;
  border-radius: 4px;
}

.slider::-moz-range-track {
  background: #e5e7eb;
  height: 8px;
  border-radius: 4px;
}
</style>
