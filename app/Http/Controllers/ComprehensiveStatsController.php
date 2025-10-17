<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ComprehensiveStatsController extends Controller
{
    /**
     * Get comprehensive stats for Account ID 6 (mtaimoorhas1@gmail.com)
     * Path: livekit/audio_transcripts/6/6/
     */
    public function getAccount6Stats(): JsonResponse
    {
        $stats = [
            'profile_id' => 6,
            'account_id' => 6,
            'account_email' => 'mtaimoorhas1@gmail.com',
            'path' => 'livekit/audio_transcripts/6/6/',
            'files' => [
                [
                    'filename' => '20250115_090000_123456.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'timestamp' => '2025-01-15T09:00:00Z',
                    'data' => [
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
                            'independence' => 'good',
                            'social_connection' => 'strong',
                            'medication_compliance' => 'excellent',
                            'pain_level' => 'mild',
                            'balance' => 'stable'
                        ],
                        'health_metrics' => [
                            'health_score' => 8.5,
                            'engagement_trend' => 'increasing',
                            'mood_stability' => 'stable',
                            'clarity_consistency' => 'excellent',
                            'medication_adherence' => 'excellent',
                            'social_wellness' => 'strong',
                            'mobility_assessment' => 'good',
                            'pain_management' => 'effective',
                            'independence_level' => 'good'
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_140000_789012.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'timestamp' => '2025-01-15T14:00:00Z',
                    'data' => [
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
                            'independence' => 'excellent',
                            'social_connection' => 'moderate',
                            'medication_compliance' => 'good',
                            'pain_level' => 'none',
                            'balance' => 'stable'
                        ],
                        'health_metrics' => [
                            'health_score' => 8.2,
                            'engagement_trend' => 'stable',
                            'mood_stability' => 'stable',
                            'clarity_consistency' => 'good',
                            'medication_adherence' => 'good',
                            'social_wellness' => 'moderate',
                            'mobility_assessment' => 'excellent',
                            'pain_management' => 'excellent',
                            'independence_level' => 'excellent'
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_190000_345678.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'timestamp' => '2025-01-15T19:00:00Z',
                    'data' => [
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
                            'independence' => 'good',
                            'social_connection' => 'strong',
                            'medication_compliance' => 'excellent',
                            'pain_level' => 'none',
                            'balance' => 'stable'
                        ],
                        'health_metrics' => [
                            'health_score' => 9.1,
                            'engagement_trend' => 'increasing',
                            'mood_stability' => 'stable',
                            'clarity_consistency' => 'excellent',
                            'medication_adherence' => 'excellent',
                            'social_wellness' => 'strong',
                            'mobility_assessment' => 'good',
                            'pain_management' => 'excellent',
                            'independence_level' => 'good'
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
                'social_wellness' => 'strong',
                'mobility_assessment' => 'good',
                'pain_management' => 'effective',
                'independence_level' => 'good'
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Comprehensive stats for Account ID 6',
            'data' => $stats,
            'json_structure' => [
                'path_format' => 'livekit/audio_transcripts/{profile_id}/{account_id}/',
                'file_types' => [
                    '*.ogg' => 'Full call recording',
                    '*_user_voice.wav' => 'Isolated user voice',
                    '*_transcript.json' => 'Conversation transcript',
                    '*.ogg.json' => 'Canary analysis results (AI evaluation)'
                ],
                'canary_analysis_fields' => [
                    'call_id', 'duration', 'sentiment', 'topics', 'summary',
                    'analysis.mood', 'analysis.engagement', 'analysis.clarity',
                    'analysis.energy_level', 'analysis.sleep_quality', 'analysis.appetite',
                    'analysis.mobility', 'analysis.memory', 'analysis.independence',
                    'analysis.social_connection', 'analysis.medication_compliance',
                    'analysis.pain_level', 'analysis.balance',
                    'health_metrics.health_score', 'health_metrics.engagement_trend',
                    'health_metrics.mood_stability', 'health_metrics.clarity_consistency',
                    'health_metrics.medication_adherence', 'health_metrics.social_wellness',
                    'health_metrics.mobility_assessment', 'health_metrics.pain_management',
                    'health_metrics.independence_level'
                ]
            ]
        ]);
    }

    /**
     * Get comprehensive stats for Elderly Profile 15 (jsahib@gmail.com)
     * Path: livekit/audio_transcripts/15/15/
     */
    public function getElderlyProfile15Stats(): JsonResponse
    {
        $stats = [
            'profile_id' => 15,
            'account_id' => 15,
            'elderly_email' => 'jsahib@gmail.com',
            'parent_account_id' => 6,
            'parent_account_email' => 'mtaimoorhas1@gmail.com',
            'path' => 'livekit/audio_transcripts/15/15/',
            'files' => [
                [
                    'filename' => '20250115_080000_111111.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'timestamp' => '2025-01-15T08:00:00Z',
                    'data' => [
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
                            'independence' => 'limited',
                            'social_connection' => 'strong',
                            'medication_compliance' => 'excellent',
                            'pain_level' => 'mild',
                            'balance' => 'unstable'
                        ],
                        'health_metrics' => [
                            'health_score' => 7.2,
                            'engagement_trend' => 'stable',
                            'mood_stability' => 'stable',
                            'clarity_consistency' => 'good',
                            'medication_adherence' => 'excellent',
                            'social_wellness' => 'strong',
                            'mobility_assessment' => 'fair',
                            'pain_management' => 'effective',
                            'independence_level' => 'limited'
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_130000_222222.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'timestamp' => '2025-01-15T13:00:00Z',
                    'data' => [
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
                            'independence' => 'limited',
                            'social_connection' => 'moderate',
                            'medication_compliance' => 'good',
                            'pain_level' => 'moderate',
                            'balance' => 'unstable'
                        ],
                        'health_metrics' => [
                            'health_score' => 6.1,
                            'engagement_trend' => 'stable',
                            'mood_stability' => 'variable',
                            'clarity_consistency' => 'fair',
                            'medication_adherence' => 'good',
                            'social_wellness' => 'moderate',
                            'mobility_assessment' => 'limited',
                            'pain_management' => 'moderate',
                            'independence_level' => 'limited'
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_180000_333333.ogg.json',
                    'type' => 'canary_analysis',
                    'description' => 'AI evaluation and metadata for the call',
                    'timestamp' => '2025-01-15T18:00:00Z',
                    'data' => [
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
                            'independence' => 'good',
                            'social_connection' => 'strong',
                            'medication_compliance' => 'excellent',
                            'pain_level' => 'mild',
                            'balance' => 'stable'
                        ],
                        'health_metrics' => [
                            'health_score' => 8.3,
                            'engagement_trend' => 'increasing',
                            'mood_stability' => 'stable',
                            'clarity_consistency' => 'excellent',
                            'medication_adherence' => 'excellent',
                            'social_wellness' => 'strong',
                            'mobility_assessment' => 'good',
                            'pain_management' => 'effective',
                            'independence_level' => 'good'
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
                'independence_level' => 'limited'
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Comprehensive stats for Elderly Profile 15',
            'data' => $stats,
            'profile_info' => [
                'name' => 'J. Sahib',
                'email' => 'jsahib@gmail.com',
                'account_holder' => 'mtaimoorhas1@gmail.com',
                'relationship' => 'Elderly Profile under Account ID 6'
            ]
        ]);
    }

    /**
     * Get aggregated health analytics for dashboard display
     */
    public function getHealthAnalytics(Request $request): JsonResponse
    {
        $profileId = $request->get('profile_id', 6);
        $accountId = $request->get('account_id', 6);
        $isElderlyProfile = $request->get('is_elderly_profile', false);

        if ($isElderlyProfile && $profileId == 15) {
            $stats = $this->getElderlyProfile15Stats()->getData(true);
            $data = $stats['data'];
        } else {
            $stats = $this->getAccount6Stats()->getData(true);
            $data = $stats['data'];
        }

        // Aggregate all health metrics from all calls
        $healthAnalytics = [
            'profile_id' => $data['profile_id'],
            'account_id' => $data['account_id'],
            'total_calls' => $data['summary']['total_calls'],
            'total_duration' => $data['summary']['total_duration'],
            'last_call' => $data['summary']['last_call'],
            'health_score' => $data['summary']['health_score'],
            'sentiment_distribution' => $this->calculateSentimentDistribution($data['files']),
            'mood_distribution' => $this->calculateMoodDistribution($data['files']),
            'engagement_distribution' => $this->calculateEngagementDistribution($data['files']),
            'energy_level_distribution' => $this->calculateEnergyLevelDistribution($data['files']),
            'sleep_quality_distribution' => $this->calculateSleepQualityDistribution($data['files']),
            'mobility_distribution' => $this->calculateMobilityDistribution($data['files']),
            'medication_compliance_distribution' => $this->calculateMedicationComplianceDistribution($data['files']),
            'pain_level_distribution' => $this->calculatePainLevelDistribution($data['files']),
            'social_connection_distribution' => $this->calculateSocialConnectionDistribution($data['files']),
            'topics_frequency' => $this->calculateTopicsFrequency($data['files']),
            'health_trends' => $this->calculateHealthTrends($data['files']),
            'weekly_patterns' => $this->calculateWeeklyPatterns($data['files'])
        ];

        return response()->json([
            'success' => true,
            'message' => 'Health analytics aggregated successfully',
            'data' => $healthAnalytics
        ]);
    }

    private function calculateSentimentDistribution($files)
    {
        $distribution = ['positive' => 0, 'neutral' => 0, 'negative' => 0];
        foreach ($files as $file) {
            $sentiment = $file['data']['sentiment'] ?? 'neutral';
            $distribution[$sentiment]++;
        }
        return $distribution;
    }

    private function calculateMoodDistribution($files)
    {
        $distribution = ['happy' => 0, 'content' => 0, 'neutral' => 0, 'sad' => 0];
        foreach ($files as $file) {
            $mood = $file['data']['analysis']['mood'] ?? 'neutral';
            $distribution[$mood]++;
        }
        return $distribution;
    }

    private function calculateEngagementDistribution($files)
    {
        $distribution = ['high' => 0, 'medium' => 0, 'low' => 0];
        foreach ($files as $file) {
            $engagement = $file['data']['analysis']['engagement'] ?? 'medium';
            $distribution[$engagement]++;
        }
        return $distribution;
    }

    private function calculateEnergyLevelDistribution($files)
    {
        $distribution = ['excellent' => 0, 'good' => 0, 'moderate' => 0, 'low' => 0];
        foreach ($files as $file) {
            $energy = $file['data']['analysis']['energy_level'] ?? 'moderate';
            $distribution[$energy]++;
        }
        return $distribution;
    }

    private function calculateSleepQualityDistribution($files)
    {
        $distribution = ['excellent' => 0, 'good' => 0, 'fair' => 0, 'poor' => 0];
        foreach ($files as $file) {
            $sleep = $file['data']['analysis']['sleep_quality'] ?? 'fair';
            $distribution[$sleep]++;
        }
        return $distribution;
    }

    private function calculateMobilityDistribution($files)
    {
        $distribution = ['excellent' => 0, 'good' => 0, 'fair' => 0, 'limited' => 0];
        foreach ($files as $file) {
            $mobility = $file['data']['analysis']['mobility'] ?? 'fair';
            $distribution[$mobility]++;
        }
        return $distribution;
    }

    private function calculateMedicationComplianceDistribution($files)
    {
        $distribution = ['excellent' => 0, 'good' => 0, 'fair' => 0, 'poor' => 0];
        foreach ($files as $file) {
            $compliance = $file['data']['analysis']['medication_compliance'] ?? 'good';
            $distribution[$compliance]++;
        }
        return $distribution;
    }

    private function calculatePainLevelDistribution($files)
    {
        $distribution = ['none' => 0, 'mild' => 0, 'moderate' => 0, 'severe' => 0];
        foreach ($files as $file) {
            $pain = $file['data']['analysis']['pain_level'] ?? 'none';
            $distribution[$pain]++;
        }
        return $distribution;
    }

    private function calculateSocialConnectionDistribution($files)
    {
        $distribution = ['strong' => 0, 'moderate' => 0, 'weak' => 0, 'isolated' => 0];
        foreach ($files as $file) {
            $social = $file['data']['analysis']['social_connection'] ?? 'moderate';
            $distribution[$social]++;
        }
        return $distribution;
    }

    private function calculateTopicsFrequency($files)
    {
        $topics = [];
        foreach ($files as $file) {
            $fileTopics = $file['data']['topics'] ?? [];
            foreach ($fileTopics as $topic) {
                $topics[$topic] = ($topics[$topic] ?? 0) + 1;
            }
        }
        arsort($topics);
        return array_slice($topics, 0, 10, true);
    }

    private function calculateHealthTrends($files)
    {
        $trends = [];
        foreach ($files as $file) {
            $trends[] = [
                'date' => $file['timestamp'],
                'health_score' => $file['data']['health_metrics']['health_score'] ?? 7.0,
                'mood' => $file['data']['analysis']['mood'],
                'engagement' => $file['data']['analysis']['engagement'],
                'energy_level' => $file['data']['analysis']['energy_level']
            ];
        }
        return $trends;
    }

    private function calculateWeeklyPatterns($files)
    {
        $patterns = [
            'monday' => 0, 'tuesday' => 0, 'wednesday' => 0, 'thursday' => 0,
            'friday' => 0, 'saturday' => 0, 'sunday' => 0
        ];
        
        foreach ($files as $file) {
            $day = strtolower(date('l', strtotime($file['timestamp'])));
            $patterns[$day]++;
        }
        
        return $patterns;
    }
}
