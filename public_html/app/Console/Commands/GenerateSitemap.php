<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Client\PendingRequest;

use DOMDocument;
use DOMXPath;

/**
 * Command to generate a sitemap for the website with custom priorities.
 * 
 * This command crawls the site starting from specified base URLs and generates 
 * a sitemap. It skips certain URLs based on defined patterns and handles both 
 * internal and external links. The generated sitemap prioritizes the links 
 * based on URL structure, special pages, and query parameters.
 * 
 * Skipping Patterns:
 * - URLs containing "javascript:void(0)"
 * - URLs starting with "tel:"
 * - URLs containing non-breaking spaces (U+00A0)
 * 
 * Priority Calculation:
 * - URLs with fewer slashes have higher priority.
 * - Special pages (e.g., "blog", "article") have increased priority.
 * - URLs with query parameters and longer URLs have decreased priority.
 */
class GenerateSitemap extends Command
{
    /**
     * The HTTP client for making requests.
     * 
     * @var PendingRequest
     */
    protected $http;

    /**
     * The name and signature of the console command.
     * 
     * @var string
     */
    protected $signature = 'sitemap:generate {--visit-external} {--timeout=10}';

    /**
     * The console command description.
     * 
     * @var string
     */
    protected $description = 'Generate the sitemap for the website';

    /**
     * List of visited URLs to avoid duplicates.
     * 
     * @var array
     */
    private $visitedUrls = [];

    /**
     * Array of URLs for the generated sitemap.
     * 
     * @var array
     */
    private $sitemapUrls = [];

    /**
     * Array of failed URLs during crawling.
     * 
     * @var array
     */
    private $failedUrls = [];

    /**
     * Array of URLs skipped based on patterns.
     * 
     * @var array
     */
    private $skippedUrls = [];

    /**
     * Array of external URLs encountered.
     * 
     * @var array
     */
    private $externalUrls = [];

    /**
     * Array of URLs not in the expected context.
     * 
     * @var array
     */
    private $notInContextUrls = [];

    /**
     * All nodes encountered during crawling.
     * 
     * @var array
     */
    private $allNodes = [];

    /**
     * Base URLs to start crawling, each with a priority.
     * 
     * @var array
     */
    private $baseUrls = [
        ['/en', 1],  // English base path with highest priority
        ['/my', 0.9] // Myanmar base path with a lower priority
    ];

    /**
     * Patterns used to skip certain URLs.
     * 
     * @var array
     */
    private $skippingPatterns = [
        "/\xC2\xA0/", // Non-breaking space
        '/javascript:void\(0\)/i',  // "javascript:void(0)" pattern
        '/tel:/i', // "tel:" URLs
    ];

    /**
     * List of internal allowed domains.
     * 
     * @var array
     */
    private $internalAllowedUrls = [
        "localhost",
        "www.atom.com.mm"
    ];

    /**
     * Constructor to initialize the HTTP client.
     */
    public function __construct()
    {
        parent::__construct();
        $this->http = $this->buildClient(false);
    }

    /**
     * Command handler to initiate the sitemap generation.
     * 
     * @return void
     */
    public function handle()
    {
        $this->info('Starting to generate sitemap...');

        $visitExternal = $this->option('visit-external');

        foreach ($this->baseUrls as [$basePath, $basePriority]) {
            $baseUrl = URL::to($basePath);
            $this->info('Crawling base URL: ' . $baseUrl);
            $this->crawlUrl($baseUrl, $baseUrl, $visitExternal, $basePriority);
        }

        $this->saveSitemapUrls();
        $this->saveFailedUrls();
        $this->saveSkippedUrls();
        $this->saveExternalUrls();
        $this->saveNotInContextUrls();
        $this->saveAllNodes();

        $this->info('Sitemap generated successfully!');
    }

    /**
     * Builds the HTTP client.
     * 
     * @param bool $middleware Whether to apply middleware (default: true).
     * @param bool $proxy      Whether to use a proxy (default: false).
     * @param int|null $timeout Custom timeout value.
     * 
     * @return PendingRequest
     */
    protected function buildClient(bool $middleware = true, bool $proxy = false, ?int $timeout = null): PendingRequest
    {
        // $options = [
        //     'base_uri' => $this->baseUrl()
        // ];
        $options = [];
        if ($proxy && env('HTTP_PROXY_HOST', false)) {
            $options['proxy'] = env('HTTP_PROXY_HOST', 'http://10.84.93.39:8008');
        }
        $options['timeout'] = $timeout ?? 10;

        $http = Http::withOptions($options)->withoutVerifying();
        return $http;
    }

