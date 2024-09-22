<?php

namespace App\Helpers;

use App\Enums\CacheEnum;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;


class FirebasePushNotification
{
    private $headers;
    private $config;
    private $accessToken;

    private $proxy;
    private $tokenExpirationTimeInSec = 3600;

    protected $serviceAccountConfig;

    /**
     * FirebasePushNotification constructor.
     * @param string $serviceAccountKeyFilePath Path to the service account key JSON file.
     * @param string $mode Configuration key to load settings (optional).
     */
    public function __construct(
        $proxy = true,
        string $mode = 'httpv1'
    )
    {
        if ($proxy && env('HTTP_PROXY_HOST', false) && env('APP_ENV') !== 'local') {
            $this->proxy = env('HTTP_PROXY_HOST', 'tnmproxy.telenor.com.mm:8008');
        }

        $this->config = config('firebase');
        $serviceAccountKeyFilePath=base_path() . '/' . env('GOOGLE_APPLICATION_CREDENTIALS');
        // Check if the file exists and is readable
        if (!file_exists($serviceAccountKeyFilePath) || !is_readable($serviceAccountKeyFilePath)) {
            throw new Exception("The service account key file does not exist or is not readable: " . $serviceAccountKeyFilePath);
        }

        $this->serviceAccountConfig = $this->getServiceAccountConfiguration( $serviceAccountKeyFilePath );
    }


