<?php

namespace App\Services;

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class DigitalOceanSpacesService
{
    private $s3Client;
    private $bucketName;
    private $basePath;

    public function __construct()
    {
        $this->bucketName = config('services.digitalocean.bucket_name', 'hazel-audio-clips');
        $this->basePath = config('services.digitalocean.base_path', 'livekit/audio_transcripts');
        
        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => config('services.digitalocean.region', 'nyc3'),
            'endpoint' => config('services.digitalocean.endpoint', 'https://nyc3.digitaloceanspaces.com'),
            'credentials' => [
                'key' => config('services.digitalocean.key'),
                'secret' => config('services.digitalocean.secret'),
            ],
            'use_path_style_endpoint' => true,
        ]);
    }

    /**
     * Get stats JSON files for a specific profile and account
     *
     * @param int $profileId
     * @param int $accountId
     * @return array
     */
    public function getStatsForProfile(int $profileId, int $accountId): array
    {
        try {
            $path = $this->buildPath($profileId, $accountId);
            $stats = [];

            // List all objects in the directory
            $objects = $this->s3Client->listObjectsV2([
                'Bucket' => $this->bucketName,
                'Prefix' => $path,
            ]);

            if (!isset($objects['Contents'])) {
                Log::info("No files found for profile {$profileId}, account {$accountId}");
                return [];
            }

            foreach ($objects['Contents'] as $object) {
                $key = $object['Key'];
                
                // Only process JSON files that contain stats/analysis
                if ($this->isStatsJsonFile($key)) {
                    $statsData = $this->getFileContent($key);
                    if ($statsData) {
                        $stats[] = [
                            'filename' => basename($key),
                            'path' => $key,
                            'last_modified' => $object['LastModified']->format('Y-m-d H:i:s'),
                            'size' => $object['Size'],
                            'data' => $statsData
                        ];
                    }
                }
            }

            return $stats;

        } catch (AwsException $e) {
            Log::error('DigitalOcean Spaces error: ' . $e->getMessage(), [
                'profile_id' => $profileId,
                'account_id' => $accountId,
                'error_code' => $e->getAwsErrorCode()
            ]);
            
            return [];
        } catch (\Exception $e) {
            Log::error('General error accessing DigitalOcean Spaces: ' . $e->getMessage(), [
                'profile_id' => $profileId,
                'account_id' => $accountId
            ]);
            
            return [];
        }
    }

    /**
     * Get a specific stats file for a profile and account
     *
     * @param int $profileId
     * @param int $accountId
     * @param string $filename
     * @return array|null
     */
    public function getSpecificStatsFile(int $profileId, int $accountId, string $filename): ?array
    {
        try {
            $path = $this->buildPath($profileId, $accountId);
            $key = $path . '/' . $filename;

            $statsData = $this->getFileContent($key);
            
            if ($statsData) {
                return [
                    'filename' => $filename,
                    'path' => $key,
                    'data' => $statsData
                ];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error getting specific stats file: ' . $e->getMessage(), [
                'profile_id' => $profileId,
                'account_id' => $accountId,
                'filename' => $filename
            ]);
            
            return null;
        }
    }

    /**
     * Get aggregated stats summary for a profile and account
     *
     * @param int $profileId
     * @param int $accountId
     * @return array
     */
    public function getStatsSummary(int $profileId, int $accountId): array
    {
        $stats = $this->getStatsForProfile($profileId, $accountId);
        
        if (empty($stats)) {
            return [
                'total_files' => 0,
                'total_calls' => 0,
                'latest_call' => null,
                'summary' => []
            ];
        }

        $totalCalls = 0;
        $latestCall = null;
        $callSummaries = [];

        foreach ($stats as $stat) {
            if (isset($stat['data'])) {
                $totalCalls++;
                
                $callData = $stat['data'];
                
                // Track latest call
                if (!$latestCall || $stat['last_modified'] > $latestCall['timestamp']) {
                    $latestCall = [
                        'filename' => $stat['filename'],
                        'timestamp' => $stat['last_modified'],
                        'data' => $callData
                    ];
                }

                // Aggregate call summaries with canary analysis data
                $callSummaries[] = [
                    'filename' => $stat['filename'],
                    'timestamp' => $stat['last_modified'],
                    'duration' => $callData['duration'] ?? null,
                    'sentiment' => $callData['sentiment'] ?? null,
                    'topics' => $callData['topics'] ?? [],
                    'summary' => $callData['summary'] ?? null,
                    'analysis' => $callData['analysis'] ?? null,
                    'health_metrics' => $callData['health_metrics'] ?? null
                ];
            }
        }

        return [
            'total_files' => count($stats),
            'total_calls' => $totalCalls,
            'latest_call' => $latestCall,
            'summary' => $callSummaries
        ];
    }

    /**
     * Build the path for the given profile and account IDs
     * Structure: livekit/audio_transcripts/{accountId}/{profileId}/
     *
     * @param int $profileId
     * @param int $accountId
     * @return string
     */
    private function buildPath(int $profileId, int $accountId): string
    {
        return "{$this->basePath}/{$accountId}/{$profileId}";
    }

    /**
     * Check if the file is a canary analysis JSON file
     *
     * @param string $key
     * @return bool
     */
    private function isStatsJsonFile(string $key): bool
    {
        // Look for files that end with .ogg.json (canary analysis results)
        return str_ends_with($key, '.ogg.json');
    }

    /**
     * Get the content of a file from DigitalOcean Spaces
     *
     * @param string $key
     * @return array|null
     */
    private function getFileContent(string $key): ?array
    {
        try {
            $result = $this->s3Client->getObject([
                'Bucket' => $this->bucketName,
                'Key' => $key,
            ]);

            $content = $result['Body']->getContents();
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('Invalid JSON in file: ' . $key, [
                    'json_error' => json_last_error_msg()
                ]);
                return null;
            }

            return $data;

        } catch (AwsException $e) {
            if ($e->getAwsErrorCode() === 'NoSuchKey') {
                Log::info('File not found: ' . $key);
                return null;
            }
            
            Log::error('Error getting file content: ' . $e->getMessage(), [
                'key' => $key,
                'error_code' => $e->getAwsErrorCode()
            ]);
            
            return null;
        }
    }

    /**
     * Verify if a path exists for the given profile and account IDs
     *
     * @param int $profileId
     * @param int $accountId
     * @return bool
     */
    public function verifyPath(int $profileId, int $accountId): bool
    {
        try {
            $path = $this->buildPath($profileId, $accountId);
            
            $objects = $this->s3Client->listObjectsV2([
                'Bucket' => $this->bucketName,
                'Prefix' => $path,
                'MaxKeys' => 1,
            ]);

            return isset($objects['Contents']) && count($objects['Contents']) > 0;

        } catch (\Exception $e) {
            Log::error('Error verifying path: ' . $e->getMessage(), [
                'profile_id' => $profileId,
                'account_id' => $accountId
            ]);
            
            return false;
        }
    }
}
