import { ref, onMounted, Ref } from 'vue';
import { supabase } from '@/lib/supabaseClient';
import type { User, Session } from '@supabase/supabase-js';

const user: Ref<User | null> = ref(null);
const session: Ref<Session | null> = ref(null);

export function useSupabaseUser() {
  onMounted(async () => {
    const { data } = await supabase.auth.getSession();
    user.value = data.session?.user || null;
    session.value = data.session || null;
    supabase.auth.onAuthStateChange((_event, newSession) => {
      user.value = newSession?.user || null;
      session.value = newSession || null;
    });
  });
  return { user, session };
} 