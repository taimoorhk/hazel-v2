# DigitalOcean Spaces Stats API Documentation

## Overview

This API provides access to conversation stats and analysis data stored in DigitalOcean Spaces. The data is organized by profile ID and account ID following the structure: `hazel-audio-clips/livekit/audio_transcripts/{profile_id}/{account_id}/`

## DigitalOcean Storage Structure

```
hazel-audio-clips/livekit/audio_transcripts/
├── 101/
│   ├── 101/                          # Main user profile (profile_id = account_id)
│   │   ├── 20251006_151002_420881.ogg
│   │   ├── 20251006_151002_420881_user_voice.wav
│   │   ├── 20251006_151133_388_transcript.json
│   │   └── 20251006_151002_420881.ogg.json
│   └── 102/                          # Elderly profile under profile 101
│       ├── 20251006_160000_123456.ogg
│       ├── 20251006_160000_123456_user_voice.wav
│       ├── 20251006_160001_789_transcript.json
│       └── 20251006_160000_123456.ogg.json
```

## API Endpoints

### Base URL
```
http://localhost:8000/api
```

### Authentication
All stats endpoints require Supabase authentication via the `supabase.auth` middleware.

### 1. Get Profile Stats

**Endpoint:** `GET /api/stats/profile`

**Parameters:**
- `profile_id` (required): The profile ID
- `account_id` (required): The account ID

**Example Request:**
```bash
curl -H "Authorization: Bearer YOUR_SUPABASE_TOKEN" \
  "http://localhost:8000/api/stats/profile?profile_id=101&account_id=101"
```

**Response:**
```json
{
  "success": true,
  "message": "Stats retrieved successfully",
  "profile_id": 101,
  "account_id": 101,
  "data": [
    {
      "filename": "20251006_151002_420881.ogg.json",
      "path": "livekit/audio_transcripts/101/101/20251006_151002_420881.ogg.json",
      "last_modified": "2025-10-06 15:10:02",
      "size": 2048,
      "data": {
        "call_analysis": {
          "duration": 180,
          "sentiment": "positive",
          "topics": ["health", "family"],
          "summary": "Conversation about health updates"
        }
      }
    }
  ],
  "count": 1
}
```

### 2. Get Stats Summary

**Endpoint:** `GET /api/stats/profile/summary`

**Parameters:**
- `profile_id` (required): The profile ID
- `account_id` (required): The account ID

**Example Request:**
```bash
curl -H "Authorization: Bearer YOUR_SUPABASE_TOKEN" \
  "http://localhost:8000/api/stats/profile/summary?profile_id=101&account_id=101"
```

**Response:**
```json
{
  "success": true,
  "message": "Stats summary retrieved successfully",
  "profile_id": 101,
  "account_id": 101,
  "data": {
    "total_files": 2,
    "total_calls": 2,
    "latest_call": {
      "filename": "20251006_151002_420881.ogg.json",
      "timestamp": "2025-10-06 15:10:02",
      "data": {
        "call_analysis": {
          "duration": 180,
          "sentiment": "positive"
        }
      }
    },
    "summary": [
      {
        "filename": "20251006_151002_420881.ogg.json",
        "timestamp": "2025-10-06 15:10:02",
        "duration": 180,
        "sentiment": "positive",
        "topics": ["health", "family"],
        "summary": "Conversation about health updates"
      }
    ]
  }
}
```

### 3. Get Specific Stats File

**Endpoint:** `GET /api/stats/profile/file`

**Parameters:**
- `profile_id` (required): The profile ID
- `account_id` (required): The account ID
- `filename` (required): The filename to retrieve

**Example Request:**
```bash
curl -H "Authorization: Bearer YOUR_SUPABASE_TOKEN" \
  "http://localhost:8000/api/stats/profile/file?profile_id=101&account_id=101&filename=20251006_151002_420881.ogg.json"
```

### 4. Verify Path Exists

**Endpoint:** `GET /api/stats/profile/verify`

**Parameters:**
- `profile_id` (required): The profile ID
- `account_id` (required): The account ID

**Example Request:**
```bash
curl -H "Authorization: Bearer YOUR_SUPABASE_TOKEN" \
  "http://localhost:8000/api/stats/profile/verify?profile_id=101&account_id=101"
```

**Response:**
```json
{
  "success": true,
  "message": "Path verification completed",
  "profile_id": 101,
  "account_id": 101,
  "exists": true
}
```

