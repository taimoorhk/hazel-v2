#!/bin/bash

echo "🧪 Testing Real-time Sync from auth.users to public.users"
echo "====================================================="
echo ""

# 1. Check current sync status
echo "1. Current Sync Status:"
STATUS=$(curl -s http://localhost:8000/api/sync-status)
echo "$STATUS" | jq .
echo ""

# 2. Test creating a new user (should trigger real-time sync)
echo "2. Testing User Creation (Real-time Sync):"
TEST_EMAIL="realtime-test-$(date +%s)@example.com"
TEST_NAME="Real-time Test User $(date +%s)"

echo "   Creating user: $TEST_EMAIL"
CREATE_RESPONSE=$(curl -s -X POST http://localhost:8000/api/create-test-user \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$TEST_EMAIL\",\"name\":\"$TEST_NAME\",\"role\":\"Normal User\"}")

echo "$CREATE_RESPONSE" | jq .

if echo "$CREATE_RESPONSE" | jq -e '.success' > /dev/null; then
    echo "   ✅ User created successfully"
    echo "   🔄 Real-time sync should have triggered automatically"
else
    echo "   ❌ Failed to create user"
    echo "   Response: $CREATE_RESPONSE"
fi
echo ""

# 3. Check if user exists in Laravel
echo "3. Verifying User in Laravel:"
LARAVEL_RESPONSE=$(curl -s -X POST http://localhost:8000/api/verify-user-exists \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$TEST_EMAIL\"}")

echo "$LARAVEL_RESPONSE" | jq .

if echo "$LARAVEL_RESPONSE" | jq -e '.exists' > /dev/null; then
    echo "   ✅ User exists in Laravel"
    SUPABASE_ID=$(echo "$LARAVEL_RESPONSE" | jq -r '.supabase_id // "Not set"')
    echo "   - Supabase ID: $SUPABASE_ID"
else
    echo "   ❌ User not found in Laravel"
fi
echo ""

# 4. Check if user exists in Supabase Auth
echo "4. Checking Supabase Auth:"
SUPABASE_RESPONSE=$(curl -s -X POST http://localhost:8000/api/check-supabase-user \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$TEST_EMAIL\"}")

echo "$SUPABASE_RESPONSE" | jq .

if echo "$SUPABASE_RESPONSE" | jq -e '.exists' > /dev/null; then
    echo "   ✅ User exists in Supabase Auth"
    SUPABASE_ID=$(echo "$SUPABASE_RESPONSE" | jq -r '.supabase_id // "Not set"')
    echo "   - Supabase ID: $SUPABASE_ID"
else
    echo "   ❌ User not found in Supabase Auth"
fi
echo ""

# 5. Check public.users table (should be synced via triggers)
echo "5. Checking public.users table (Real-time Sync):"
PUBLIC_USERS_RESPONSE=$(curl -s -X POST http://localhost:8000/api/check-public-users \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"$TEST_EMAIL\"}")

echo "$PUBLIC_USERS_RESPONSE" | jq .

if echo "$PUBLIC_USERS_RESPONSE" | jq -e '.exists' > /dev/null; then
    echo "   ✅ User exists in public.users table"
    NAME=$(echo "$PUBLIC_USERS_RESPONSE" | jq -r '.name // "N/A"')
    EMAIL=$(echo "$PUBLIC_USERS_RESPONSE" | jq -r '.email // "N/A"')
    SUPABASE_ID=$(echo "$PUBLIC_USERS_RESPONSE" | jq -r '.supabase_id // "N/A"')
    echo "   - Name: $NAME"
    echo "   - Email: $EMAIL"
    echo "   - Supabase ID: $SUPABASE_ID"
    echo "   🎉 Real-time sync is working perfectly!"
else
    echo "   ❌ User not found in public.users table"
    echo "   ⚠️  Real-time sync may not be working"
    echo "   💡 Make sure the Supabase migration has been applied"
fi
echo ""

# 6. Final sync status
echo "6. Final Sync Status:"
FINAL_STATUS=$(curl -s http://localhost:8000/api/sync-status)
echo "$FINAL_STATUS" | jq .

echo ""
echo "✅ Test completed!"
echo ""
echo "📋 Next Steps:"
echo "1. Apply the Supabase migration if not done already"
echo "2. Run this test script again to verify real-time sync"
echo "3. Monitor the logs for real-time sync events"
echo ""
echo "🔧 To apply the Supabase migration:"
echo "1. Go to your Supabase Dashboard"
echo "2. Navigate to SQL Editor"
echo "3. Copy and paste the content from: supabase/migrations/20250725183410_add_onboarding_question_to_user_metadata.sql"
echo "4. Click Run to execute the migration" 