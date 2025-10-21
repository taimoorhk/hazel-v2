import { createClient } from '@supabase/supabase-js';

const supabaseUrl = 'https://rertznygkkeykftoapge.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InJlcnR6bnlna2tleWtmdG9hcGdlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMxOTMzOTgsImV4cCI6MjA2ODc2OTM5OH0.Cy6VMJUtBTxGSJZHrnv9fG0Xr0fJvbowyf2UURSvY2E';

export const supabase = createClient(supabaseUrl, supabaseKey, {
  auth: {
    redirectTo: 'http://24.199.87.186/auth/confirm'
  }
}); 