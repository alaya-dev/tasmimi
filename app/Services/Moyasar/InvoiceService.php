<?php

namespace App\Services\Moyasar;

use GuzzleHttp\Client;
use Exception;

class InvoiceService
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
     * Create a new invoice
     */
    public function create(array $data)
    {
        try {
            $response = $this->client->post('invoices', [
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Fetch an invoice by ID
     */
    public function fetch(string $invoiceId)
    {
        try {
            $response = $this->client->get("invoices/{$invoiceId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to fetch invoice: ' . $e->getMessage());
        }
    }

    /**
     * List all invoices
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

            $response = $this->client->get('invoices', [
                'query' => $queryParams
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to list invoices: ' . $e->getMessage());
        }
    }

    /**
     * Update an invoice
     */
    public function update(string $invoiceId, array $data)
    {
        try {
            $response = $this->client->put("invoices/{$invoiceId}", [
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to update invoice: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an invoice
     */
    public function cancel(string $invoiceId)
    {
        try {
            $response = $this->client->post("invoices/{$invoiceId}/cancel");
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            throw new Exception('Failed to cancel invoice: ' . $e->getMessage());
        }
    }
}
