import { createClient } from '@supabase/supabase-js';

const supabaseUrl = 'https://rertznygkkeykftoapge.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InJlcnR6bnlna2tleWtmdG9hcGdlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTMxOTMzOTgsImV4cCI6MjA2ODc2OTM5OH0.Cy6VMJUtBTxGSJZHrnv9fG0Xr0fJvbowyf2UURSvY2E';

// Get the base URL from environment or use deployed IP
const getBaseUrl = () => {
  if (typeof window !== 'undefined') {
    // In browser, check if we're on localhost or deployed IP
    const hostname = window.location.hostname;
    if (hostname === 'localhost' || hostname === '127.0.0.1') {
      return 'http://24.199.87.186'; // Force deployed IP even in development
    }
    return window.location.origin;
  }
  return 'http://24.199.87.186'; // Default to deployed IP
};

export const supabase = createClient(supabaseUrl, supabaseKey, {
  auth: {
    redirectTo: `${getBaseUrl()}/auth/confirm`
  }
}); 