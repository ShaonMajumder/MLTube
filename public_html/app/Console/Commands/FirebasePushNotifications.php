<?php

namespace App\Console\Commands;

use App\Http\Services\PushNotificationService;
use App\Models\PushNotificatonSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Helpers\FirebasePushNotification;
use App\Models\PushNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FirebasePushNotifications extends Command
{
    protected $firebasePushNotification;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pushnoti:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notification from firebase';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FirebasePushNotification $firebasePushNotification)
    {
        parent::__construct();
        $this->firebasePushNotification = $firebasePushNotification;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::transaction(function () {
                $this->processPushNotifications();
            });

        } catch (Exception $e) {
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

        
        $this->info('Push notificaton send: ' . now());
    }

    private function processPushNotifications()
    {
        $pushNotifications = PushNotification::all();
        $topic = config('firebase.key')['topic'];

        foreach ($pushNotifications as $pushNotification) {
            $this->sendNotification($pushNotification, $topic);
            $this->updateNotificationCount($pushNotification);
        }
    }

    private function sendNotification(PushNotification $pushNotification, string $topic)
    {
        $data = [
            "message" => [
                "topic" => $topic,
                "data" => [
                    'id' => (string) $pushNotification->id,
                    'title' => $pushNotification->title,
                    'body' => $pushNotification->message
                ]
            ]
        ];
        $response = $this->firebasePushNotification->send($data);
        
        Log::channel('elasticsearch')->info('Sending Push Notification', [
            'index' =>  config('elasticsearch.indices.push_notifications'),
            'notification' => $data,
            'response' => $response,
        ]);
    }

    private function updateNotificationCount(PushNotification $pushNotification)
    {
        $pushNotification->increment('total_sent');
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
}
