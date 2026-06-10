<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ShopifyService
{
    protected $shopUrl;
    protected $apiVersion;
    protected $accessToken;

    public function __construct()
    {
        $this->shopUrl = config('shopify.shop_url');
        $this->apiVersion = config('shopify.api_version');
        $this->accessToken = config('shopify.access_token');
    }

    protected function request($method, $endpoint, $data = [])
    {
        $url = "{$this->shopUrl}/admin/api/{$this->apiVersion}/{$endpoint}";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->send($method, $url, [
            'json' => $data
        ]);

        return [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
        ];
    }

    public function createCustomer($first, $last, $email)
    {
        return $this->request(
            'POST',
            "customers.json",
            [
                "customer" => [
                    "first_name" => $first,
                    "last_name"  => $last,
                    "email"      => $email,
                    "verified_email" => true,
                    "addresses" => [
                        [
                            "address1" => "40th Street",
                            "city" => "Makati City",
                            "province" => "Metro Manila",
                            "zip" => "1900",
                            "country" => "Philippines",
                            "first_name" => $first,
                            "last_name" => $last
                        ]
                    ]
                ]
            ]
        );
    }

    public function sendInvite($customerId)
    {
        return $this->request(
            'POST',
            "customers/{$customerId}/send_invite.json"
        );
    }

    public function createCustomerFromArray(array $data)
    {
        return $this->request(
            'POST',
            "customers.json",
            [
                "customer" => [
                    "first_name" => $data['first_name'] ?? null,
                    "last_name"  => $data['last_name'] ?? null,
                    "email"      => $data['email'] ?? null,
                    "verified_email" => true,
                    "addresses" => [
                        [
                            "address1" => "40th Street",
                            "city" => "Makati City",
                            "province" => "Metro Manila",
                            "zip" => "1900",
                            "country" => "Philippines",
                            "first_name" => $data['first_name'] ?? null,
                            "last_name" => $data['last_name'] ?? null
                        ]
                    ]
                ]
            ]
        );
    }

    public function graphql($query)
    {
        return $this->request(
            'POST',
            'graphql.json',
            [
                'query' => $query
            ]
        );
    }

    public function startCustomerBulkExport($queryFilter = "")
    {
        $queryFilter = trim($queryFilter);

        $customerQuery = $queryFilter
            ? "customers(query: \"{$queryFilter}\")"
            : "customers";

        $query = <<<GQL
    mutation {
    bulkOperationRunQuery(
        query: """
        {
        {$customerQuery} {
            edges {
            node {
                id
                firstName
                lastName
                email
                phone
                createdAt
                updatedAt
                state
                numberOfOrders
                amountSpent {
                amount
                currencyCode
                }
            }
            }
        }
        }
        """
    ) {
        bulkOperation {
        id
        status
        }
        userErrors {
        field
        message
        }
    }
    }
    GQL;

        return $this->graphql($query);
    }

    public function getBulkExportStatus()
    {
        $query = <<<GQL
    {
    currentBulkOperation {
        id
        status
        errorCode
        url
        objectCount
        fileSize
        completedAt
    }
    }
    GQL;

        return $this->graphql($query);
    }

    public function getCustomersByIds(array $ids)
    {
        $url = "{$this->shopUrl}/admin/api/{$this->apiVersion}/customers.json";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->get($url, [
            'ids' => implode(',', $ids)
        ]);

        return [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
        ];
    }

    public function searchCustomerByEmail(string $email)
    {
        $email = trim($email);

        $url = "{$this->shopUrl}/admin/api/{$this->apiVersion}/customers/search.json";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->get($url, [
            'query' => "email:{$email}"
        ]);

        return [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
        ];
    }

    public function searchCustomers(string $query, int $limit = 250)
    {
        $url = "{$this->shopUrl}/admin/api/{$this->apiVersion}/customers/search.json";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->get($url, [
            'query' => $query,
            'limit' => $limit,
        ]);

        return [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
            'headers' => $response->headers(),
        ];
    }

    public function searchCustomersByUrl(string $url)
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->get($url);

        return [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
            'headers' => $response->headers(),
        ];
    }

    public function countCustomersByState(string $state)
    {
        $total = 0;

        $result = $this->searchCustomers("state:{$state}");

        while (true) {

            $customers = $result['json']['customers'] ?? [];

            $total += count($customers);

            $linkHeader = $result['headers']['link'][0] ?? null;

            if (!$linkHeader || strpos($linkHeader, 'rel="next"') === false) {
                break;
            }

            preg_match('/<(.*?)>;\s*rel="next"/', $linkHeader, $matches);

            if (empty($matches[1])) {
                break;
            }

            $nextUrl = $matches[1];

            $result = $this->searchCustomersByUrl($nextUrl);
        }

        return $total;
    }

    public function countCustomers(string $query = '')
    {
        $url = "{$this->shopUrl}/admin/api/{$this->apiVersion}/customers/count.json";
        $params = [];
        if ($query !== '') {
            $params['query'] = $query;
        }

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->get($url, $params);

        return [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
        ];
    }

    public function getCustomers(array $params = [])
    {
        $url = "{$this->shopUrl}/admin/api/{$this->apiVersion}/customers.json";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])->get($url, $params);

        return [
            'url' => $url,
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
        ];
    }

    public function testConnection()
    {
        return $this->request(
            'GET',
            'shop.json'
        );
    }
}