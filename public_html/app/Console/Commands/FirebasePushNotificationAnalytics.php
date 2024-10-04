<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use App\Models\PushNotification;
use App\Models\PushNotificationAnalytics;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FirebasePushNotificationAnalytics extends Command
{
    protected $signature = 'pushnoti:analytics {--days=3 : The number of days to get analytics for} {--proxy : Use the proxy defined in the environment}';
    protected $description = 'Retrieve push notification impressions from Google Analytics';

    private $client;
    private $propertyId;
    private $eventName;

    public function __construct()
    {
        parent::__construct();
        $this->propertyId = config('firebase.key.propertyId');
        $this->eventName = config('firebase.key.logEventName');
    }

    public function handle()
    {
        $this->initializeAnalyticsClient();

        $days = (int)$this->option('days');
        if ($days <= 0) {
            $this->error('The number of days must be greater than zero.');
            return 1;
        }

        $datesToFetch = $this->getDatesForLastDays($days);

        foreach ($datesToFetch as $reportDate) {
            $this->updateImpressions($reportDate);
        }

        $this->info("Push notification impressions have been updated for the last {$days} days.");
        return 0;
    }

    private function getDatesForLastDays($days)
    {
        return collect(range(0, $days - 1))->map(function ($i) {
            return Carbon::now()->subDays($i)->toDateString();
        })->toArray();
    }

    private function initializeAnalyticsClient()
    {
        $keyFilePath = base_path(env('GOOGLE_APPLICATION_CREDENTIALS'));
        $options = [
            'credentials' => $keyFilePath,
        ];
        
        $useProxy = $this->option('proxy');
        if ($useProxy) {
            $proxy = $useProxy ? env('HTTP_PROXY_HOST', 'http://10.84.93.39:8008') : null;
            $options['transport'] = 'rest';
            $options['httpClientOptions'] = [
                'proxy' => $proxy,
                'verify' => false,
            ];
        }

        $this->client = new BetaAnalyticsDataClient($options);
    }

    private function updateImpressions($reportDate)
    {
        $request = $this->buildAnalyticsRequest($reportDate);

        try {
            DB::transaction(function () use ($request, $reportDate) {
                $response = $this->client->runReport($request);
                $this->processAnalyticsResponse($response, $reportDate);
            }, 3);
            return true;
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return false;
        }
    }

    private function buildAnalyticsRequest($reportDate)
    {
        $dateRange = new DateRange([
            'start_date' => $reportDate,
            'end_date' => $reportDate,
        ]);

        $metrics = [
            new Metric(['name' => 'eventCount']),
            new Metric(['name' => 'totalUsers']),
            new Metric(['name' => 'eventCountPerUser']),
            new Metric(['name' => 'eventsPerSession']),
        ];

        $dimensions = [
            new Dimension(['name' => 'eventName']),
            new Dimension(['name' => 'country']),
            new Dimension(['name' => 'city']),
            new Dimension(['name' => 'customEvent:notification_title']),
            new Dimension(['name' => 'customEvent:notification_id']),
        ];

        $filterExpression = $this->createEventNameFilter();

        return [
            'property' => "properties/{$this->propertyId}",
            'dateRanges' => [$dateRange],
            'metrics' => $metrics,
            'dimensions' => $dimensions,
            'dimensionFilter' => $filterExpression,
        ];
    }

    private function createEventNameFilter()
    {
        $stringFilter = new StringFilter([
            'value' => $this->eventName,
            'match_type' => StringFilter\MatchType::EXACT,
        ]);

        $filter = new Filter([
            'field_name' => 'eventName',
            'string_filter' => $stringFilter,
        ]);

        return new FilterExpression(['filter' => $filter]);
    }

    private function processAnalyticsResponse($response, $reportDate)
    {
        foreach ($response->getRows() as $row) {
            $data = $this->extractDataFromRow($row, $reportDate);
            $this->updateDatabase($data);
        }
    }

    private function extractDataFromRow($row, $reportDate)
    {
        $dimensionValues = $row->getDimensionValues();
        $metricValues = $row->getMetricValues();

        return [
            'eventName' => $dimensionValues[0]->getValue(),
            'eventCountByCountry' => $dimensionValues[1]->getValue(),
            'eventCountByCity' => $dimensionValues[2]->getValue(),
            'eventParameterTitle' => $dimensionValues[3]->getValue(),
            'eventParameterNotificationId' => $dimensionValues[4]->getValue(),
            'eventCount' => $metricValues[0]->getValue(),
            'totalUsers' => $metricValues[1]->getValue(),
            'eventCountPerActiveUser' => $metricValues[2]->getValue(),
            'eventsPerSession' => $metricValues[3]->getValue(),
            'reportDate' => $reportDate,
        ];
    }

    private function updateDatabase($data)
    {
        try {
            DB::transaction(function () use ($data) {
                $pushNotification = PushNotification::find((int) $data['eventParameterNotificationId']);
                if (!$pushNotification) {
                    $this->error('Push notification not found for ID: ' . $data['eventParameterNotificationId']);
                    return; // Early return if not found
                }

                PushNotificationAnalytics::updateOrCreate(
                    [
                        'push_notification_id' => $pushNotification->id,
                        'report_date' => $data['reportDate'],
                    ],
                    [
                        'total_received' => (int) $data['eventCount'],
                        'unique_users' => (int) $data['totalUsers']
                    ]
                );

                $totalImpressions = PushNotificationAnalytics::where('push_notification_id', $pushNotification->id)
                                                            ->sum('total_received');

                $pushNotification->update(['total_received' => $totalImpressions]);
            });
        } catch (Exception $e) {
            print_r($data);
            $this->error('Failed to update database: ' . $e->getMessage());
        }
    }


}
