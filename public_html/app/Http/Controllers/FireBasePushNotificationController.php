<?php declare(strict_types=1);


namespace App\Http\Controllers;

use App\Helpers\FirebasePushNotification;
use App\Http\Services\TestService;
use Exception;

class FireBasePushNotificationController
{
    private TestService $testService;
    private $testDeviceToken;

    public function __construct(TestService $testService=null)
    {
        // $this->testService = $testService;
        $this->testDeviceToken = "db9iV9AfEe2DP9wCmamvWy:APA91bEXVBmnvMMA53GsFm6_szSPSIrX4ka6PgxgnrvaXOaJ3vk9KMtfIkyiEjaA2rDH2e9Yklf7eLnHUmVoArrYA8AJUnP1Fts1nd1qX2XvlQdluHjHAWuyexa806H7s1a6CkFAtBgY";
    }

    public function sendSingle(){
        try{
            $deviceToken = $this->testDeviceToken;
            // Load the service account JSON
            $serviceAccountKeyJSONFillePath = $_SERVER['DOCUMENT_ROOT'].'/../' . env('GOOGLE_APPLICATION_CREDENTIALS');
            $firebaseAdmin = new FirebasePushNotification($serviceAccountKeyJSONFillePath);
            $response = $firebaseAdmin->send(
                [
                    'message' => [
                        'token' => $deviceToken,
                        'data' => [
                            'title' => 'You balance is low. Get Emergency Advance Data 500 MB.',
                            'body' => 'You need to recharge immediately.',
                            'link' => 'http://mytm.link/4049',
                            'banner' => 'https://example.com/banner.png',
                            'isSave' => 'true',
                            'icon' => 'https://example.com/banner.png'
                        ]
                    ]
                ]
            );
    
            return $response;
        } catch(Exception $e){
            // Extract raw response from the exception message
            $message = $e->getMessage();
            $errorData = $this->extractErrorData($message);

            return [
                'success' => false,
                'error' => [
                    'message' => 'Error making cURL request: ' . $errorData['message'],
                    'details' => $errorData['details']
                ]
            ];
        }
    }

    /**
     * Extract detailed error information from the exception message.
     *
     * @param string $message The raw exception message.
     * @return array An array containing a user-friendly error message and detailed error data.
     */
    private function extractErrorData(string $message): array
    {
        // Separate the main error message from the detailed response
        $parts = explode('Error making cURL request: ', $message, 2);
        
        $userMessage = $parts[0] ?? 'Unknown error';
        $details = isset($parts[1]) ? json_decode($parts[1], true) : [];

        // Check if the details are valid JSON and extract them
        if (json_last_error() === JSON_ERROR_NONE) {
            return [
                'message' => $userMessage,
                'details' => $details
            ];
        } else {
            return [
                'message' => $userMessage,
                'details' => $message
            ];
        }
    }

    public function sendMultiple(){

    }

    public function sendToTopic($topic){
        
        try{
            $serviceAccountKeyJSONFillePath = $_SERVER['DOCUMENT_ROOT'].'/../' . env('GOOGLE_APPLICATION_CREDENTIALS');
            $firebaseAdmin = new FirebasePushNotification($serviceAccountKeyJSONFillePath);
            $response = $firebaseAdmin->send(
                [
                    "message" => [
                        "topic" => "$topic",
                        "data" => [
                            "title" => "You balance is low. Get Emergency Advance Data 500 MB.",
                            "body" => "You need to recharge immediately.",
                            "link" => "http://mytm.link/4049",
                            "banner" => "https://example.com/banner.png",
                            "isSave" => "true",
                            "icon" => "https://example.com/banner.png"
                        ]
                    ]
                ]
            );

            return $response;
        } catch(Exception $e){
            dd($e->getMessage());
        }
        
    }

    public function subscribeToTopic($topic){
        try{
            $deviceTokens = [
                $this->testDeviceToken,
                'token2'
            ];
        
            $serviceAccountKeyJSONFillePath = $_SERVER['DOCUMENT_ROOT'].'/../' . env('GOOGLE_APPLICATION_CREDENTIALS');
            $firebaseAdmin = new FirebasePushNotification($serviceAccountKeyJSONFillePath);
            return $firebaseAdmin->subscribeToTopic($topic,$deviceTokens);
        } catch(Exception $e){
            dd($e->getMessage());
        }
    }

    public function unsubscribeToTopic($topic){
        try{
            $deviceTokens = [
                $this->testDeviceToken,
                'token2'
            ];
        
            $serviceAccountKeyJSONFillePath = $_SERVER['DOCUMENT_ROOT'].'/../' . env('GOOGLE_APPLICATION_CREDENTIALS');
            $firebaseAdmin = new FirebasePushNotification($serviceAccountKeyJSONFillePath);
            return $firebaseAdmin->subscribeToTopic($topic,$deviceTokens,false);
        } catch(Exception $e){
            dd($e->getMessage());
        }
    }
}

