<?php

namespace App\Console\Commands;

use App\Http\Services\PushNotificationService;
use App\Models\PushNotificatonSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Helpers\FirebasePushNotification;
use App\Models\PushNotification;
use Illuminate\Support\Facades\DB;

class FirebasePushNotifications extends Command
{
    protected $firebasePushNotification;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:pushnoti';

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

        $pushNotifications = PushNotification::all();
        foreach ($pushNotifications as $pushNotification) {
            try {
                $topic=config('firebase.key')['topic'];
                $response =$this->firebasePushNotification->send(
                    [
                        "message" => [
                            "topic" => "$topic",
                            "data" => [
                                'id' => (string) $pushNotification->id,
                                'title' => $pushNotification->title,
                                'body' => $pushNotification->message,
                                // 'link' => $pushNotification->url,
                                // 'image' => web_asset_url($pushNotification->notification_thumbnail_desktop),
                                // 'cta_text' => $pushNotification->cta_text ?? '',
                                // 'cta_link' => $pushNotification->cta_link ?? ''
                            ]
                        ]
                    ]
                );

                
                $pushNotification->total_sent += 1;
                $pushNotification->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
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
        $this->info('Push notificaton send: ' . now());
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
