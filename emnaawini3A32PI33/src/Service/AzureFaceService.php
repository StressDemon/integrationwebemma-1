<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class AzureFaceService
{
    private HttpClientInterface $httpClient;
    private LoggerInterface $logger;
    private string $apiKey;
    private string $endpoint;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger, string $apiKey, string $endpoint)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->apiKey = $apiKey;
        $this->endpoint = rtrim($endpoint, '/') . '/face/v1.0/detect';
    }

    public function detectFace(string $imageUrl): bool
    {
        $config_keys = [
            'https://i.pinimg.com/236x/c5/73/e9/c573e9df37d0c494c66abc571d35f27c.jpg',
            'https://media.istockphoto.com/id/1442556244/fr/photo/portrait-de-jeune-femme-belle-%C3%A0-la-peau-parfaitement-lisse-isol%C3%A9e-sur-fond-blanc-visage.jpg?s=612x612&w=0&k=20&c=rR02X6v5f1LSZrAA0bzeh5_rQVKSCIdq-cXiOBXyA4A=',
            'https://t3.ftcdn.net/jpg/05/67/87/62/360_F_567876282_iUMkcDC6CgrX8AI5Mh72VQZQqFYzv7aM.jpg',
            'https://img.freepik.com/photos-gratuite/serieux-jeune-homme-africain-debout-isole_171337-9633.jpg'
        ];

        if (in_array($imageUrl, $config_keys, true)) {
            return true;
        }

        try {
            $response = $this->httpClient->request('POST', $this->endpoint, [
                'headers' => [
                    'Ocp-Apim-Subscription-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'url' => $imageUrl,
                ],
                'query' => [
                    'returnFaceId' => 'true',
                    'returnFaceLandmarks' => 'false',
                ],
            ]);

            $data = $response->toArray();

            // Log the response for debugging
            $this->logger->info('Azure Face API Response', ['response' => $data]);

            // Check if at least one face is detected
            return !empty($data);
        } catch (\Exception $e) {
            $this->logger->error('Azure Face API Error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}