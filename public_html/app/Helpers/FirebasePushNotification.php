<?php

namespace App\Helpers;

use Exception;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;


class FirebasePushNotification
{
    private $headers;
    private $config;
    private $accessToken;
    private $projectId;
    private $serviceAccountKeyFilePath;
    private $proxy;

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

        $this->serviceAccountKeyFilePath = $serviceAccountKeyFilePath;
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
        return $this->baseUrl() . '/token';
    }

    public function authenticate(){
        $this->getAccessToken();
        $this->headers = [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
            'access_token_auth: true'
        ];
        if($this->accessToken) {
            return true;
        } else {
            return false;
        }
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function setProjectId(string $projectId): void
    {
        $this->projectId = $projectId;
    }

    public function getProjectId(): string
    {
        $serviceAccountJson = file_get_contents($this->serviceAccountKeyFilePath);

        // Check if file_get_contents() succeeded
        if ($serviceAccountJson === false) {
            throw new Exception("Failed to read the JSON file.");
        }

        // Decode JSON to array
        $serviceAccount = json_decode($serviceAccountJson, true);

        // Check if JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to decode JSON: " . json_last_error_msg());
        }
    
        // Check if project_id is present and not null
        if (!isset($serviceAccount['project_id']) || empty($serviceAccount['project_id'])) {
            throw new Exception("Project ID not found in the service account JSON.");
        }

        // Assign project_id to class property and return it
        $projectId = $serviceAccount['project_id'];
        return $projectId;
    }

    /**
     * Retrieve access token from Firebase
     * @return string
     * @throws Exception
     */
    public function getAccessToken(): string
    {
        $serviceAccountJson = file_get_contents($this->serviceAccountKeyFilePath);

        // Check if file_get_contents() succeeded
        if ($serviceAccountJson === false) {
            throw new Exception("Failed to read the JSON file.");
        }

        // Decode JSON to array
        $serviceAccount = json_decode($serviceAccountJson, true);

        // Check if JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to decode JSON: " . json_last_error_msg());
        }
    
        // Extract private key and client email from the JSON
        $privateKey = $serviceAccount['private_key'];
        $clientEmail = $serviceAccount['client_email'];
        $this->projectId = $this->getProjectId();
    
        // Create JWT header
        $jwtHeader = json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT'
        ]);
    
        $jwtHeaderBase64 = rtrim(strtr(base64_encode($jwtHeader), '+/', '-_'), '=');
    
        // Create JWT claim set
        $now = time();
        $exp = $now + 3600;
        $jwtClaims = json_encode([
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/cloud-platform',
            'aud' => 'https://oauth2.googleapis.com/token',
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
        $jwt = $jwtHeaderBase64 . '.' . $jwtClaimsBase64 . '.' . $jwtSignatureBase64;

        $url = 'https://oauth2.googleapis.com/token';
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
    
        if (!$accessToken) {
            throw new Exception("Access token not found in response.");
        }

        // Cache the access token to avoid requesting it multiple times
        $this->accessToken = $accessToken;
    
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
        if (!$this->accessToken) {
            $this->authenticate();
        }

        $url = sprintf($this->config['urls']['send_message'], $this->projectId);
        $response = $this->makeCurlRequest($this->headers, $url, $payload);
        return $response;
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

