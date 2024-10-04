<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use App\Models\PushNotification;
use App\Models\PushNotificationImpression as PushNotificationImpressionModel;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FirebaseAnalyticsDateRangeConstants
{
    const TODAY = 'today';
    const YESTERDAY = 'yesterday';
    const SEVEN_DAYS_AGO = '7daysAgo';
    const THIRTY_DAYS_AGO = '30daysAgo';
    const LAST_SEVEN_DAYS = 'last7Days';
    const LAST_THIRTY_DAYS = 'last30Days';
    const THIS_MONTH = 'thisMonth';
    const LAST_MONTH = 'lastMonth';
    const THIS_YEAR = 'thisYear';
    const LAST_YEAR = 'lastYear';
}

class FirebasePushNotificationAnalytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pushnoti:analytics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to retrieve push notification impressions from Google Analytics';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $propertyId = config('firebase.key.propertyId');
        $eventName = config('firebase.key.logEventName');

        // Get event count for the last 3 days, As google analytics take 24-48 hours to update their report
        for ($i = 1; $i <= 3; $i++) {
            $reportDate = Carbon::now()->subDays($i)->toDateString();
            $this->updateImpressionsPushNotificationFirebaseLogEvent($propertyId, $eventName, $reportDate);
        }
       
        $this->info("Push notification impressions have been updated for previous 3 days.");
        return 0;
    }

    private function updateImpressionsPushNotificationFirebaseLogEvent($propertyId, $eventName, $reportDate) {
        $keyFilePath = base_path(  env('GOOGLE_APPLICATION_CREDENTIALS') );

        // $client = new BetaAnalyticsDataClient([
        //     'credentials' => $keyFilePath,
        // ]);

        $proxy = env('HTTP_PROXY_HOST', 'http://10.84.93.39:8008');

        $client = new BetaAnalyticsDataClient([
            'credentials' => $keyFilePath,
            'transport' => 'rest', // Specify REST transport to configure HTTP proxy
            'httpClientOptions' => [
                'proxy' => $proxy,
                'verify' => false, // Optional: disable SSL cert validation, use with caution
            ]
        ]);
    
        $dateRange = new DateRange([
            'start_date' => $reportDate,
            'end_date' => $reportDate,
        ]);
    
        $eventCountMetric = new Metric(['name' => 'eventCount']);
        $totalUsersMetric = new Metric(['name' => 'totalUsers']);
        $eventCountPerActiveUserMetric = new Metric(['name' => 'eventCountPerUser']);
        $eventsPerSessionMetric = new Metric(['name' => 'eventsPerSession']);

        $dimensionEventName = new Dimension(['name' => 'eventName']);
        $dimensionCountry = new Dimension(['name' => 'country']);
        $dimensionCity = new Dimension(['name' => 'city']);

        $dimensionTitle = new Dimension(['name' => 'customEvent:notification_title']);
        $dimensionNotificationId = new Dimension(['name' => 'customEvent:notification_id']);
        
        $stringFilter = new StringFilter();
        $stringFilter->setValue($eventName);
        $stringFilter->setMatchType(StringFilter\MatchType::EXACT);

        $filter = new Filter();
        $filter->setFieldName('eventName');
        $filter->setStringFilter($stringFilter);
    
        $filterExpression = new FilterExpression();
        $filterExpression->setFilter($filter);

        $request = [
            'property' => "properties/{$propertyId}",
            'dateRanges' => [$dateRange],
            'metrics' => [$eventCountMetric, $totalUsersMetric, $eventCountPerActiveUserMetric, $eventsPerSessionMetric],
            'dimensions' => [$dimensionEventName, $dimensionCountry, $dimensionCity, $dimensionTitle, $dimensionNotificationId],
            'dimensionFilter' => $filterExpression,
        ];
    
        DB::beginTransaction();

        try {
            $response = $client->runReport($request);
    
            foreach ($response->getRows() as $row) {
                $eventName = $row->getDimensionValues()[0]->getValue();
                $eventCountByCountry = $row->getDimensionValues()[1]->getValue();
                $eventCountByCity = $row->getDimensionValues()[2]->getValue();
                $eventParameterTitle = $row->getDimensionValues()[3]->getValue();
                $eventParameterNotificationId = $row->getDimensionValues()[4]->getValue();

                $eventCount = $row->getMetricValues()[0]->getValue();
                $totalUsers = $row->getMetricValues()[1]->getValue();
                $eventCountPerActiveUser = $row->getMetricValues()[2]->getValue();
                $eventsPerSession = $row->getMetricValues()[3]->getValue();

                $data = [
                    'eventName' => $eventName,
                    'notificationId' => $eventParameterNotificationId,
                    'reportDate' => $reportDate,
                    'notificationTitle' => $eventParameterTitle,
                    'eventCount' => $eventCount,
                    'totalUsers' => $totalUsers,
                    'eventCountPerActiveUser' => $eventCountPerActiveUser,
                    'eventCountByCountry' => $eventCountByCountry,
                    'eventCountByCity' => $eventCountByCity,
                    'eventsPerSession' => $eventsPerSession
                ];
                echo json_encode($data, JSON_PRETTY_PRINT) . "\n";

                $pushNotification = PushNotification::find((int) $eventParameterNotificationId);
                if($pushNotification){
                    PushNotificationImpressionModel::updateOrCreate(
                        [
                            'push_notification_id' => $pushNotification->id,
                            'report_date' => $reportDate,
                        ],
                        [
                            'total_viewed' => (int) $eventCount,
                            'total_users' => (int) $totalUsers
                        ]
                    );                    
                }

                $totalImpressions = PushNotificationImpressionModel::where('push_notification_id', $pushNotification->id)
                                                                    ->sum('total_viewed');

                $pushNotification->update([
                    'impressions' => $totalImpressions,
                ]);

                DB::commit();
            }
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }
    
}