    /**
     * Reads the service account key JSON file and extracts the required fields.
     * 
     * @param string $serviceAccountKeyFilePath Path to the service account key JSON file.
     * 
     * @return array The extracted service account configuration.
     * 
     * @throws Exception If the file cannot be read, decoded, or if the required fields are missing.
     */
    public function getServiceAccountConfiguration( string $serviceAccountKeyFilePath ): array
    {
        try {
            // Read the service account key JSON file
            $serviceAccountJson = file_get_contents( $serviceAccountKeyFilePath );

            if ($serviceAccountJson === false) {
                throw new Exception("Failed to read the JSON file. Please check if the file exists and is readable.");
            }

            // Decode JSON to array
            $serviceAccount = json_decode($serviceAccountJson, true);

            // Check if JSON decoding was successful
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Failed to decode JSON: " . json_last_error_msg());
            }

            // Extract the required fields from the service account JSON
            $serviceAccountConfig = [
                'type' => $serviceAccount['type'],
                'project_id' => $serviceAccount['project_id'],
                'private_key_id' => $serviceAccount['private_key_id'],
                'private_key' => $serviceAccount['private_key'],
                'client_email' => $serviceAccount['client_email'],
                'client_id' => $serviceAccount['client_id'],
                'token_uri' => $serviceAccount['token_uri'],
                'auth_uri' => $serviceAccount['auth_uri'],
                'auth_provider_x509_cert_url' => $serviceAccount['auth_provider_x509_cert_url'],
                'client_x509_cert_url' => $serviceAccount['client_x509_cert_url'],
                'universe_domain' => $serviceAccount['universe_domain'],
            ];

            // Check if all the key exists
            $requiredKeys = [
                'type',
                'project_id',
                'private_key_id',
                'private_key',
                'client_email',
                'client_id',
                'token_uri',
                'auth_uri',
                'auth_provider_x509_cert_url',
                'client_x509_cert_url',
                'universe_domain',
            ];

            $missingKeys = array_diff($requiredKeys, array_keys($serviceAccountConfig));

            if (count($missingKeys) > 0) {
                throw new Exception("The service account key file is missing the following keys: " . implode(', ', $missingKeys));
            }

            return $serviceAccountConfig;
        } catch (Exception $e) {
            Log::error("Failed to read and parse the service account key file: " . $e->getMessage());
            throw $e;
        }
    }


    protected function getProjectId(): string
    {
        return $this->serviceAccountConfig['project_id'];
    }

    protected function getPrivateKey(): string
    {
        return $this->serviceAccountConfig['private_key'];
    }

    protected function getClientEmail(): string
    {
        return $this->serviceAccountConfig['client_email'];
    }

    /**
     * @return string
     */
    protected function baseUrl(): string
    {
        return "https://oauth2.googleapis.com";
    }

    /**
     * @return string
     */
    private function tokenUrl(): string
    {
        // return $this->baseUrl() . '/token';
        return $this->serviceAccountConfig['token_uri'];
    }

    /**
     * @return bool
     */
    public function authenticate(): bool
    {
        $this->accessToken = $this->getAccessToken();
        if(!$this->accessToken){
            return false;
        }
        
        $this->headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
            'access_token_auth: true'
        ];
        
        return true;
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Retrieves the token expiration time in seconds.
     *
     * @return int token expiration time in seconds
     */
    public function getTokenExpirationTimeInSec(): int
    {
        return $this->tokenExpirationTimeInSec;
    }

    /**
     * Sets the expiration time in seconds for the Firebase Push Notification access token.
     *
     * @param int $tokenExpirationTimeInSec The expiration time in seconds for the access token.
     * @return void
     */
    public function setTokenExpirationTimeInSec(int $tokenExpirationTimeInSec): void
    {
        $this->tokenExpirationTimeInSec = $tokenExpirationTimeInSec;
    }

    
    
    /**
     * Creates a JSON Web Token (JWT) for authentication purposes.
     *
     * @param string $authScope The authentication scope.
     * @param string $tokenUrl The URL of the token endpoint.
     * @param string $clientEmail The email address of the client.
     * @param string $privateKey The private key used for signing the JWT.
     *
     * @throws Exception If the private key resource cannot be obtained.
     *
     * @return string The generated JWT.
     */
    private function createJwt(
        string $authScope,
        string $tokenUrl,
        string $clientEmail,
        string $privateKey
    ): string {
        // Create JWT header
        $jwtHeader = json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]);

        $jwtHeaderBase64 = rtrim(strtr(base64_encode($jwtHeader), '+/', '-_'), '=');

        // Create JWT claim set
        $now = time();
        $exp = $now + $this->tokenExpirationTimeInSec;
        $jwtClaims = json_encode([
            'iss' => $clientEmail,
            'scope' => $authScope,
            // 'aud' => 'https://oauth2.googleapis.com/token',
            'aud' => $tokenUrl,
            'iat' => $now,
            'exp' => $exp
        ]);

        $jwtClaimsBase64 = rtrim(strtr(base64_encode($jwtClaims), '+/', '-_'), '=');

        // Create JWT signature
        $jwtSignatureInput = $jwtHeaderBase64 . '.' . $jwtClaimsBase64;
        $privateKey = str_replace(["\n", "\r"], ["\n", "\r"], $privateKey); // Fix newlines

        $privateKeyResource = openssl_pkey_get_private($privateKey);
        if ($privateKeyResource === false) {
            throw new Exception("Failed to get private key resource.");
        }

        openssl_sign($jwtSignatureInput, $jwtSignature, $privateKeyResource, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKeyResource);

        $jwtSignatureBase64 = rtrim(strtr(base64_encode($jwtSignature), '+/', '-_'), '=');

        // Form the JWT
        return $jwtHeaderBase64 . '.' . $jwtClaimsBase64 . '.' . $jwtSignatureBase64;
    }

    /**
     * Retrieves an access token for Firebase Push Notification.
     *
     * First, it checks if a cached token exists in Redis. If it does, the cached token is returned.
     * Otherwise, it generates a new token by creating a JWT, signing it with a private key, and exchanging it for an access token.
     * The new token is then cached in Redis for a specified expiration time.
     *
     * @throws Exception if the JSON file cannot be read, decoded, or if the private key resource cannot be obtained.
     * @throws Exception if the access token is not found in the response.
     * @return string the access token
     */
    public function getAccessToken(): string
    {
        $cachedToken = Redis::get(CacheEnum::PUSHNOTIFICATION_ACCESS_TOKEN);
        if($cachedToken){
            return $cachedToken;
        }

        $privateKey = $this->getPrivateKey();
        $clientEmail = $this->getClientEmail();
        $authScope = config('firebase.auth.scope');
        $tokenUrl = $this->tokenUrl();
        $jwt = $this->createJwt(
            $authScope,
            $tokenUrl,
            $clientEmail,
            $privateKey
        );
        $headers = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
        $payloadString = http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        // Use the makeCurlRequest method to get the response
        // Exchange JWT for an access token
        $responseData = $this->makeCurlRequest($headers, $this->tokenUrl(), $payloadString);
        $accessToken = $responseData['access_token'] ?? null;
        $tokenType = $responseData['token_type'] ?? null;
        $expiresIn = $responseData['expires_in'] ?? null;

        if (!$accessToken || !$tokenType || !$expiresIn) {
            throw new Exception("Access token, token type, or expires_in not found in response.");
        }

        $bufferSeconds = 10;
        Redis::setex(CacheEnum::PUSHNOTIFICATION_ACCESS_TOKEN, $expiresIn - $bufferSeconds, $accessToken);
        Log::info('access toekn'. $accessToken);
        return $accessToken;
    }

    /**
     * Send a push notification using Firebase Cloud Messaging.
     *
     * @param string $deviceToken The recipient's device token.
     * @param string $title The title of the notification.
     * @param string $body The body of the notification.
     * @param array $additionalData Optional additional data to send with the notification.
     * @return array The response from Firebase.
     * @throws Exception
     */
    public function send(array $payload = []): array
    {
        $this->authenticate();
        
        $url = sprintf($this->config['urls']['send_message'], $this->getProjectId());
        $response = $this->makeCurlRequest($this->headers, $url, $payload);
        if (isset($response['name'])) {
            // Extract project_id and message_id from the response
            preg_match('/projects\/([^\/]+)\/messages\/(\d+)/', $response['name'], $matches);
            $projectId = $matches[1] ?? null;
            $messageId = $matches[2] ?? null;

            if ($projectId === $this->getProjectId() && $messageId) {
                return $response;
            }
        }

        throw new Exception("Error sending push notification: " . json_encode($response));
    }

    /**
     * Subscribe to a topic.
     *
     * @param string $topic The topic to subscribe to.
     * @param array $registrationTokenOrTokens The registration tokens.
     * @param bool $subscribe True to subscribe, false to unsubscribe.
     * @return array The response from Firebase.
     * @throws Exception
     */
    public function subscribeToTopic(string $topic, array $registrationTokenOrTokens, bool $subscribe = true): array
    {
        if (!$this->accessToken) {
            $this->authenticate();
        }

        $url = $subscribe ? $this->config['urls']['subscribe'] : $this->config['urls']['unsubscribe'];

        $payload = [
            'to' => "/topics/$topic",
            'registration_tokens' => $registrationTokenOrTokens 
        ];

        $response = $this->makeCurlRequest($this->headers, $url, $payload);

        $results = [];
        foreach ($registrationTokenOrTokens as $index => $token) {
            $results[$token] = isset($response['results'][$index]['error']) ?
                ['subscribed' => false, 'error' => $response['results'][$index]['error']] :
                ['subscribed' => $subscribe ? true : false];
        }

        $results['token'] = $this->accessToken;
        return $results;
    }

    /**
     * Make a cURL request.
     *
     * @param string $url The URL to make the request to.
     * @param array $payload The payload to send.
     * @return array The response from the server.
     * @throws Exception
     */
    private function makeCurlRequest(array $headers, string $url, $payload, string $proxy = null, string $proxyUserPwd = null): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (is_array($payload)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        if ($proxy || $this->proxy) {
            curl_setopt($ch, CURLOPT_PROXY, $proxy ?? $this->proxy);
        }
    
        if ($proxyUserPwd) {
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxyUserPwd);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new Exception("Error making cURL request: " . $response);
        }

        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to decode response JSON: " . json_last_error_msg());
        }

        return $responseData;
    }
}

