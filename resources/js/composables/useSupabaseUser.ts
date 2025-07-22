import { ref, provide, inject, Ref } from 'vue';
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
  provide('supabaseUser', user);
  provide('supabaseSession', session);
}

export function useSupabaseUser() {
  return { user: inject('supabaseUser', user), session: inject('supabaseSession', session) };
} 