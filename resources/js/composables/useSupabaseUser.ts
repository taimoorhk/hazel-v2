import { ref, provide, inject, Ref, onMounted } from 'vue';
import { supabase } from '@/lib/supabaseClient';
import type { User, Session } from '@supabase/supabase-js';

const user: Ref<User | null> = ref(null);
const session: Ref<Session | null> = ref(null);

export function provideSupabaseUser() {
  // Fetch session immediately
  supabase.auth.getSession().then(({ data }) => {
    user.value = data.session?.user || null;
    session.value = data.session || null;
  });
  // Listen for auth state changes
  supabase.auth.onAuthStateChange((_event, newSession) => {
    user.value = newSession?.user || null;
    session.value = newSession || null;
  });
  // Poll for user updates every 10 seconds
  let pollInterval: number | undefined;
  onMounted(() => {
    pollInterval = window.setInterval(async () => {
      const { data } = await supabase.auth.getUser();
      if (data && data.user) {
        user.value = data.user;
      }
    }, 10000);
  });
  provide('supabaseUser', user);
  provide('supabaseSession', session);
}

export function useSupabaseUser() {
  return { user: inject('supabaseUser', user), session: inject('supabaseSession', session) };
} 