    /**
     * Determines if a URL should be skipped or crawled.
     * 
     * @param string $url The URL to check.
     * @param string|null $baseUrlContext The base URL for context checking.
     * @param bool $visitExternal Whether to visit external URLs.
     * 
     * @return bool
     */
    private function crawlFilter($url, $baseUrlContext = null, $visitExternal = false){
        if ($this->containsUnwantedPatterns($url)) {
            $this->skippedUrls[] = $url;
            return false;
        }

        if (in_array($url, $this->visitedUrls)) {
            return false;
        }

        if (!$visitExternal && !$this->isInternalLink($url)) {
            $this->externalUrls[] = $url;
            return false;
        }

        if ($baseUrlContext && !$this->isUrlInContext($url, $baseUrlContext)) {
            $this->notInContextUrls[] = $url;
            return false;
        }

        if($this->hasRepetitiveDoubleSlashes($url)){
            return false;
        }

        return true;
    }

    /**
     * Checks if the URL contains repetitive patterns of double slashes.
     * 
     * @param string $url The URL to check.
     * 
     * @return bool
     */
    private function hasRepetitiveDoubleSlashes($url) {
        preg_match_all('/((\/[^\/]+){1,})\1+/', $url, $matches);
        $result = $matches[0][0] ?? '';
        $chars = str_split($result);
        $counts = array_count_values($chars);
        $slashCount = isset($counts['/']) ? $counts['/'] : 0;

        if(strlen($result) >= 4 && $slashCount >= 2) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Crawls the given URL, extracting and processing links.
     * 
     * @param string $url The URL to crawl.
     * @param string|null $baseUrlContext The base URL for context checking.
     * @param bool $visitExternal Whether to visit external URLs.
     * @param float $basePriority The priority of the base URL.
     * 
     * @return void
     */
    private function crawlUrl($url, $baseUrlContext = null, $visitExternal = false, $basePriority = 1.0)
    {
        $url = rtrim($url, '/');

        $crawlFilter = $this->crawlFilter($url, $baseUrlContext, $visitExternal);
        if (!$crawlFilter) {
            return;
        }

        if (strpos($url, '/en/personal/app/en/personal/app/en/personal/app') !== false) {
            dd($url, $this->hasRepetitivePattern($url));
            if($this->hasRepetitivePattern($url)){
                return false;
            }
            
        }

        $this->info('Crawling: ' . $url);
        $this->visitedUrls[] = $url;

        try {
            $passUrl = '';
            if (env('APP_ENV') == 'local') {
                $passUrl = $url;
            } else {
                $passUrl = $this->replaceDomainWithLocalhost($url);
            }
            $response = $this->http->get( $passUrl );

            if ($response->successful()) {
                $priority = $this->calculatePriority($url, $basePriority);
                if (!in_array($url, array_column($this->sitemapUrls, 'url'))) {
                    $this->sitemapUrls[] = ['url' => $url, 'priority' => $priority];
                }
                $html = $response->body();
                $this->extractAndCrawlLinks($html, $url, $baseUrlContext, $visitExternal, $basePriority);
            } else {
                $this->failedUrls[] = $url;
            }
        } catch (\Exception $e) {
            $this->error('Error crawling ' . $url . ': ' . $e->getMessage());
        }
    }
    
    /**
     * Extracts and processes links from the HTML content.
     * 
     * @param string $html The HTML content to extract links from.
     * @param string $currentUrl The URL being crawled.
     * @param string|null $baseUrlContext The base URL for context checking.
     * @param bool $visitExternal Whether to visit external URLs.
     * @param float $basePriority The priority of the base URL.
     * 
     * @return void
     */
    private function extractAndCrawlLinks($html, $currentUrl, $baseUrlContext = null, $visitExternal = false, $basePriority = 1.0)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);

        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//a[@href]');

        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            if(!$href || $href == '/'){
                continue;
            }
            
            $this->allNodes[] = $href;
            $fullUrl = $this->resolveUrl($href, $currentUrl);
            $fullUrl = $this->replaceInternalAllowedDomains($fullUrl);

            $crawlFilter = $this->crawlFilter($fullUrl, $baseUrlContext, $visitExternal);
            if (!$crawlFilter) {
                continue;
            }

