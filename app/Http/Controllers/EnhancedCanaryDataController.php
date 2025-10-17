<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EnhancedCanaryDataController extends Controller
{
    /**
     * Get comprehensive enhanced canary analysis data for Account ID 7 (microassetsmain@gmail.com)
     * Path: livekit/audio_transcripts/7/-1/
     * Includes all health conditions: Alzheimer's, Parkinson's, Depression, etc.
     */
    public function getAccount7EnhancedData(): JsonResponse
    {
        $data = [
            'account_info' => [
                'account_id' => 7,
                'profile_id' => -1,
                'email' => 'microassetsmain@gmail.com',
                'path' => 'livekit/audio_transcripts/7/-1/',
                'file_structure' => 'Direct account holder (profile_id = -1)',
                'health_profile' => 'Professional adult with moderate stress levels, good overall health'
            ],
            'canary_analysis_files' => [
                [
                    'filename' => '20250115_100000_789012.ogg.json',
                    'file_type' => 'canary_analysis',
                    'path' => 'livekit/audio_transcripts/7/-1/20250115_100000_789012.ogg.json',
                    'last_modified' => '2025-01-15T10:05:00Z',
                    'size' => 2956,
                    'canary_data' => [
                        'call_id' => '20250115_100000_789012',
                        'duration' => 320,
                        'sentiment' => 'neutral',
                        'confidence_score' => 0.82,
                        'topics' => ['work', 'stress', 'health', 'exercise', 'nutrition'],
                        'summary' => 'Professional discussing work-related stress, exercise routine, and nutrition habits. Shows good self-awareness of health management.',
                        'analysis' => [
                            // Basic Health Metrics
                            'mood' => 'focused',
                            'engagement' => 'moderate',
                            'clarity' => 'good',
                            'energy_level' => 'moderate',
                            'sleep_quality' => 'fair',
                            'appetite' => 'good',
                            'mobility' => 'excellent',
                            'memory' => 'sharp',
                            'independence' => 'excellent',
                            'social_connection' => 'moderate',
                            'medication_compliance' => 'excellent',
                            'pain_level' => 'low',
                            'balance' => 'excellent',
                            'sentiment' => 'neutral',
                            'duration' => 320,
                            'topics' => ['work', 'stress', 'health', 'exercise', 'nutrition'],
                            'total_calls' => 12,
                            'total_duration' => 3840,
                            'average_sentiment' => 'neutral',
                            'most_common_topics' => ['work', 'health', 'exercise'],
                            'last_call' => '2025-01-15T10:00:00Z',
                            'health_score' => 78,
                            'engagement_trend' => 'stable',
                            'mood_stability' => 'good',
                            'clarity_consistency' => 'excellent',
                            'medication_adherence' => 'excellent',
                            'social_wellness' => 'moderate',
                            'mobility_assessment' => 'excellent',
                            'pain_management' => 'effective',
                            'independence_level' => 'excellent',
                            'confidence_score' => 0.82,
                            'conversation_quality' => 'good',
                            'cognitive_indicators' => 'sharp',
                            'speech_rate_trend' => 'normal',
                            'confusion_episodes' => 'none',
                            'repetition_patterns' => 'minimal'
                        ],
                        'health_conditions' => [
                            'alzheimer' => [
                                'risk_score' => 1.2,
                                'risk_level' => 'low',
                                'indicators' => ['memory_sharp', 'cognitive_clarity'],
                                'recommendations' => ['continue_current_routine', 'maintain_social_connections']
                            ],
                            'parkinson' => [
                                'risk_score' => 1.0,
                                'risk_level' => 'low',
                                'indicators' => ['excellent_mobility', 'good_balance'],
                                'recommendations' => ['continue_exercise', 'monitor_movement']
                            ],
                            'depression' => [
                                'risk_score' => 2.1,
                                'risk_level' => 'low',
                                'indicators' => ['work_stress', 'moderate_mood'],
                                'recommendations' => ['stress_management', 'work_life_balance']
                            ],
                            'anxiety' => [
                                'risk_score' => 2.3,
                                'risk_level' => 'low',
                                'indicators' => ['work_pressure', 'moderate_engagement'],
                                'recommendations' => ['relaxation_techniques', 'time_management']
                            ],
                            'diabetes' => [
                                'risk_score' => 1.5,
                                'risk_level' => 'low',
                                'indicators' => ['good_nutrition', 'regular_exercise'],
                                'recommendations' => ['maintain_diet', 'continue_exercise']
                            ],
                            'hypertension' => [
                                'risk_score' => 1.8,
                                'risk_level' => 'low',
                                'indicators' => ['stress_management_needed', 'good_physical_activity'],
                                'recommendations' => ['stress_reduction', 'regular_monitoring']
                            ],
                            'arthritis' => [
                                'risk_score' => 1.0,
                                'risk_level' => 'low',
                                'indicators' => ['excellent_mobility', 'no_pain_reported'],
                                'recommendations' => ['maintain_activity', 'joint_health']
                            ]
                        ]
                    ]
                ]
            ],
            'aggregated_health_summary' => [
                'overall_health_score' => 78,
                'cognitive_health_score' => 85,
                'mental_health_score' => 72,
                'physical_health_score' => 88,
                'social_health_score' => 65,
                'alzheimer_risk_score' => 1.2,
                'parkinson_risk_score' => 1.0,
                'depression_risk_score' => 2.1,
                'anxiety_risk_score' => 2.3,
                'diabetes_risk_score' => 1.5,
                'hypertension_risk_score' => 1.8,
                'arthritis_risk_score' => 1.0,
                'fall_risk_score' => 1.5,
                'cognitive_risk_score' => 1.2,
                'total_calls' => 12,
                'total_duration' => 3840,
                'average_call_duration' => 320,
                'last_analysis_date' => '2025-01-15T10:05:00Z',
                'health_trend' => 'stable',
                'risk_trend' => 'low',
                'engagement_level' => 'moderate',
                'wellness_score' => 78,
                'mood_score' => 72,
                'energy_score' => 75,
                'depression_risk' => 2.1,
                'anxiety_risk' => 2.3,
                'parkinson_risk' => 1.0,
                'alzheimer_risk' => 1.2
            ]
        ];

        return response()->json($data);
    }

    /**
     * Get comprehensive enhanced canary analysis data for Account ID 6 (mtaimoorhas1@gmail.com)
     * Path: livekit/audio_transcripts/6/-1/
     * Includes all health conditions: Alzheimer's, Parkinson's, Depression, etc.
     */
    public function getAccount6EnhancedData(): JsonResponse
    {
        $data = [
            'account_info' => [
                'account_id' => 6,
                'profile_id' => -1,
                'email' => 'mtaimoorhas1@gmail.com',
                'path' => 'livekit/audio_transcripts/6/-1/',
                'file_structure' => 'Direct account holder (profile_id = -1)',
                'health_profile' => 'Adult with good overall health, some age-related concerns'
            ],
            'canary_analysis_files' => [
                [
                    'filename' => '20250115_090000_123456.ogg.json',
                    'file_type' => 'canary_analysis',
                    'path' => 'livekit/audio_transcripts/6/-1/20250115_090000_123456.ogg.json',
                    'last_modified' => '2025-01-15T09:05:00Z',
                    'size' => 2847,
                    'canary_data' => [
                        'call_id' => '20250115_090000_123456',
                        'duration' => 245,
                        'sentiment' => 'positive',
                        'confidence_score' => 0.87,
                        'topics' => ['health', 'medication', 'family', 'daily_routine', 'memory_concerns'],
                        'summary' => 'Morning health check-in discussing medication adherence, family updates, and recent memory lapses. User appears energetic but mentions occasional forgetfulness.',
                        'analysis' => [
                            // Basic Health Metrics
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
                            'balance' => 'stable',
                            
                            // Cognitive Health
                            'cognitive_function' => 'good',
                            'memory_recall' => 'slight_difficulty',
                            'attention_span' => 'normal',
                            'problem_solving' => 'good',
                            'language_fluency' => 'excellent',
                            'orientation' => 'excellent',
                            'executive_function' => 'good',
                            
                            // Mental Health
                            'depression_indicators' => 'minimal',
                            'anxiety_level' => 'low',
                            'stress_level' => 'moderate',
                            'emotional_stability' => 'stable',
                            'social_withdrawal' => 'none',
                            'interest_in_activities' => 'high',
                            'self_worth' => 'positive',
                            
                            // Neurological Health
                            'tremors' => 'none',
                            'rigidity' => 'none',
                            'bradykinesia' => 'none',
                            'postural_instability' => 'none',
                            'gait_abnormalities' => 'none',
                            'facial_expression' => 'normal',
                            'speech_clarity' => 'excellent',
                            
                            // Cardiovascular Health
                            'heart_rate' => 'normal',
                            'blood_pressure_concerns' => 'mild',
                            'chest_pain' => 'none',
                            'shortness_of_breath' => 'mild',
                            'fatigue_level' => 'moderate',
                            
                            // Metabolic Health
                            'diabetes_indicators' => 'none',
                            'weight_changes' => 'stable',
                            'thirst_level' => 'normal',
                            'urination_frequency' => 'normal',
                            'blood_sugar_concerns' => 'none',
                            
                            // Sensory Health
                            'hearing' => 'good',
                            'vision' => 'fair',
                            'taste' => 'normal',
                            'smell' => 'good',
                            'touch_sensitivity' => 'normal',
                            
                            // Gastrointestinal Health
                            'digestive_issues' => 'mild',
                            'nausea' => 'none',
                            'appetite_changes' => 'none',
                            'bowel_movements' => 'regular',
                            
                            // Musculoskeletal Health
                            'joint_pain' => 'mild',
                            'muscle_strength' => 'good',
                            'flexibility' => 'fair',
                            'bone_health' => 'good',
                            'arthritis_indicators' => 'mild',
                            
                            // Skin Health
                            'skin_condition' => 'good',
                            'wound_healing' => 'normal',
                            'bruising' => 'minimal',
                            
                            // Sleep Health
                            'sleep_duration' => '7_hours',
                            'sleep_quality' => 'fair',
                            'sleep_apnea_indicators' => 'none',
                            'insomnia' => 'mild',
                            'restless_legs' => 'none',
                            
                            // Medication & Treatment
                            'medication_side_effects' => 'minimal',
                            'treatment_adherence' => 'excellent',
                            'therapy_attendance' => 'regular',
                            'lifestyle_modifications' => 'good',
                            
                            // Risk Factors
                            'fall_risk' => 'low',
                            'infection_risk' => 'low',
                            'dehydration_risk' => 'low',
                            'malnutrition_risk' => 'low'
                        ],
                        'health_metrics' => [
                            'overall_health_score' => 8.5,
                            'cognitive_health_score' => 8.0,
                            'mental_health_score' => 8.8,
                            'physical_health_score' => 8.2,
                            'social_health_score' => 9.0,
                            'engagement_trend' => 'increasing',
                            'mood_stability' => 'stable',
                            'clarity_consistency' => 'excellent',
                            'medication_adherence' => 'excellent',
                            'social_wellness' => 'strong',
                            'mobility_assessment' => 'good',
                            'pain_management' => 'effective',
                            'independence_level' => 'good',
                            'fall_risk_score' => 2.1,
                            'cognitive_risk_score' => 3.2,
                            'depression_risk_score' => 1.8,
                            'anxiety_risk_score' => 2.0,
                            'parkinson_risk_score' => 1.2,
                            'alzheimer_risk_score' => 3.8
                        ],
                        'conversation_insights' => [
                            'speech_rate' => 'normal',
                            'pauses' => 'minimal',
                            'repetition' => 'low',
                            'confusion_indicators' => 'none',
                            'emotional_tone' => 'positive',
                            'cognitive_load' => 'normal',
                            'word_finding_difficulty' => 'mild',
                            'memory_gaps' => 'occasional',
                            'topic_switching' => 'normal',
                            'conversation_flow' => 'smooth'
                        ],
                        'health_conditions' => [
                            'diagnosed_conditions' => [
                                'mild_hypertension',
                                'age_related_memory_concerns',
                                'mild_arthritis'
                            ],
                            'suspected_conditions' => [
                                'early_mild_cognitive_impairment'
                            ],
                            'risk_factors' => [
                                'family_history_dementia',
                                'age_over_65',
                                'hypertension'
                            ],
                            'monitored_conditions' => [
                                'memory_function',
                                'blood_pressure',
                                'joint_health'
                            ]
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_140000_789012.ogg.json',
                    'file_type' => 'canary_analysis',
                    'path' => 'livekit/audio_transcripts/6/-1/20250115_140000_789012.ogg.json',
                    'last_modified' => '2025-01-15T14:05:00Z',
                    'size' => 2134,
                    'canary_data' => [
                        'call_id' => '20250115_140000_789012',
                        'duration' => 180,
                        'sentiment' => 'neutral',
                        'confidence_score' => 0.82,
                        'topics' => ['weather', 'hobbies', 'exercise', 'nutrition', 'memory_techniques'],
                        'summary' => 'Afternoon conversation about daily activities, health habits, and memory improvement techniques. User discusses gentle exercise routine and meal planning.',
                        'analysis' => [
                            // Basic Health Metrics
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
                            'balance' => 'stable',
                            
                            // Cognitive Health
                            'cognitive_function' => 'good',
                            'memory_recall' => 'good',
                            'attention_span' => 'normal',
                            'problem_solving' => 'excellent',
                            'language_fluency' => 'excellent',
                            'orientation' => 'excellent',
                            'executive_function' => 'excellent',
                            
                            // Mental Health
                            'depression_indicators' => 'none',
                            'anxiety_level' => 'low',
                            'stress_level' => 'low',
                            'emotional_stability' => 'stable',
                            'social_withdrawal' => 'none',
                            'interest_in_activities' => 'high',
                            'self_worth' => 'positive',
                            
                            // Neurological Health
                            'tremors' => 'none',
                            'rigidity' => 'none',
                            'bradykinesia' => 'none',
                            'postural_instability' => 'none',
                            'gait_abnormalities' => 'none',
                            'facial_expression' => 'normal',
                            'speech_clarity' => 'excellent',
                            
                            // Cardiovascular Health
                            'heart_rate' => 'normal',
                            'blood_pressure_concerns' => 'none',
                            'chest_pain' => 'none',
                            'shortness_of_breath' => 'none',
                            'fatigue_level' => 'low',
                            
                            // Metabolic Health
                            'diabetes_indicators' => 'none',
                            'weight_changes' => 'stable',
                            'thirst_level' => 'normal',
                            'urination_frequency' => 'normal',
                            'blood_sugar_concerns' => 'none',
                            
                            // Sensory Health
                            'hearing' => 'good',
                            'vision' => 'good',
                            'taste' => 'normal',
                            'smell' => 'good',
                            'touch_sensitivity' => 'normal',
                            
                            // Gastrointestinal Health
                            'digestive_issues' => 'none',
                            'nausea' => 'none',
                            'appetite_changes' => 'none',
                            'bowel_movements' => 'regular',
                            
                            // Musculoskeletal Health
                            'joint_pain' => 'none',
                            'muscle_strength' => 'excellent',
                            'flexibility' => 'good',
                            'bone_health' => 'excellent',
                            'arthritis_indicators' => 'none',
                            
                            // Skin Health
                            'skin_condition' => 'excellent',
                            'wound_healing' => 'normal',
                            'bruising' => 'none',
                            
                            // Sleep Health
                            'sleep_duration' => '8_hours',
                            'sleep_quality' => 'good',
                            'sleep_apnea_indicators' => 'none',
                            'insomnia' => 'none',
                            'restless_legs' => 'none',
                            
                            // Medication & Treatment
                            'medication_side_effects' => 'none',
                            'treatment_adherence' => 'good',
                            'therapy_attendance' => 'regular',
                            'lifestyle_modifications' => 'excellent',
                            
                            // Risk Factors
                            'fall_risk' => 'very_low',
                            'infection_risk' => 'very_low',
                            'dehydration_risk' => 'very_low',
                            'malnutrition_risk' => 'very_low'
                        ],
                        'health_metrics' => [
                            'overall_health_score' => 8.2,
                            'cognitive_health_score' => 8.5,
                            'mental_health_score' => 9.0,
                            'physical_health_score' => 8.8,
                            'social_health_score' => 7.5,
                            'engagement_trend' => 'stable',
                            'mood_stability' => 'stable',
                            'clarity_consistency' => 'good',
                            'medication_adherence' => 'good',
                            'social_wellness' => 'moderate',
                            'mobility_assessment' => 'excellent',
                            'pain_management' => 'excellent',
                            'independence_level' => 'excellent',
                            'fall_risk_score' => 1.2,
                            'cognitive_risk_score' => 2.1,
                            'depression_risk_score' => 1.0,
                            'anxiety_risk_score' => 1.2,
                            'parkinson_risk_score' => 0.8,
                            'alzheimer_risk_score' => 2.5
                        ],
                        'conversation_insights' => [
                            'speech_rate' => 'slightly_slow',
                            'pauses' => 'moderate',
                            'repetition' => 'low',
                            'confusion_indicators' => 'none',
                            'emotional_tone' => 'neutral',
                            'cognitive_load' => 'normal',
                            'word_finding_difficulty' => 'none',
                            'memory_gaps' => 'none',
                            'topic_switching' => 'normal',
                            'conversation_flow' => 'smooth'
                        ],
                        'health_conditions' => [
                            'diagnosed_conditions' => [],
                            'suspected_conditions' => [],
                            'risk_factors' => [
                                'age_over_65',
                                'family_history_dementia'
                            ],
                            'monitored_conditions' => [
                                'memory_function',
                                'cognitive_health'
                            ]
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_190000_345678.ogg.json',
                    'file_type' => 'canary_analysis',
                    'path' => 'livekit/audio_transcripts/6/-1/20250115_190000_345678.ogg.json',
                    'last_modified' => '2025-01-15T19:05:00Z',
                    'size' => 2456,
                    'canary_data' => [
                        'call_id' => '20250115_190000_345678',
                        'duration' => 320,
                        'sentiment' => 'positive',
                        'confidence_score' => 0.91,
                        'topics' => ['family', 'memories', 'health_goals', 'gratitude', 'cognitive_exercises'],
                        'summary' => 'Evening reflection on family memories and health improvement goals. User expresses gratitude and discusses future wellness plans including cognitive exercises.',
                        'analysis' => [
                            // Basic Health Metrics
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
                            'balance' => 'stable',
                            
                            // Cognitive Health
                            'cognitive_function' => 'excellent',
                            'memory_recall' => 'excellent',
                            'attention_span' => 'normal',
                            'problem_solving' => 'excellent',
                            'language_fluency' => 'excellent',
                            'orientation' => 'excellent',
                            'executive_function' => 'excellent',
                            
                            // Mental Health
                            'depression_indicators' => 'none',
                            'anxiety_level' => 'very_low',
                            'stress_level' => 'low',
                            'emotional_stability' => 'very_stable',
                            'social_withdrawal' => 'none',
                            'interest_in_activities' => 'very_high',
                            'self_worth' => 'very_positive',
                            
                            // Neurological Health
                            'tremors' => 'none',
                            'rigidity' => 'none',
                            'bradykinesia' => 'none',
                            'postural_instability' => 'none',
                            'gait_abnormalities' => 'none',
                            'facial_expression' => 'normal',
                            'speech_clarity' => 'excellent',
                            
                            // Cardiovascular Health
                            'heart_rate' => 'normal',
                            'blood_pressure_concerns' => 'none',
                            'chest_pain' => 'none',
                            'shortness_of_breath' => 'none',
                            'fatigue_level' => 'low',
                            
                            // Metabolic Health
                            'diabetes_indicators' => 'none',
                            'weight_changes' => 'stable',
                            'thirst_level' => 'normal',
                            'urination_frequency' => 'normal',
                            'blood_sugar_concerns' => 'none',
                            
                            // Sensory Health
                            'hearing' => 'good',
                            'vision' => 'good',
                            'taste' => 'normal',
                            'smell' => 'good',
                            'touch_sensitivity' => 'normal',
                            
                            // Gastrointestinal Health
                            'digestive_issues' => 'none',
                            'nausea' => 'none',
                            'appetite_changes' => 'none',
                            'bowel_movements' => 'regular',
                            
                            // Musculoskeletal Health
                            'joint_pain' => 'none',
                            'muscle_strength' => 'good',
                            'flexibility' => 'good',
                            'bone_health' => 'good',
                            'arthritis_indicators' => 'none',
                            
                            // Skin Health
                            'skin_condition' => 'good',
                            'wound_healing' => 'normal',
                            'bruising' => 'none',
                            
                            // Sleep Health
                            'sleep_duration' => '7.5_hours',
                            'sleep_quality' => 'excellent',
                            'sleep_apnea_indicators' => 'none',
                            'insomnia' => 'none',
                            'restless_legs' => 'none',
                            
                            // Medication & Treatment
                            'medication_side_effects' => 'none',
                            'treatment_adherence' => 'excellent',
                            'therapy_attendance' => 'regular',
                            'lifestyle_modifications' => 'excellent',
                            
                            // Risk Factors
                            'fall_risk' => 'low',
                            'infection_risk' => 'low',
                            'dehydration_risk' => 'low',
                            'malnutrition_risk' => 'low'
                        ],
                        'health_metrics' => [
                            'overall_health_score' => 9.1,
                            'cognitive_health_score' => 9.0,
                            'mental_health_score' => 9.5,
                            'physical_health_score' => 8.8,
                            'social_health_score' => 9.2,
                            'engagement_trend' => 'increasing',
                            'mood_stability' => 'stable',
                            'clarity_consistency' => 'excellent',
                            'medication_adherence' => 'excellent',
                            'social_wellness' => 'strong',
                            'mobility_assessment' => 'good',
                            'pain_management' => 'excellent',
                            'independence_level' => 'good',
                            'fall_risk_score' => 1.8,
                            'cognitive_risk_score' => 1.5,
                            'depression_risk_score' => 0.8,
                            'anxiety_risk_score' => 0.9,
                            'parkinson_risk_score' => 0.7,
                            'alzheimer_risk_score' => 2.1
                        ],
                        'conversation_insights' => [
                            'speech_rate' => 'normal',
                            'pauses' => 'minimal',
                            'repetition' => 'very_low',
                            'confusion_indicators' => 'none',
                            'emotional_tone' => 'positive',
                            'cognitive_load' => 'low',
                            'word_finding_difficulty' => 'none',
                            'memory_gaps' => 'none',
                            'topic_switching' => 'normal',
                            'conversation_flow' => 'excellent'
                        ],
                        'health_conditions' => [
                            'diagnosed_conditions' => [],
                            'suspected_conditions' => [],
                            'risk_factors' => [
                                'age_over_65',
                                'family_history_dementia'
                            ],
                            'monitored_conditions' => [
                                'memory_function',
                                'cognitive_health',
                                'emotional_wellbeing'
                            ]
                        ]
                    ]
                ]
            ],
            'aggregated_health_summary' => [
                'total_calls' => 15,
                'total_duration' => 2850,
                'average_sentiment' => 'positive',
                'sentiment_distribution' => ['positive' => 10, 'neutral' => 4, 'negative' => 1],
                'most_common_topics' => ['health', 'family', 'medication', 'daily_routine', 'memories', 'exercise', 'memory_concerns', 'cognitive_exercises'],
                'last_call' => '2025-01-15T19:00:00Z',
                
                // Overall Health Scores
                'overall_health_score' => 8.7,
                'cognitive_health_score' => 8.3,
                'mental_health_score' => 9.0,
                'physical_health_score' => 8.6,
                'social_health_score' => 8.6,
                
                // Health Trends
                'engagement_trend' => 'increasing',
                'mood_stability' => 'stable',
                'clarity_consistency' => 'excellent',
                'medication_adherence' => 'excellent',
                'social_wellness' => 'strong',
                'mobility_assessment' => 'good',
                'pain_management' => 'effective',
                'independence_level' => 'good',
                
                // Risk Assessment Scores
                'fall_risk_score' => 1.7,
                'cognitive_risk_score' => 2.3,
                'depression_risk_score' => 1.2,
                'anxiety_risk_score' => 1.4,
                'parkinson_risk_score' => 0.9,
                'alzheimer_risk_score' => 2.8,
                
                // Health Conditions Summary
                'diagnosed_conditions_count' => 3,
                'suspected_conditions_count' => 1,
                'risk_factors_count' => 3,
                'monitored_conditions_count' => 3,
                
                // Quality Metrics
                'average_confidence_score' => 0.87,
                'conversation_quality' => 'high',
                'data_completeness' => 'excellent'
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Enhanced comprehensive canary analysis data for Account ID 6 (mtaimoorhas1@gmail.com)',
            'data' => $data,
            'enhanced_features' => [
                'comprehensive_health_metrics' => [
                    'cognitive_health' => 'Memory, attention, problem-solving, language fluency',
                    'mental_health' => 'Depression, anxiety, stress, emotional stability',
                    'neurological_health' => 'Parkinson indicators, tremors, gait, speech',
                    'cardiovascular_health' => 'Heart rate, blood pressure, chest pain',
                    'metabolic_health' => 'Diabetes indicators, weight, blood sugar',
                    'sensory_health' => 'Hearing, vision, taste, smell, touch',
                    'gastrointestinal_health' => 'Digestive issues, nausea, appetite',
                    'musculoskeletal_health' => 'Joint pain, muscle strength, arthritis',
                    'skin_health' => 'Skin condition, wound healing',
                    'sleep_health' => 'Sleep quality, apnea, insomnia',
                    'medication_treatment' => 'Side effects, adherence, therapy',
                    'risk_assessment' => 'Fall risk, infection, dehydration, malnutrition'
                ],
                'health_conditions_tracking' => [
                    'alzheimer_indicators' => 'Memory recall, cognitive function, confusion',
                    'parkinson_indicators' => 'Tremors, rigidity, bradykinesia, gait',
                    'depression_indicators' => 'Mood, interest, self-worth, withdrawal',
                    'anxiety_indicators' => 'Stress level, emotional stability',
                    'diabetes_indicators' => 'Weight changes, thirst, urination',
                    'cardiovascular_indicators' => 'Blood pressure, chest pain, fatigue',
                    'arthritis_indicators' => 'Joint pain, stiffness, mobility'
                ],
                'risk_scoring_system' => [
                    'alzheimer_risk_score' => '0-10 scale based on cognitive indicators',
                    'parkinson_risk_score' => '0-10 scale based on motor symptoms',
                    'depression_risk_score' => '0-10 scale based on mood indicators',
                    'anxiety_risk_score' => '0-10 scale based on stress indicators',
                    'fall_risk_score' => '0-10 scale based on balance and mobility',
                    'cognitive_risk_score' => '0-10 scale based on cognitive function'
                ]
            ]
        ]);
    }

    /**
     * Get comprehensive enhanced canary analysis data for Profile ID 15 (jsahib@gmail.com)
     * Path: livekit/audio_transcripts/6/15/
     * Includes elderly-specific health conditions and comprehensive monitoring
     */
    public function getProfile15EnhancedData(): JsonResponse
    {
        $data = [
            'profile_info' => [
                'account_id' => 6,
                'profile_id' => 15,
                'email' => 'jsahib@gmail.com',
                'parent_account' => 'mtaimoorhas1@gmail.com',
                'path' => 'livekit/audio_transcripts/6/15/',
                'file_structure' => 'Elderly profile under Account ID 6',
                'health_profile' => 'Elderly with multiple health conditions requiring comprehensive monitoring'
            ],
            'canary_analysis_files' => [
                [
                    'filename' => '20250115_080000_111111.ogg.json',
                    'file_type' => 'canary_analysis',
                    'path' => 'livekit/audio_transcripts/6/15/20250115_080000_111111.ogg.json',
                    'last_modified' => '2025-01-15T08:05:00Z',
                    'size' => 2876,
                    'canary_data' => [
                        'call_id' => '20250115_080000_111111',
                        'duration' => 195,
                        'sentiment' => 'positive',
                        'confidence_score' => 0.84,
                        'topics' => ['health', 'medication', 'doctor_visit', 'family', 'memory_lapses'],
                        'summary' => 'Morning health check discussing recent doctor visit and medication changes. User reports feeling better after treatment adjustments but mentions increasing memory difficulties.',
                        'analysis' => [
                            // Basic Health Metrics
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
                            'balance' => 'unstable',
                            
                            // Cognitive Health
                            'cognitive_function' => 'fair',
                            'memory_recall' => 'difficulty',
                            'attention_span' => 'short',
                            'problem_solving' => 'fair',
                            'language_fluency' => 'good',
                            'orientation' => 'good',
                            'executive_function' => 'fair',
                            
                            // Mental Health
                            'depression_indicators' => 'mild',
                            'anxiety_level' => 'moderate',
                            'stress_level' => 'moderate',
                            'emotional_stability' => 'variable',
                            'social_withdrawal' => 'mild',
                            'interest_in_activities' => 'moderate',
                            'self_worth' => 'fair',
                            
                            // Neurological Health
                            'tremors' => 'mild',
                            'rigidity' => 'mild',
                            'bradykinesia' => 'mild',
                            'postural_instability' => 'moderate',
                            'gait_abnormalities' => 'mild',
                            'facial_expression' => 'reduced',
                            'speech_clarity' => 'good',
                            
                            // Cardiovascular Health
                            'heart_rate' => 'elevated',
                            'blood_pressure_concerns' => 'moderate',
                            'chest_pain' => 'mild',
                            'shortness_of_breath' => 'moderate',
                            'fatigue_level' => 'high',
                            
                            // Metabolic Health
                            'diabetes_indicators' => 'mild',
                            'weight_changes' => 'weight_loss',
                            'thirst_level' => 'increased',
                            'urination_frequency' => 'increased',
                            'blood_sugar_concerns' => 'mild',
                            
                            // Sensory Health
                            'hearing' => 'fair',
                            'vision' => 'poor',
                            'taste' => 'reduced',
                            'smell' => 'reduced',
                            'touch_sensitivity' => 'reduced',
                            
                            // Gastrointestinal Health
                            'digestive_issues' => 'moderate',
                            'nausea' => 'mild',
                            'appetite_changes' => 'reduced',
                            'bowel_movements' => 'irregular',
                            
                            // Musculoskeletal Health
                            'joint_pain' => 'moderate',
                            'muscle_strength' => 'poor',
                            'flexibility' => 'poor',
                            'bone_health' => 'fair',
                            'arthritis_indicators' => 'moderate',
                            
                            // Skin Health
                            'skin_condition' => 'fair',
                            'wound_healing' => 'slow',
                            'bruising' => 'frequent',
                            
                            // Sleep Health
                            'sleep_duration' => '6_hours',
                            'sleep_quality' => 'poor',
                            'sleep_apnea_indicators' => 'mild',
                            'insomnia' => 'moderate',
                            'restless_legs' => 'mild',
                            
                            // Medication & Treatment
                            'medication_side_effects' => 'moderate',
                            'treatment_adherence' => 'excellent',
                            'therapy_attendance' => 'regular',
                            'lifestyle_modifications' => 'limited',
                            
                            // Risk Factors
                            'fall_risk' => 'high',
                            'infection_risk' => 'moderate',
                            'dehydration_risk' => 'moderate',
                            'malnutrition_risk' => 'moderate'
                        ],
                        'health_metrics' => [
                            'overall_health_score' => 7.2,
                            'cognitive_health_score' => 6.8,
                            'mental_health_score' => 7.0,
                            'physical_health_score' => 6.5,
                            'social_health_score' => 8.0,
                            'engagement_trend' => 'stable',
                            'mood_stability' => 'variable',
                            'clarity_consistency' => 'good',
                            'medication_adherence' => 'excellent',
                            'social_wellness' => 'strong',
                            'mobility_assessment' => 'fair',
                            'pain_management' => 'effective',
                            'independence_level' => 'limited',
                            'fall_risk_score' => 6.8,
                            'cognitive_risk_score' => 7.2,
                            'depression_risk_score' => 5.8,
                            'anxiety_risk_score' => 6.2,
                            'parkinson_risk_score' => 5.5,
                            'alzheimer_risk_score' => 7.8
                        ],
                        'conversation_insights' => [
                            'speech_rate' => 'slow',
                            'pauses' => 'frequent',
                            'repetition' => 'moderate',
                            'confusion_indicators' => 'mild',
                            'emotional_tone' => 'positive',
                            'cognitive_load' => 'moderate',
                            'word_finding_difficulty' => 'moderate',
                            'memory_gaps' => 'frequent',
                            'topic_switching' => 'difficult',
                            'conversation_flow' => 'choppy'
                        ],
                        'health_conditions' => [
                            'diagnosed_conditions' => [
                                'mild_cognitive_impairment',
                                'parkinson_disease_early',
                                'type_2_diabetes',
                                'hypertension',
                                'osteoarthritis',
                                'mild_depression'
                            ],
                            'suspected_conditions' => [
                                'early_alzheimer_disease'
                            ],
                            'risk_factors' => [
                                'age_over_75',
                                'family_history_dementia',
                                'family_history_parkinson',
                                'diabetes',
                                'hypertension',
                                'previous_falls'
                            ],
                            'monitored_conditions' => [
                                'cognitive_function',
                                'motor_symptoms',
                                'blood_sugar',
                                'blood_pressure',
                                'mood',
                                'fall_risk'
                            ]
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_130000_222222.ogg.json',
                    'file_type' => 'canary_analysis',
                    'path' => 'livekit/audio_transcripts/6/15/20250115_130000_222222.ogg.json',
                    'last_modified' => '2025-01-15T13:05:00Z',
                    'size' => 2154,
                    'canary_data' => [
                        'call_id' => '20250115_130000_222222',
                        'duration' => 165,
                        'sentiment' => 'neutral',
                        'confidence_score' => 0.79,
                        'topics' => ['hobbies', 'weather', 'exercise', 'nutrition', 'medication_side_effects'],
                        'summary' => 'Afternoon conversation about daily activities and gentle exercise routine. User discusses challenges with mobility and medication side effects.',
                        'analysis' => [
                            // Basic Health Metrics
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
                            'balance' => 'unstable',
                            
                            // Cognitive Health
                            'cognitive_function' => 'poor',
                            'memory_recall' => 'poor',
                            'attention_span' => 'very_short',
                            'problem_solving' => 'poor',
                            'language_fluency' => 'fair',
                            'orientation' => 'fair',
                            'executive_function' => 'poor',
                            
                            // Mental Health
                            'depression_indicators' => 'moderate',
                            'anxiety_level' => 'high',
                            'stress_level' => 'high',
                            'emotional_stability' => 'unstable',
                            'social_withdrawal' => 'moderate',
                            'interest_in_activities' => 'low',
                            'self_worth' => 'poor',
                            
                            // Neurological Health
                            'tremors' => 'moderate',
                            'rigidity' => 'moderate',
                            'bradykinesia' => 'moderate',
                            'postural_instability' => 'severe',
                            'gait_abnormalities' => 'moderate',
                            'facial_expression' => 'masked',
                            'speech_clarity' => 'fair',
                            
                            // Cardiovascular Health
                            'heart_rate' => 'irregular',
                            'blood_pressure_concerns' => 'severe',
                            'chest_pain' => 'mild',
                            'shortness_of_breath' => 'severe',
                            'fatigue_level' => 'very_high',
                            
                            // Metabolic Health
                            'diabetes_indicators' => 'moderate',
                            'weight_changes' => 'significant_weight_loss',
                            'thirst_level' => 'very_increased',
                            'urination_frequency' => 'very_increased',
                            'blood_sugar_concerns' => 'moderate',
                            
                            // Sensory Health
                            'hearing' => 'poor',
                            'vision' => 'very_poor',
                            'taste' => 'very_reduced',
                            'smell' => 'very_reduced',
                            'touch_sensitivity' => 'very_reduced',
                            
                            // Gastrointestinal Health
                            'digestive_issues' => 'severe',
                            'nausea' => 'moderate',
                            'appetite_changes' => 'significantly_reduced',
                            'bowel_movements' => 'very_irregular',
                            
                            // Musculoskeletal Health
                            'joint_pain' => 'severe',
                            'muscle_strength' => 'very_poor',
                            'flexibility' => 'very_poor',
                            'bone_health' => 'poor',
                            'arthritis_indicators' => 'severe',
                            
                            // Skin Health
                            'skin_condition' => 'poor',
                            'wound_healing' => 'very_slow',
                            'bruising' => 'very_frequent',
                            
                            // Sleep Health
                            'sleep_duration' => '4_hours',
                            'sleep_quality' => 'very_poor',
                            'sleep_apnea_indicators' => 'moderate',
                            'insomnia' => 'severe',
                            'restless_legs' => 'moderate',
                            
                            // Medication & Treatment
                            'medication_side_effects' => 'severe',
                            'treatment_adherence' => 'good',
                            'therapy_attendance' => 'irregular',
                            'lifestyle_modifications' => 'very_limited',
                            
                            // Risk Factors
                            'fall_risk' => 'very_high',
                            'infection_risk' => 'high',
                            'dehydration_risk' => 'high',
                            'malnutrition_risk' => 'high'
                        ],
                        'health_metrics' => [
                            'overall_health_score' => 6.1,
                            'cognitive_health_score' => 5.2,
                            'mental_health_score' => 5.8,
                            'physical_health_score' => 5.5,
                            'social_health_score' => 6.5,
                            'engagement_trend' => 'stable',
                            'mood_stability' => 'variable',
                            'clarity_consistency' => 'fair',
                            'medication_adherence' => 'good',
                            'social_wellness' => 'moderate',
                            'mobility_assessment' => 'limited',
                            'pain_management' => 'moderate',
                            'independence_level' => 'limited',
                            'fall_risk_score' => 8.5,
                            'cognitive_risk_score' => 8.8,
                            'depression_risk_score' => 7.2,
                            'anxiety_risk_score' => 8.5,
                            'parkinson_risk_score' => 7.8,
                            'alzheimer_risk_score' => 8.9
                        ],
                        'conversation_insights' => [
                            'speech_rate' => 'very_slow',
                            'pauses' => 'very_frequent',
                            'repetition' => 'high',
                            'confusion_indicators' => 'moderate',
                            'emotional_tone' => 'neutral',
                            'cognitive_load' => 'high',
                            'word_finding_difficulty' => 'severe',
                            'memory_gaps' => 'very_frequent',
                            'topic_switching' => 'very_difficult',
                            'conversation_flow' => 'very_choppy'
                        ],
                        'health_conditions' => [
                            'diagnosed_conditions' => [
                                'moderate_cognitive_impairment',
                                'parkinson_disease_moderate',
                                'type_2_diabetes_poorly_controlled',
                                'severe_hypertension',
                                'severe_osteoarthritis',
                                'moderate_depression',
                                'chronic_fatigue_syndrome'
                            ],
                            'suspected_conditions' => [
                                'alzheimer_disease_moderate',
                                'heart_failure'
                            ],
                            'risk_factors' => [
                                'age_over_75',
                                'family_history_dementia',
                                'family_history_parkinson',
                                'poorly_controlled_diabetes',
                                'severe_hypertension',
                                'multiple_falls',
                                'polypharmacy'
                            ],
                            'monitored_conditions' => [
                                'cognitive_function',
                                'motor_symptoms',
                                'blood_sugar',
                                'blood_pressure',
                                'mood',
                                'fall_risk',
                                'heart_function',
                                'medication_side_effects'
                            ]
                        ]
                    ]
                ],
                [
                    'filename' => '20250115_180000_333333.ogg.json',
                    'file_type' => 'canary_analysis',
                    'path' => 'livekit/audio_transcripts/6/15/20250115_180000_333333.ogg.json',
                    'last_modified' => '2025-01-15T18:05:00Z',
                    'size' => 2387,
                    'canary_data' => [
                        'call_id' => '20250115_180000_333333',
                        'duration' => 280,
                        'sentiment' => 'positive',
                        'confidence_score' => 0.86,
                        'topics' => ['family', 'memories', 'health_concerns', 'gratitude', 'medication_improvements'],
                        'summary' => 'Evening conversation sharing family memories and expressing health concerns. User shows emotional connection and gratitude for support, mentions medication adjustments helping.',
                        'analysis' => [
                            // Basic Health Metrics
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
                            'balance' => 'stable',
                            
                            // Cognitive Health
                            'cognitive_function' => 'good',
                            'memory_recall' => 'good',
                            'attention_span' => 'normal',
                            'problem_solving' => 'good',
                            'language_fluency' => 'excellent',
                            'orientation' => 'excellent',
                            'executive_function' => 'good',
                            
                            // Mental Health
                            'depression_indicators' => 'minimal',
                            'anxiety_level' => 'low',
                            'stress_level' => 'low',
                            'emotional_stability' => 'stable',
                            'social_withdrawal' => 'none',
                            'interest_in_activities' => 'high',
                            'self_worth' => 'positive',
                            
                            // Neurological Health
                            'tremors' => 'mild',
                            'rigidity' => 'mild',
                            'bradykinesia' => 'mild',
                            'postural_instability' => 'mild',
                            'gait_abnormalities' => 'mild',
                            'facial_expression' => 'normal',
                            'speech_clarity' => 'excellent',
                            
                            // Cardiovascular Health
                            'heart_rate' => 'normal',
                            'blood_pressure_concerns' => 'mild',
                            'chest_pain' => 'none',
                            'shortness_of_breath' => 'mild',
                            'fatigue_level' => 'moderate',
                            
                            // Metabolic Health
                            'diabetes_indicators' => 'mild',
                            'weight_changes' => 'stable',
                            'thirst_level' => 'normal',
                            'urination_frequency' => 'normal',
                            'blood_sugar_concerns' => 'mild',
                            
                            // Sensory Health
                            'hearing' => 'fair',
                            'vision' => 'fair',
                            'taste' => 'reduced',
                            'smell' => 'reduced',
                            'touch_sensitivity' => 'normal',
                            
                            // Gastrointestinal Health
                            'digestive_issues' => 'mild',
                            'nausea' => 'none',
                            'appetite_changes' => 'none',
                            'bowel_movements' => 'regular',
                            
                            // Musculoskeletal Health
                            'joint_pain' => 'mild',
                            'muscle_strength' => 'fair',
                            'flexibility' => 'fair',
                            'bone_health' => 'fair',
                            'arthritis_indicators' => 'mild',
                            
                            // Skin Health
                            'skin_condition' => 'fair',
                            'wound_healing' => 'normal',
                            'bruising' => 'minimal',
                            
                            // Sleep Health
                            'sleep_duration' => '7_hours',
                            'sleep_quality' => 'excellent',
                            'sleep_apnea_indicators' => 'none',
                            'insomnia' => 'mild',
                            'restless_legs' => 'none',
                            
                            // Medication & Treatment
                            'medication_side_effects' => 'mild',
                            'treatment_adherence' => 'excellent',
                            'therapy_attendance' => 'regular',
                            'lifestyle_modifications' => 'good',
                            
                            // Risk Factors
                            'fall_risk' => 'moderate',
                            'infection_risk' => 'moderate',
                            'dehydration_risk' => 'low',
                            'malnutrition_risk' => 'low'
                        ],
                        'health_metrics' => [
                            'overall_health_score' => 8.3,
                            'cognitive_health_score' => 8.0,
                            'mental_health_score' => 8.8,
                            'physical_health_score' => 7.8,
                            'social_health_score' => 9.0,
                            'engagement_trend' => 'increasing',
                            'mood_stability' => 'stable',
                            'clarity_consistency' => 'excellent',
                            'medication_adherence' => 'excellent',
                            'social_wellness' => 'strong',
                            'mobility_assessment' => 'good',
                            'pain_management' => 'effective',
                            'independence_level' => 'good',
                            'fall_risk_score' => 4.2,
                            'cognitive_risk_score' => 4.8,
                            'depression_risk_score' => 2.8,
                            'anxiety_risk_score' => 3.2,
                            'parkinson_risk_score' => 4.5,
                            'alzheimer_risk_score' => 5.8
                        ],
                        'conversation_insights' => [
                            'speech_rate' => 'normal',
                            'pauses' => 'moderate',
                            'repetition' => 'low',
                            'confusion_indicators' => 'none',
                            'emotional_tone' => 'positive',
                            'cognitive_load' => 'normal',
                            'word_finding_difficulty' => 'mild',
                            'memory_gaps' => 'occasional',
                            'topic_switching' => 'normal',
                            'conversation_flow' => 'smooth'
                        ],
                        'health_conditions' => [
                            'diagnosed_conditions' => [
                                'mild_cognitive_impairment',
                                'parkinson_disease_mild',
                                'type_2_diabetes_well_controlled',
                                'mild_hypertension',
                                'mild_osteoarthritis'
                            ],
                            'suspected_conditions' => [],
                            'risk_factors' => [
                                'age_over_75',
                                'family_history_dementia',
                                'family_history_parkinson',
                                'diabetes',
                                'hypertension'
                            ],
                            'monitored_conditions' => [
                                'cognitive_function',
                                'motor_symptoms',
                                'blood_sugar',
                                'blood_pressure',
                                'mood',
                                'fall_risk'
                            ]
                        ]
                    ]
                ]
            ],
            'aggregated_health_summary' => [
                'total_calls' => 12,
                'total_duration' => 2100,
                'average_sentiment' => 'positive',
                'sentiment_distribution' => ['positive' => 8, 'neutral' => 3, 'negative' => 1],
                'most_common_topics' => ['health', 'medication', 'family', 'memories', 'doctor_visit', 'mobility', 'memory_lapses', 'medication_side_effects'],
                'last_call' => '2025-01-15T18:00:00Z',
                
                // Overall Health Scores
                'overall_health_score' => 7.2,
                'cognitive_health_score' => 6.7,
                'mental_health_score' => 7.2,
                'physical_health_score' => 6.6,
                'social_health_score' => 7.8,
                
                // Health Trends
                'engagement_trend' => 'stable',
                'mood_stability' => 'variable',
                'clarity_consistency' => 'good',
                'medication_adherence' => 'excellent',
                'social_wellness' => 'strong',
                'mobility_assessment' => 'fair',
                'pain_management' => 'effective',
                'independence_level' => 'limited',
                
                // Risk Assessment Scores
                'fall_risk_score' => 6.5,
                'cognitive_risk_score' => 7.0,
                'depression_risk_score' => 5.3,
                'anxiety_risk_score' => 6.0,
                'parkinson_risk_score' => 6.0,
                'alzheimer_risk_score' => 7.5,
                
                // Health Conditions Summary
                'diagnosed_conditions_count' => 6,
                'suspected_conditions_count' => 2,
                'risk_factors_count' => 7,
                'monitored_conditions_count' => 8,
                
                // Quality Metrics
                'average_confidence_score' => 0.83,
                'conversation_quality' => 'moderate',
                'data_completeness' => 'excellent',
                
                // Elderly-Specific Indicators
                'cognitive_indicators' => [
                    'speech_rate_trend' => 'variable',
                    'memory_consistency' => 'declining',
                    'confusion_episodes' => 'increasing',
                    'repetition_patterns' => 'moderate',
                    'word_finding_difficulty' => 'increasing'
                ],
                'mobility_indicators' => [
                    'fall_risk_trend' => 'increasing',
                    'balance_issues' => 'moderate',
                    'gait_abnormalities' => 'mild',
                    'postural_instability' => 'moderate'
                ],
                'medication_indicators' => [
                    'polypharmacy' => true,
                    'side_effects' => 'moderate',
                    'adherence_rate' => 'excellent',
                    'drug_interactions' => 'monitored'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Enhanced comprehensive canary analysis data for Profile ID 15 (jsahib@gmail.com)',
            'data' => $data,
            'elderly_profile_enhanced_features' => [
                'comprehensive_health_conditions' => [
                    'cognitive_disorders' => 'Alzheimer, MCI, dementia indicators',
                    'movement_disorders' => 'Parkinson tremors, rigidity, bradykinesia',
                    'mental_health' => 'Depression, anxiety, emotional stability',
                    'chronic_conditions' => 'Diabetes, hypertension, arthritis',
                    'age_related_concerns' => 'Vision, hearing, mobility, balance',
                    'medication_management' => 'Polypharmacy, side effects, adherence'
                ],
                'risk_assessment_comprehensive' => [
                    'alzheimer_risk_score' => '7.5/10 - High risk based on cognitive decline',
                    'parkinson_risk_score' => '6.0/10 - Moderate risk with motor symptoms',
                    'depression_risk_score' => '5.3/10 - Moderate risk with mood indicators',
                    'fall_risk_score' => '6.5/10 - High risk due to balance issues',
                    'cognitive_risk_score' => '7.0/10 - High risk with memory decline',
                    'medication_risk_score' => '4.2/10 - Moderate risk with polypharmacy'
                ],
                'elderly_specific_monitoring' => [
                    'cognitive_decline_tracking' => 'Memory, attention, executive function',
                    'motor_symptom_monitoring' => 'Tremors, gait, balance, rigidity',
                    'medication_effectiveness' => 'Side effects, adherence, interactions',
                    'quality_of_life_indicators' => 'Independence, social connection, pain',
                    'caregiver_burden_indicators' => 'Support needs, safety concerns'
                ]
            ]
        ]);
    }

    /**
     * Get health analytics formatted for enhanced dashboard display
     */
    public function getEnhancedHealthAnalytics(Request $request): JsonResponse
    {
        $profileId = $request->get('profile_id', -1);
        $accountId = $request->get('account_id', 6);
        $isElderlyProfile = $request->get('is_elderly_profile', false);

        if ($isElderlyProfile && $profileId == 15) {
            $response = $this->getProfile15EnhancedData();
            $data = $response->getData(true)['data'];
        } else {
            $response = $this->getAccount6EnhancedData();
            $data = $response->getData(true)['data'];
        }

        // Enhanced formatting for dashboard consumption
        $enhancedAnalytics = [
            'profile_id' => $profileId,
            'account_id' => $accountId,
            'is_elderly_profile' => $isElderlyProfile,
            
            // Basic Metrics
            'total_calls' => $data['aggregated_health_summary']['total_calls'],
            'total_duration' => $data['aggregated_health_summary']['total_duration'],
            'last_call' => $data['aggregated_health_summary']['last_call'],
            
            // Comprehensive Health Scores
            'overall_health_score' => $data['aggregated_health_summary']['overall_health_score'],
            'cognitive_health_score' => $data['aggregated_health_summary']['cognitive_health_score'],
            'mental_health_score' => $data['aggregated_health_summary']['mental_health_score'],
            'physical_health_score' => $data['aggregated_health_summary']['physical_health_score'],
            'social_health_score' => $data['aggregated_health_summary']['social_health_score'],
            
            // Risk Assessment
            'risk_scores' => [
                'alzheimer_risk' => $data['aggregated_health_summary']['alzheimer_risk_score'],
                'parkinson_risk' => $data['aggregated_health_summary']['parkinson_risk_score'],
                'depression_risk' => $data['aggregated_health_summary']['depression_risk_score'],
                'anxiety_risk' => $data['aggregated_health_summary']['anxiety_risk_score'],
                'fall_risk' => $data['aggregated_health_summary']['fall_risk_score'],
                'cognitive_risk' => $data['aggregated_health_summary']['cognitive_risk_score']
            ],
            
            // Health Conditions
            'health_conditions' => [
                'diagnosed_count' => $data['aggregated_health_summary']['diagnosed_conditions_count'],
                'suspected_count' => $data['aggregated_health_summary']['suspected_conditions_count'],
                'risk_factors_count' => $data['aggregated_health_summary']['risk_factors_count'],
                'monitored_count' => $data['aggregated_health_summary']['monitored_conditions_count']
            ],
            
            // Detailed Analytics
            'sentiment_distribution' => $data['aggregated_health_summary']['sentiment_distribution'],
            'most_common_topics' => $data['aggregated_health_summary']['most_common_topics'],
            'health_trends' => $this->calculateEnhancedHealthTrends($data['canary_analysis_files']),
            'condition_progression' => $this->calculateConditionProgression($data['canary_analysis_files']),
            'medication_analytics' => $this->calculateMedicationAnalytics($data['canary_analysis_files']),
            'risk_trends' => $this->calculateRiskTrends($data['canary_analysis_files'])
        ];

        return response()->json([
            'success' => true,
            'message' => 'Enhanced health analytics for comprehensive dashboard',
            'data' => $enhancedAnalytics,
            'source' => 'enhanced_canary_analysis',
            'comprehensive_metrics' => [
                'total_health_metrics' => 45,
                'total_risk_scores' => 6,
                'total_health_conditions' => 12,
                'total_conversation_insights' => 10
            ]
        ]);
    }

    private function calculateEnhancedHealthTrends(array $files): array
    {
        $trends = [];
        foreach ($files as $file) {
            $canaryData = $file['canary_data'];
            $trends[] = [
                'date' => $file['last_modified'],
                'overall_health_score' => $canaryData['health_metrics']['overall_health_score'],
                'cognitive_health_score' => $canaryData['health_metrics']['cognitive_health_score'],
                'mental_health_score' => $canaryData['health_metrics']['mental_health_score'],
                'physical_health_score' => $canaryData['health_metrics']['physical_health_score'],
                'social_health_score' => $canaryData['health_metrics']['social_health_score'],
                'alzheimer_risk_score' => $canaryData['health_metrics']['alzheimer_risk_score'],
                'parkinson_risk_score' => $canaryData['health_metrics']['parkinson_risk_score'],
                'depression_risk_score' => $canaryData['health_metrics']['depression_risk_score'],
                'confidence_score' => $canaryData['confidence_score']
            ];
        }
        return $trends;
    }

    private function calculateConditionProgression(array $files): array
    {
        $progression = [];
        foreach ($files as $file) {
            $canaryData = $file['canary_data'];
            $progression[] = [
                'date' => $file['last_modified'],
                'cognitive_function' => $canaryData['analysis']['cognitive_function'],
                'memory_recall' => $canaryData['analysis']['memory_recall'],
                'tremors' => $canaryData['analysis']['tremors'],
                'depression_indicators' => $canaryData['analysis']['depression_indicators'],
                'mobility' => $canaryData['analysis']['mobility'],
                'independence' => $canaryData['analysis']['independence']
            ];
        }
        return $progression;
    }

    private function calculateMedicationAnalytics(array $files): array
    {
        $analytics = [];
        foreach ($files as $file) {
            $canaryData = $file['canary_data'];
            $analytics[] = [
                'date' => $file['last_modified'],
                'medication_compliance' => $canaryData['analysis']['medication_compliance'],
                'medication_side_effects' => $canaryData['analysis']['medication_side_effects'],
                'treatment_adherence' => $canaryData['analysis']['treatment_adherence'],
                'therapy_attendance' => $canaryData['analysis']['therapy_attendance']
            ];
        }
        return $analytics;
    }

    private function calculateRiskTrends(array $files): array
    {
        $trends = [];
        foreach ($files as $file) {
            $canaryData = $file['canary_data'];
            $trends[] = [
                'date' => $file['last_modified'],
                'fall_risk' => $canaryData['analysis']['fall_risk'],
                'infection_risk' => $canaryData['analysis']['infection_risk'],
                'dehydration_risk' => $canaryData['analysis']['dehydration_risk'],
                'malnutrition_risk' => $canaryData['analysis']['malnutrition_risk'],
                'alzheimer_risk_score' => $canaryData['health_metrics']['alzheimer_risk_score'],
                'parkinson_risk_score' => $canaryData['health_metrics']['parkinson_risk_score']
            ];
        }
        return $trends;
    }
}
