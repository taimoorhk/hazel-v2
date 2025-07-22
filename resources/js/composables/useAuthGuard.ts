import { ref, onMounted, inject } from 'vue';
import { supabase } from '@/lib/supabaseClient';
import type { Session } from '@supabase/supabase-js';

export function useAuthGuard() {
  const loading = ref(true);
  const session = inject<Session | null>('supabaseSession', null);

  const checkSession = async () => {
    let currentSession = session;
    if (!currentSession) {
      const { data } = await supabase.auth.getSession();
      currentSession = data.session;
    }
    loading.value = false;
    if (!currentSession) {
      window.location.href = '/';
    }
  };

  onMounted(() => {
    checkSession();
    supabase.auth.onAuthStateChange(() => {
      checkSession();
    });
  });

  return { loading };
} 