### 5. Get Elderly Profile Stats

**Endpoint:** `GET /api/stats/elderly-profile`

**Parameters:**
- `profile_id` (required): The main profile ID
- `elderly_account_id` (required): The elderly profile account ID

**Example Request:**
```bash
curl -H "Authorization: Bearer YOUR_SUPABASE_TOKEN" \
  "http://localhost:8000/api/stats/elderly-profile?profile_id=101&elderly_account_id=102"
```

## Test Endpoints (No Authentication Required)

### 1. Test Connection

**Endpoint:** `GET /api/test-stats-connection`

**Example Request:**
```bash
curl "http://localhost:8000/api/test-stats-connection"
```

**Response:**
```json
{
  "success": true,
  "message": "DigitalOcean Spaces connection test completed",
  "test_profile_id": 101,
  "test_account_id": 101,
  "path_exists": false,
  "configuration": {
    "bucket": "hazel-audio-clips",
    "region": "nyc3",
    "endpoint": "https://nyc3.digitaloceanspaces.com",
    "base_path": "livekit/audio_transcripts",
    "has_credentials": false
  }
}
```

### 2. Get Sample Data Structure

**Endpoint:** `GET /api/test-stats-sample`

**Example Request:**
```bash
curl "http://localhost:8000/api/test-stats-sample"
```

## Configuration

### Environment Variables

Add these to your `.env` file:

```env
# DigitalOcean Spaces Configuration
DIGITALOCEAN_SPACES_KEY=your_access_key
DIGITALOCEAN_SPACES_SECRET=your_secret_key
DIGITALOCEAN_SPACES_REGION=nyc3
DIGITALOCEAN_SPACES_ENDPOINT=https://nyc3.digitaloceanspaces.com
DIGITALOCEAN_SPACES_BUCKET=hazel-audio-clips
DIGITALOCEAN_SPACES_BASE_PATH=livekit/audio_transcripts
```

## File Types Processed

The API processes the following JSON file types:

1. **Transcript Files** (`*_transcript.json`)
   - Conversation transcripts
   - Speaker identification
   - Timestamps

2. **Analysis Files** (`*.ogg.json`)
   - AI analysis results
   - Sentiment analysis
   - Topic extraction
   - Call metadata

## Error Responses

### 404 - Not Found
```json
{
  "success": false,
  "message": "No data found for the specified profile and account",
  "profile_id": 101,
  "account_id": 101
}
```

### 422 - Validation Error
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "profile_id": ["The profile id field is required."],
    "account_id": ["The account id field is required."]
  }
}
```

### 500 - Server Error
```json
{
  "success": false,
  "message": "Failed to retrieve stats",
  "error": "Connection timeout"
}
```

## Usage Examples

### Frontend Integration

```javascript
// Get stats for main user profile
const getMainProfileStats = async (profileId, accountId) => {
  const response = await fetch(`/api/stats/profile?profile_id=${profileId}&account_id=${accountId}`, {
    headers: {
      'Authorization': `Bearer ${supabaseToken}`,
      'Content-Type': 'application/json'
    }
  });
  return await response.json();
};

// Get stats for elderly profile
const getElderlyProfileStats = async (profileId, elderlyAccountId) => {
  const response = await fetch(`/api/stats/elderly-profile?profile_id=${profileId}&elderly_account_id=${elderlyAccountId}`, {
    headers: {
      'Authorization': `Bearer ${supabaseToken}`,
      'Content-Type': 'application/json'
    }
  });
  return await response.json();
};

// Get stats summary
const getStatsSummary = async (profileId, accountId) => {
  const response = await fetch(`/api/stats/profile/summary?profile_id=${profileId}&account_id=${accountId}`, {
    headers: {
      'Authorization': `Bearer ${supabaseToken}`,
      'Content-Type': 'application/json'
    }
  });
  return await response.json();
};
```

## Security

- All endpoints are protected by Supabase authentication
- Path verification ensures users can only access their own data
- Profile and account ID validation prevents unauthorized access
- Comprehensive error logging for debugging

## Rate Limiting

Currently no rate limiting is implemented, but it's recommended to add rate limiting for production use.

## Monitoring

All API calls are logged with:
- Request parameters
- Response status
- Error details
- Performance metrics

Check Laravel logs for detailed information about API usage and errors.
