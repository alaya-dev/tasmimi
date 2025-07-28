<?php

namespace App\Services\Moyasar;

use GuzzleHttp\Client;
use Exception;

class PaymentService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('moyasar.secret_key');
        $this->baseUrl = config('moyasar.base_url', 'https://api.moyasar.com/v1/');

        $clientConfig = [
            'base_uri' => $this->baseUrl,
            'auth' => [$this->apiKey, ''],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'verify' => config('moyasar.verify_ssl', true),
        ];

        $this->client = new Client($clientConfig);
    }

    /**
     * Create a new payment
     */
    public function create(array $data)
    {
        try {
            $response = $this->client->post('payments', [
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to create payment: ' . $e->getMessage());
        }
    }

    /**
     * Create a hosted payment (without source - user will pay on Moyasar page)
     */
    public function createHostedPayment(array $data)
    {
        // Remove source if present - hosted payments don't need it
        unset($data['source']);

        return $this->create($data);
    }

    /**
     * Fetch a payment by ID
     */
    public function fetch(string $paymentId)
    {
        try {
            $response = $this->client->get("payments/{$paymentId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to fetch payment: ' . $e->getMessage());
        }
    }

    /**
     * List all payments
     */
    public function all($search = null)
    {
        try {
            $queryParams = [];
            
            if (is_array($search)) {
                $queryParams = $search;
            } elseif ($search instanceof Search) {
                $queryParams = $search->toArray();
            }

            $response = $this->client->get('payments', [
                'query' => $queryParams
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to list payments: ' . $e->getMessage());
        }
    }

    /**
     * Update a payment
     */
    public function update(string $paymentId, array $data)
    {
        try {
            $response = $this->client->put("payments/{$paymentId}", [
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to update payment: ' . $e->getMessage());
        }
    }

    /**
     * Refund a payment
     */
    public function refund(string $paymentId, int $amount = null)
    {
        try {
            $data = [];
            if ($amount !== null) {
                $data['amount'] = $amount;
            }

            $response = $this->client->post("payments/{$paymentId}/refund", [
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to refund payment: ' . $e->getMessage());
        }
    }

    /**
     * Capture a payment
     */
    public function capture(string $paymentId, int $amount = null)
    {
        try {
            $data = [];
            if ($amount !== null) {
                $data['amount'] = $amount;
            }

            $response = $this->client->post("payments/{$paymentId}/capture", [
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to capture payment: ' . $e->getMessage());
        }
    }

    /**
     * Void a payment
     */
    public function void(string $paymentId)
    {
        try {
            $response = $this->client->post("payments/{$paymentId}/void");
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to void payment: ' . $e->getMessage());
        }
    }
}
