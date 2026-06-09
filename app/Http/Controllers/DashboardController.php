<?php

namespace App\Http\Controllers;

use App\Services\ShopifyService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(ShopifyService $shopify)
    {
        $weekAgo = Carbon::now()->subWeek()->toIso8601String();

        $customerCount = $shopify->countCustomers();
        $invitedCount = $shopify->countCustomers('state:invited');
        $enabledCount = $shopify->countCustomers('state:enabled');
        $recentCustomers = $shopify->getCustomers([
            'limit' => 10,
            'created_at_min' => Carbon::now()->subWeek()->toDateString(),
            'fields' => 'id,first_name,last_name,email,state,created_at',
            'order' => 'created_at asc',
        ]);

        return response()->json([
            'counts' => [
                'customers' => data_get($customerCount, 'json.count', 0),
                'invited' => data_get($invitedCount, 'json.count', 0),
                'enabled' => data_get($enabledCount, 'json.count', 0),
            ],
            'recent_customers' => collect(data_get($recentCustomers, 'json.customers', []))->map(function ($customer) {
                return [
                    'id' => $customer['id'] ?? null,
                    'first_name' => $customer['first_name'] ?? '',
                    'last_name' => $customer['last_name'] ?? '',
                    'email' => $customer['email'] ?? '',
                    'state' => $customer['state'] ?? '',
                    'created_at' => $customer['created_at'] ?? '',
                ];
            })->toArray(),
        ]);
    }
}
