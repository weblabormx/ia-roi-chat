<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class AzureLanguage
{
    protected string $endpoint;
    protected string $apiKey;
    protected string $defaultLanguage;
    protected float $confidenceThreshold;

    public function __construct(string $defaultLanguage = 'en', float $confidenceThreshold = 0.8)
    {
        $this->endpoint = env('AZURE_LANGUAGE_ENDPOINT');
        $this->apiKey = env('AZURE_LANGUAGE_KEY');
        $this->defaultLanguage = $defaultLanguage;
        $this->confidenceThreshold = $confidenceThreshold;
    }

    public function detectLanguage(string $text): string
    {
        $result = $this->sendRequest('/languages', [
            'documents' => [['id' => '1', 'text' => $text]]
        ]);
        $lang = $result['documents'][0]['detectedLanguage']['name'] ?? null;
        return $lang;
    }

    public function analyzeSentiment(string $text): array
    {
        $response = $this->sendRequest('/sentiment', [
            'documents' => [['id' => '1', 'text' => $text, 'language' => $this->defaultLanguage]]
        ]);
        return $this->filterByConfidence($response);
    }

    public function extractKeyPhrases(string $text): array
    {
        return $this->sendRequest('/keyPhrases', [
            'documents' => [['id' => '1', 'text' => $text, 'language' => $this->defaultLanguage]]
        ]);
    }

    public function extractPII(string $text): array
    {
        $response = $this->sendRequest('/entities/recognition/pii', [
            'documents' => [['id' => '1', 'text' => $text, 'language' => $this->defaultLanguage]]
        ]);
        return $this->filterByConfidence($response);
    }

    public function extractLinkedEntities(string $text): array
    {
        $response = $this->sendRequest('/entities/linking', [
            'documents' => [['id' => '1', 'text' => $text, 'language' => $this->defaultLanguage]]
        ]);
        return $this->filterByConfidence($response);
    }

    public function extractEntities(string $text): array
    {
        $response = $this->sendRequest('/entities/recognition/general', [
            'documents' => [['id' => '1', 'text' => $text, 'language' => $this->defaultLanguage]]
        ]);
        return $response;
    }

    public function analyzeHealthText(string $text): array
    {
        $response = $this->sendRequest('/entities/health', [
            'documents' => [['id' => '1', 'text' => $text, 'language' => $this->defaultLanguage]]
        ]);
        return $this->filterByConfidence($response);
    }

    public function sentimentAnalysis(string $text): array
    {
        return $this->sendRequest('/sentiment', [
            'documents' => [['id' => '1', 'text' => $text, 'language' => $this->defaultLanguage]]
        ]);
    }

    private function sendRequest(string $path, array $data): array
    {
        $url = rtrim($this->endpoint, '/') . '/text/analytics/v3.1' . $path;

        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($url, $data);

        return $response->json();
    }

    private function filterByConfidence(array $response): array
    {
        if (!isset($response['documents'])) {
            return $response;
        }

        $response['documents'] = collect($response['documents'])->map(function ($document) {
            if (isset($document['entities'])) {
                $document['entities'] = collect($document['entities'])->map(function($item) {
                    if(isset($item['matches'])) {
                        $item['matches'] = collect($item['matches'])->filter(function($match) {
                            return $match['confidenceScore'] >= $this->confidenceThreshold;
                        })->values()->all();
                    }
                    return $item;
                })->filter(function($item) {
                    return (isset($item['matches']) && count($item['matches']) > 0) || !isset($item['matches']);
                })->values()->all();
            }
            return $document;
        })->all();

        return $response;
    }
}