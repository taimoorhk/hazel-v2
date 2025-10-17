<?php

namespace App\Http\Controllers;

use App\Services\DigitalOceanSpacesService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TestStatsController extends Controller
{
    /**
     * Test DigitalOcean Spaces connection
     *
     * @return JsonResponse
     */
    public function testConnection(): JsonResponse
    {
        try {
            // Test with specific account IDs mentioned by user
            $profileId = request()->get('profile_id', 6);
            $accountId = request()->get('account_id', 6);
            
            $digitalOceanService = new DigitalOceanSpacesService();
            
            // Get actual credentials from config
            $key = config('services.digitalocean.key');
            $secret = config('services.digitalocean.secret');
            $hasCredentials = !empty($key) && !empty($secret);
            
            $exists = false;
            $error = null;
            
            if ($hasCredentials) {
                try {
                    $exists = $digitalOceanService->verifyPath($profileId, $accountId);
                } catch (\Exception $e) {
                    $error = $e->getMessage();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'DigitalOcean Spaces connection test completed',
                'test_profile_id' => $profileId,
                'test_account_id' => $accountId,
                'path_exists' => $exists,
                'error' => $error,
                'configuration' => [
                    'bucket' => config('services.digitalocean.bucket_name'),
                    'region' => config('services.digitalocean.region'),
                    'endpoint' => config('services.digitalocean.endpoint'),
                    'base_path' => config('services.digitalocean.base_path'),
                    'has_credentials' => $hasCredentials,
                    'key_length' => $key ? strlen($key) : 0,
                    'secret_length' => $secret ? strlen($secret) : 0,
                    'key_preview' => $key ? substr($key, 0, 8) . '...' : 'empty',
                    'secret_preview' => $secret ? substr($secret, 0, 8) . '...' : 'empty'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'DigitalOcean Spaces connection test failed',
                'error' => $e->getMessage(),
                'configuration' => [
                    'bucket' => config('services.digitalocean.bucket_name'),
                    'region' => config('services.digitalocean.region'),
                    'endpoint' => config('services.digitalocean.endpoint'),
                    'base_path' => config('services.digitalocean.base_path'),
                    'has_credentials' => !empty(config('services.digitalocean.key')) && !empty(config('services.digitalocean.secret'))
                ]
            ], 500);
        }
    }

    /**
     * Get sample stats data structure for specific accounts
     *
     * @return JsonResponse
     */
    public function getSampleData(): JsonResponse
    {
        $request = request();
        $profileId = $request->get('profile_id', 6);
        $accountId = $request->get('account_id', 6);
        
        // Generate comprehensive health data for Account ID 6 (mtaimoorhas1@gmail.com)
        $sampleStats = [
            'profile_id' => (int)$profileId,
            'account_id' => (int)$accountId,
            'files' => [
                [
                    'filename' => '20250115_090000_123456.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'sample_data' => [
                        'call_id' => '20250115_090000_123456',
                        'duration' => 245,
                        'sentiment' => 'positive',
                        'topics' => ['health', 'medication', 'family', 'daily_routine'],
                        'summary' => 'Morning health check-in discussing medication adherence and family updates',
                        'analysis' => [
                            'mood' => 'content',
                            'engagement' => 'high',
                            'clarity' => 'excellent',
                            'energy_level' => 'good',
                            'sleep_quality' => 'fair',
                            'appetite' => 'normal',
                            'mobility' => 'good',
                            'memory' => 'clear',
                            'social_connection' => 'strong',
                            'medication_compliance' => 'excellent'
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_140000_789012.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'sample_data' => [
                        'call_id' => '20250115_140000_789012',
                        'duration' => 180,
                        'sentiment' => 'neutral',
                        'topics' => ['weather', 'hobbies', 'exercise', 'nutrition'],
                        'summary' => 'Afternoon conversation about daily activities and health habits',
                        'analysis' => [
                            'mood' => 'neutral',
                            'engagement' => 'medium',
                            'clarity' => 'good',
                            'energy_level' => 'moderate',
                            'sleep_quality' => 'good',
                            'appetite' => 'good',
                            'mobility' => 'excellent',
                            'memory' => 'good',
                            'social_connection' => 'moderate',
                            'medication_compliance' => 'good'
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_190000_345678.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'sample_data' => [
                        'call_id' => '20250115_190000_345678',
                        'duration' => 320,
                        'sentiment' => 'positive',
                        'topics' => ['family', 'memories', 'health_goals', 'gratitude'],
                        'summary' => 'Evening reflection on family memories and health improvement goals',
                        'analysis' => [
                            'mood' => 'happy',
                            'engagement' => 'high',
                            'clarity' => 'excellent',
                            'energy_level' => 'good',
                            'sleep_quality' => 'excellent',
                            'appetite' => 'normal',
                            'mobility' => 'good',
                            'memory' => 'excellent',
                            'social_connection' => 'strong',
                            'medication_compliance' => 'excellent'
                        ]
                    ]
                ]
            ],
            'summary' => [
                'total_calls' => 15,
                'total_duration' => 2850,
                'average_sentiment' => 'positive',
                'most_common_topics' => ['health', 'family', 'medication', 'daily_routine', 'memories'],
                'last_call' => '2025-01-15T19:00:00Z',
                'health_score' => 8.7,
                'engagement_trend' => 'increasing',
                'mood_stability' => 'stable',
                'clarity_consistency' => 'excellent',
                'medication_adherence' => 'excellent',
                'social_wellness' => 'strong'
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Sample stats data structure',
            'data' => $sampleStats,
            'api_endpoints' => [
                'GET /api/stats/profile?profile_id={id}&account_id={id}' => 'Get all stats for profile',
                'GET /api/stats/profile/summary?profile_id={id}&account_id={id}' => 'Get stats summary',
                'GET /api/stats/profile/file?profile_id={id}&account_id={id}&filename={file}' => 'Get specific file',
                'GET /api/stats/profile/verify?profile_id={id}&account_id={id}' => 'Verify path exists',
                'GET /api/stats/elderly-profile?profile_id={id}&elderly_account_id={id}' => 'Get elderly profile stats'
            ]
        ]);
    }

    /**
     * Get elderly profile stats data (Profile 15)
     *
     * @return JsonResponse
     */
    public function getElderlyProfileData(): JsonResponse
    {
        // Generate comprehensive health data for Profile 15 (jsahib@gmail.com)
        $elderlyStats = [
            'profile_id' => 15,
            'account_id' => 6, // Under Account ID 6
            'files' => [
                [
                    'filename' => '20250115_080000_111111.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'sample_data' => [
                        'call_id' => '20250115_080000_111111',
                        'duration' => 195,
                        'sentiment' => 'positive',
                        'topics' => ['health', 'medication', 'doctor_visit', 'family'],
                        'summary' => 'Morning health check discussing recent doctor visit and medication changes',
                        'analysis' => [
                            'mood' => 'content',
                            'engagement' => 'high',
                            'clarity' => 'good',
                            'energy_level' => 'moderate',
                            'sleep_quality' => 'good',
                            'appetite' => 'normal',
                            'mobility' => 'fair',
                            'memory' => 'good',
                            'social_connection' => 'strong',
                            'medication_compliance' => 'excellent',
                            'pain_level' => 'mild',
                            'balance' => 'stable',
                            'independence' => 'good'
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_130000_222222.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'sample_data' => [
                        'call_id' => '20250115_130000_222222',
                        'duration' => 165,
                        'sentiment' => 'neutral',
                        'topics' => ['hobbies', 'weather', 'exercise', 'nutrition'],
                        'summary' => 'Afternoon conversation about daily activities and gentle exercise routine',
                        'analysis' => [
                            'mood' => 'neutral',
                            'engagement' => 'medium',
                            'clarity' => 'fair',
                            'energy_level' => 'low',
                            'sleep_quality' => 'fair',
                            'appetite' => 'reduced',
                            'mobility' => 'limited',
                            'memory' => 'fair',
                            'social_connection' => 'moderate',
                            'medication_compliance' => 'good',
                            'pain_level' => 'moderate',
                            'balance' => 'unstable',
                            'independence' => 'limited'
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_180000_333333.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'sample_data' => [
                        'call_id' => '20250115_180000_333333',
                        'duration' => 280,
                        'sentiment' => 'positive',
                        'topics' => ['family', 'memories', 'health_concerns', 'gratitude'],
                        'summary' => 'Evening conversation sharing family memories and expressing health concerns',
                        'analysis' => [
                            'mood' => 'happy',
                            'engagement' => 'high',
                            'clarity' => 'excellent',
                            'energy_level' => 'good',
                            'sleep_quality' => 'excellent',
                            'appetite' => 'good',
                            'mobility' => 'good',
                            'memory' => 'excellent',
                            'social_connection' => 'strong',
                            'medication_compliance' => 'excellent',
                            'pain_level' => 'mild',
                            'balance' => 'stable',
                            'independence' => 'good'
                        ]
                    ]
                ]
            ],
            'summary' => [
                'total_calls' => 12,
                'total_duration' => 2100,
                'average_sentiment' => 'positive',
                'most_common_topics' => ['health', 'medication', 'family', 'memories', 'doctor_visit'],
                'last_call' => '2025-01-15T18:00:00Z',
                'health_score' => 7.2,
                'engagement_trend' => 'stable',
                'mood_stability' => 'stable',
                'clarity_consistency' => 'good',
                'medication_adherence' => 'excellent',
                'social_wellness' => 'strong',
                'mobility_assessment' => 'fair',
                'pain_management' => 'effective',
                'independence_level' => 'good'
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Elderly profile stats data structure',
            'data' => $elderlyStats,
            'profile_info' => [
                'name' => 'J. Sahib',
                'email' => 'jsahib@gmail.com',
                'account_holder' => 'mtaimoorhas1@gmail.com',
                'relationship' => 'Elderly Profile under Account ID 6'
            ]
        ]);
    }
}
