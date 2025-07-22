import { ref, onMounted } from 'vue';
import { supabase } from '@/lib/supabaseClient';

export function useAuthGuard() {
  const loading = ref(true);

  onMounted(async () => {
    const { data } = await supabase.auth.getSession();
    loading.value = false;
    if (!data.session) {
      window.location.href = '/login';
    }
  });

  return { loading };
} 