            $this->crawlUrl($fullUrl, $baseUrlContext, $visitExternal, $basePriority);
        }
    }

    /**
     * Gets the base domain of the application.
     *
     * This method retrieves the base domain from the application's URL. If it does not work, it can be 
     * customized with environment-specific URLs.
     *
     * @return string The base domain of the application.
     */
    private function getBaseDomain()
    {
        // if does not work then define with APP_ENV specific urls
        return parse_url(URL::to('/'), PHP_URL_HOST);
    }

    /**
     * Resolves a given URL relative to the current URL.
     *
     * If the URL does not contain a host, it is resolved based on the current URL. Special handling
     * is applied for URLs that end or start with language codes (e.g., '/en' or '/my').
     *
     * @param string $url The URL to be resolved.
     * @param string $currentUrl The current URL context for resolution.
     *
     * @return string The resolved URL.
     */
    private function resolveUrl($url, $currentUrl)
    {
        if (parse_url($url, PHP_URL_HOST) === null) {
            // resolving double lang
            // ends with lang
            if (preg_match('/\/(en|my)$/', $currentUrl, $matches)) {
                // starts with lang
                if (preg_match('/^\/(en|my)/', $url)) {
                    return URL::to($url);
                }
            }
            return URL::to(rtrim($currentUrl, '/') . '/' . ltrim($url, '/'));
        }

        return $url;
    }

    /**
     * Checks if the given URL contains unwanted patterns.
     *
     * This method iterates through a list of patterns and checks if the URL matches any of them.
     *
     * @param string $url The URL to check for unwanted patterns.
     *
     * @return bool True if the URL contains any unwanted patterns, false otherwise.
     */
    private function containsUnwantedPatterns($url)
    {
        foreach ($this->skippingPatterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines if a URL is an internal link.
     *
     * This method checks if the URL's host matches the base host of the application or if the URL does
     * not specify a host.
     *
     * @param string $url The URL to check.
     *
     * @return bool True if the URL is internal, false otherwise.
     */
    private function isInternalLink($url)
    {
        $baseHost = parse_url(URL::to('/'), PHP_URL_HOST);
        $urlHost = parse_url($url, PHP_URL_HOST);
        return $urlHost === $baseHost || empty($urlHost);
    }

    /**
     * Checks if a URL is within the context of a base URL.
     *
     * This method determines if the given URL starts with the specified base URL context.
     *
     * @param string $url The URL to check.
     * @param string $baseUrlContext The base URL context to compare against.
     *
     * @return bool True if the URL is within the base URL context, false otherwise.
     */
    private function isUrlInContext($url, $baseUrlContext)
    {
        return strpos($url, $baseUrlContext) === 0;
    }

    /**
     * Replaces the domain of a URL with 'localhost'.
     *
     * This method replaces the host part of the URL with 'localhost'. It also changes the scheme from
     * 'https' to 'http' if applicable.
     *
     * @param string $url The URL to modify.
     *
     * @return string The URL with the domain replaced by 'localhost'.
     */
    private function replaceDomainWithLocalhost($url)
    {
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['host'])) {
            $parsedUrl['host'] = 'localhost';
        }

        if (isset($parsedUrl['scheme']) && $parsedUrl['scheme'] === 'https') {
            $parsedUrl['scheme'] = 'http';
        }

        $replacedUrl = $this->buildUrl($parsedUrl);
        return $replacedUrl;
    }


    /**
     * Replace domains in internalAllowedUrls with the base domain.
     *
     * @param string $url
     * @return string
     */
    private function replaceInternalAllowedDomains($url)
    {
        // Parse the URL and get the host
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? null;
    
        // Check if the URL does not have a host or is an internal allowed URL
        if ($host === null || in_array($host, $this->internalAllowedUrls)) {
            foreach ($this->internalAllowedUrls as $allowedDomain) {
                if (strpos($url, $allowedDomain) !== false) {
                    // If a match is found, update the host to the base domain
                    $parsedUrl['host'] = $this->getBaseDomain();
                    
                    // If the host is not 'localhost', replace the scheme with https
                    if ($parsedUrl['host'] !== 'localhost') {
                        $parsedUrl['scheme'] = 'https';
                    }
                    
                    // Rebuild the URL with the updated domain and scheme
                    $url = $this->buildUrl($parsedUrl);
                    break;
                }
            }
        }
    
        return $url;
    }

    /**
     * Calculate priority for a URL based on custom logic and base priority.
     * 
     * @param string $url
     * @param float $basePriority
     * @return float
     */
    private function calculatePriority($url, $basePriority)
    {
        $priority = 0.5;
        $slashCount = substr_count($url, '/');
        $hasQueryParams = parse_url($url, PHP_URL_QUERY) ? true : false;
        $isSpecialPage = (stripos($url, 'blog') !== false || stripos($url, 'article') !== false);

        $urlLength = strlen($url);
        if ($url === URL::to('/') || $slashCount <= 2 ) {
            $priority = 1.0;
        } elseif ($slashCount <= 3) {
            $priority = 0.9;
        } elseif ($slashCount <= 5) {
            $priority = 0.8;
        } else {
            $priority = 0.5;
        }

        if ($urlLength > 100) {
            $priority -= 0.1;
        }
        if ($hasQueryParams) {
            $priority -= 0.2;
        }
        if ($isSpecialPage) {
            $priority += 0.1;
        }

        $priority = max(0, min($priority, 1));
        return $priority * $basePriority;
    }
    
    /**
     * Builds a valid URL from its components.
     * 
     * @param array $components Parsed URL components.
     * 
     * @return string
     */
    private function buildUrl(array $parsedUrl)
    {
        $scheme   = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : '';
        $host     = $parsedUrl['host'] ?? '';
        $port     = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user     = $parsedUrl['user'] ?? '';
        $pass     = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass']  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = $parsedUrl['path'] ?? '';
        $query    = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
    
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * Saves the sitemap URLs to an XML file.
     *
     * The sitemap is rendered using a Blade view and saved to the public directory as 'sitemap.xml'.
     *
     * @return void
     */
    private function saveSitemapUrls()
    {
        $sitemap = view('sitemap', ['urls' => $this->sitemapUrls])->render();
        File::put(public_path('sitemap.xml'), $sitemap);
    }

    /**
     * Saves the failed URLs encountered during crawling to a log file.
     *
     * Failed URLs are saved in a text file within a date-based directory in the storage/logs/sitemap folder.
     * The file is named with the current timestamp for uniqueness.
     *
     * @return void
     */
    private function saveFailedUrls()
    {
        $uniqueFailedUrls = array_unique($this->failedUrls);
        $directory = storage_path('logs/sitemap/' . now()->format('Y-m-d'));
        File::makeDirectory($directory, 0755, true, true);
        $filePath = $directory . '/failed_urls_' . now()->format('Y-m-d_H-i-s') . '.txt';
        File::put($filePath, implode(PHP_EOL, $uniqueFailedUrls));

        $this->info('Failed URLs saved to: ' . $filePath);
    }

    /**
     * Saves the skipped URLs encountered during crawling to a log file.
     *
     * Skipped URLs are saved in a text file within a date-based directory in the storage/logs/sitemap folder.
     * The file is named with the current timestamp for uniqueness.
     *
     * @return void
     */
    private function saveSkippedUrls()
    {
        $uniqueSkippedUrls = array_unique($this->skippedUrls);
        $directory = storage_path('logs/sitemap/' . now()->format('Y-m-d'));
        File::makeDirectory($directory, 0755, true, true);
        $filePath = $directory . '/skipped_urls_' . now()->format('Y-m-d_H-i-s') . '.txt';
        File::put($filePath, implode(PHP_EOL, $uniqueSkippedUrls));

        $this->info('Skipped URLs saved to: ' . $filePath);
    }

    /**
     * Saves external URLs (outside the base context) encountered during crawling to a log file.
     *
     * External URLs are saved in a text file within a date-based directory in the storage/logs/sitemap folder.
     * The file is named with the current timestamp for uniqueness.
     *
     * @return void
     */
    private function saveExternalUrls()
    {
        $uniqueExternalUrls = array_unique($this->externalUrls);
        $directory = storage_path('logs/sitemap/' . now()->format('Y-m-d'));
        File::makeDirectory($directory, 0755, true, true);
        $filePath = $directory . '/external_urls_' . now()->format('Y-m-d_H-i-s') . '.txt';
        File::put($filePath, implode(PHP_EOL, $uniqueExternalUrls));

        $this->info('External URLs saved to: ' . $filePath);
    }

    /**
     * Saves URLs that are not within the crawling context to a log file.
     *
     * These URLs are considered 'out of context' and are saved in a text file within a date-based directory
     * in the storage/logs/sitemap folder. The file is named with the current timestamp for uniqueness.
     *
     * @return void
     */
    private function saveNotInContextUrls()
    {
        $uniqueNotInContextUrls = array_unique($this->notInContextUrls);
        $directory = storage_path('logs/sitemap/' . now()->format('Y-m-d'));
        File::makeDirectory($directory, 0755, true, true);
        $filePath = $directory . '/not_in_context_urls_' . now()->format('Y-m-d_H-i-s') . '.txt';
        File::put($filePath, implode(PHP_EOL, $uniqueNotInContextUrls));

        $this->info('Not-in-context URLs saved to: ' . $filePath);
    }

    /**
     * Saves all nodes (URLs) encountered during the crawling process to a log file.
     *
     * The URLs are saved in a text file within a date-based directory in the storage/logs/sitemap folder.
     * The file is named with the current timestamp for uniqueness.
     *
     * @return void
     */
    private function saveAllNodes()
    {
        $uniqueNodes = array_unique($this->allNodes);
        $directory = storage_path('logs/sitemap/' . now()->format('Y-m-d'));
        File::makeDirectory($directory, 0755, true, true);
        $filePath = $directory . '/all_nodes_' . now()->format('Y-m-d_H-i-s') . '.txt';
        File::put($filePath, implode(PHP_EOL, $uniqueNodes));

        $this->info('All nodes saved to: ' . $filePath);
    }

}
