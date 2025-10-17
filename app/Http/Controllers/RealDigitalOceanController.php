<?php

namespace App\Http\Controllers;

use App\Services\DigitalOceanSpacesService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RealDigitalOceanController extends Controller
{
    private $digitalOceanService;

    public function __construct(DigitalOceanSpacesService $digitalOceanService)
    {
        $this->digitalOceanService = $digitalOceanService;
    }

    /**
     * Get real canary analysis data for Account ID 6 (mtaimoorhas1@gmail.com)
     * Path: livekit/audio_transcripts/6/-1/
     */
    public function getAccount6RealData(): JsonResponse
    {
        try {
            $accountId = 6;
            $profileId = -1; // Direct account holder
            
            Log::info("Fetching real data for Account ID {$accountId}, Profile ID {$profileId}");
            
            // Verify path exists
            $pathExists = $this->digitalOceanService->verifyPath($profileId, $accountId);
            
            if (!$pathExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for Account ID 6',
                    'account_id' => $accountId,
                    'profile_id' => $profileId,
                    'path' => "livekit/audio_transcripts/{$accountId}/{$profileId}/",
                    'path_exists' => false
                ], 404);
            }

            // Get all canary analysis files
            $stats = $this->digitalOceanService->getStatsForProfile($profileId, $accountId);
            
            // Get summary
            $summary = $this->digitalOceanService->getStatsSummary($profileId, $accountId);
            
            // Process and format the data
            $processedData = $this->processCanaryAnalysisData($stats, $summary, $accountId, $profileId);

            return response()->json([
                'success' => true,
                'message' => 'Real canary analysis data retrieved successfully',
                'account_info' => [
                    'account_id' => $accountId,
                    'profile_id' => $profileId,
                    'email' => 'mtaimoorhas1@gmail.com',
                    'path' => "livekit/audio_transcripts/{$accountId}/{$profileId}/",
                    'path_exists' => $pathExists
                ],
                'data' => $processedData,
                'raw_files_count' => count($stats)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching Account 6 real data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch real data for Account ID 6',
                'error' => $e->getMessage(),
                'account_info' => [
                    'account_id' => 6,
                    'profile_id' => -1,
                    'email' => 'mtaimoorhas1@gmail.com',
                    'path' => 'livekit/audio_transcripts/6/-1/'
                ]
            ], 500);
        }
    }

    /**
     * Get real canary analysis data for Profile ID 15 (jsahib@gmail.com)
     * Path: livekit/audio_transcripts/6/15/
     */
    public function getProfile15RealData(): JsonResponse
    {
        try {
            $accountId = 6;
            $profileId = 15; // Elderly profile under Account 6
            
            Log::info("Fetching real data for Account ID {$accountId}, Profile ID {$profileId}");
            
            // Verify path exists
            $pathExists = $this->digitalOceanService->verifyPath($profileId, $accountId);
            
            if (!$pathExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for Profile ID 15',
                    'account_id' => $accountId,
                    'profile_id' => $profileId,
                    'path' => "livekit/audio_transcripts/{$accountId}/{$profileId}/",
                    'path_exists' => false
                ], 404);
            }

            // Get all canary analysis files
            $stats = $this->digitalOceanService->getStatsForProfile($profileId, $accountId);
            
            // Get summary
            $summary = $this->digitalOceanService->getStatsSummary($profileId, $accountId);
            
            // Process and format the data
            $processedData = $this->processCanaryAnalysisData($stats, $summary, $accountId, $profileId);

            return response()->json([
                'success' => true,
                'message' => 'Real canary analysis data retrieved successfully',
                'profile_info' => [
                    'account_id' => $accountId,
                    'profile_id' => $profileId,
                    'email' => 'jsahib@gmail.com',
                    'parent_account' => 'mtaimoorhas1@gmail.com',
                    'path' => "livekit/audio_transcripts/{$accountId}/{$profileId}/",
                    'path_exists' => $pathExists
                ],
                'data' => $processedData,
                'raw_files_count' => count($stats)
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching Profile 15 real data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch real data for Profile ID 15',
                'error' => $e->getMessage(),
                'profile_info' => [
                    'account_id' => 6,
                    'profile_id' => 15,
                    'email' => 'jsahib@gmail.com',
                    'path' => 'livekit/audio_transcripts/6/15/'
                ]
            ], 500);
        }
    }

    /**
     * Test DigitalOcean connection with correct paths
     */
    public function testRealConnection(): JsonResponse
    {
        try {
            $tests = [
                [
                    'name' => 'Account ID 6 (mtaimoorhas1@gmail.com)',
                    'account_id' => 6,
                    'profile_id' => -1,
                    'path' => 'livekit/audio_transcripts/6/-1/',
                    'exists' => false
                ],
                [
                    'name' => 'Profile ID 15 (jsahib@gmail.com)',
                    'account_id' => 6,
                    'profile_id' => 15,
                    'path' => 'livekit/audio_transcripts/6/15/',
                    'exists' => false
                ]
            ];

            foreach ($tests as &$test) {
                try {
                    $test['exists'] = $this->digitalOceanService->verifyPath($test['profile_id'], $test['account_id']);
                    
                    if ($test['exists']) {
                        // Get file count
                        $stats = $this->digitalOceanService->getStatsForProfile($test['profile_id'], $test['account_id']);
                        $test['file_count'] = count($stats);
                        $test['files'] = array_map(function($file) {
                            return [
                                'filename' => $file['filename'],
                                'last_modified' => $file['last_modified'],
                                'size' => $file['size']
                            ];
                        }, $stats);
                    }
                } catch (\Exception $e) {
                    $test['error'] = $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'DigitalOcean connection test with correct paths',
                'configuration' => [
                    'bucket' => config('services.digitalocean.bucket_name'),
                    'region' => config('services.digitalocean.region'),
                    'endpoint' => config('services.digitalocean.endpoint'),
                    'base_path' => config('services.digitalocean.base_path'),
                    'has_credentials' => !empty(config('services.digitalocean.key')) && !empty(config('services.digitalocean.secret'))
                ],
                'path_tests' => $tests
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process canary analysis data from DigitalOcean
     */
    private function processCanaryAnalysisData(array $stats, array $summary, int $accountId, int $profileId): array
    {
        $processedFiles = [];
        $allTopics = [];
        $sentimentCounts = ['positive' => 0, 'neutral' => 0, 'negative' => 0];
        $totalDuration = 0;

        foreach ($stats as $stat) {
            if (isset($stat['data'])) {
                $data = $stat['data'];
                
                // Extract topics
                if (isset($data['topics']) && is_array($data['topics'])) {
                    $allTopics = array_merge($allTopics, $data['topics']);
                }
                
                // Count sentiment
                if (isset($data['sentiment'])) {
                    $sentiment = strtolower($data['sentiment']);
                    if (isset($sentimentCounts[$sentiment])) {
                        $sentimentCounts[$sentiment]++;
                    }
                }
                
                // Sum duration
                if (isset($data['duration'])) {
                    $totalDuration += $data['duration'];
                }

                $processedFiles[] = [
                    'filename' => $stat['filename'],
                    'timestamp' => $stat['last_modified'],
                    'duration' => $data['duration'] ?? null,
                    'sentiment' => $data['sentiment'] ?? null,
                    'topics' => $data['topics'] ?? [],
                    'summary' => $data['summary'] ?? null,
                    'analysis' => $data['analysis'] ?? null,
                    'health_metrics' => $data['health_metrics'] ?? null,
                    'raw_data' => $data // Include full raw data for debugging
                ];
            }
        }

        // Calculate most common topics
        $topicCounts = array_count_values($allTopics);
        arsort($topicCounts);
        $mostCommonTopics = array_keys(array_slice($topicCounts, 0, 10, true));

        // Calculate average sentiment
        $totalSentimentCount = array_sum($sentimentCounts);
        $averageSentiment = 'neutral';
        if ($totalSentimentCount > 0) {
            if ($sentimentCounts['positive'] > $sentimentCounts['neutral'] && $sentimentCounts['positive'] > $sentimentCounts['negative']) {
                $averageSentiment = 'positive';
            } elseif ($sentimentCounts['negative'] > $sentimentCounts['neutral'] && $sentimentCounts['negative'] > $sentimentCounts['positive']) {
                $averageSentiment = 'negative';
            }
        }

        return [
            'profile_id' => $profileId,
            'account_id' => $accountId,
            'files' => $processedFiles,
            'summary' => [
                'total_calls' => count($processedFiles),
                'total_duration' => $totalDuration,
                'average_sentiment' => $averageSentiment,
                'sentiment_distribution' => $sentimentCounts,
                'most_common_topics' => $mostCommonTopics,
                'last_call' => count($processedFiles) > 0 ? $processedFiles[0]['timestamp'] : null,
                'health_score' => $this->calculateHealthScore($processedFiles),
                'engagement_trend' => $this->calculateEngagementTrend($processedFiles),
                'mood_stability' => $this->calculateMoodStability($processedFiles),
                'clarity_consistency' => $this->calculateClarityConsistency($processedFiles),
                'medication_adherence' => $this->calculateMedicationAdherence($processedFiles),
                'social_wellness' => $this->calculateSocialWellness($processedFiles),
                'mobility_assessment' => $this->calculateMobilityAssessment($processedFiles),
                'pain_management' => $this->calculatePainManagement($processedFiles),
                'independence_level' => $this->calculateIndependenceLevel($processedFiles)
            ]
        ];
    }

    // Helper methods for calculating health metrics
    private function calculateHealthScore(array $files): float
    {
        if (empty($files)) return 0.0;
        
        $totalScore = 0;
        $count = 0;
        
        foreach ($files as $file) {
            if (isset($file['health_metrics']['health_score'])) {
                $totalScore += $file['health_metrics']['health_score'];
                $count++;
            }
        }
        
        return $count > 0 ? round($totalScore / $count, 1) : 7.0;
    }

    private function calculateEngagementTrend(array $files): string
    {
        if (count($files) < 2) return 'stable';
        
        $recentEngagement = $files[0]['analysis']['engagement'] ?? 'medium';
        $olderEngagement = $files[1]['analysis']['engagement'] ?? 'medium';
        
        $engagementLevels = ['low' => 1, 'medium' => 2, 'high' => 3];
        
        if ($engagementLevels[$recentEngagement] > $engagementLevels[$olderEngagement]) {
            return 'increasing';
        } elseif ($engagementLevels[$recentEngagement] < $engagementLevels[$olderEngagement]) {
            return 'decreasing';
        }
        
        return 'stable';
    }

    private function calculateMoodStability(array $files): string
    {
        if (count($files) < 3) return 'stable';
        
        $moods = array_column(array_column($files, 'analysis'), 'mood');
        $uniqueMoods = array_unique($moods);
        
        return count($uniqueMoods) <= 2 ? 'stable' : 'variable';
    }

    private function calculateClarityConsistency(array $files): string
    {
        if (empty($files)) return 'good';
        
        $clarities = array_filter(array_column(array_column($files, 'analysis'), 'clarity'));
        $excellentCount = count(array_filter($clarities, fn($c) => $c === 'excellent'));
        
        return $excellentCount / count($clarities) >= 0.7 ? 'excellent' : 'good';
    }

    private function calculateMedicationAdherence(array $files): string
    {
        if (empty($files)) return 'good';
        
        $adherences = array_filter(array_column(array_column($files, 'analysis'), 'medication_compliance'));
        $excellentCount = count(array_filter($adherences, fn($a) => $a === 'excellent'));
        
        return $excellentCount / count($adherences) >= 0.8 ? 'excellent' : 'good';
    }

    private function calculateSocialWellness(array $files): string
    {
        if (empty($files)) return 'moderate';
        
        $connections = array_filter(array_column(array_column($files, 'analysis'), 'social_connection'));
        $strongCount = count(array_filter($connections, fn($c) => $c === 'strong'));
        
        return $strongCount / count($connections) >= 0.7 ? 'strong' : 'moderate';
    }

    private function calculateMobilityAssessment(array $files): string
    {
        if (empty($files)) return 'good';
        
        $mobilities = array_filter(array_column(array_column($files, 'analysis'), 'mobility'));
        $goodCount = count(array_filter($mobilities, fn($m) => in_array($m, ['good', 'excellent'])));
        
        return $goodCount / count($mobilities) >= 0.7 ? 'good' : 'fair';
    }

    private function calculatePainManagement(array $files): string
    {
        if (empty($files)) return 'effective';
        
        $painLevels = array_filter(array_column(array_column($files, 'analysis'), 'pain_level'));
        $lowPainCount = count(array_filter($painLevels, fn($p) => in_array($p, ['none', 'mild'])));
        
        return $lowPainCount / count($painLevels) >= 0.8 ? 'effective' : 'moderate';
    }

    private function calculateIndependenceLevel(array $files): string
    {
        if (empty($files)) return 'good';
        
        $independences = array_filter(array_column(array_column($files, 'analysis'), 'independence'));
        $goodCount = count(array_filter($independences, fn($i) => in_array($i, ['good', 'excellent'])));
        
        return $goodCount / count($independences) >= 0.7 ? 'good' : 'limited';
    }
}
