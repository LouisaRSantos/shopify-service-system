<?php

namespace App\Http\Controllers;

use App\Services\ShopifyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function summary(ShopifyService $shopify)
    {
        $weekAgo = Carbon::now()->subWeek()->toIso8601String();

        $customerCount = Cache::remember(
            'dashboard_customer_count',
            now()->addMinutes(10),
            fn() => data_get(
                $shopify->countCustomers(),
                'json.count',
                0
            )
        );
        $invitedCount = Cache::remember(
            'dashboard_invited_count',
            now()->addMinutes(5),
            fn() => $shopify->countCustomersByState('invited')
        );

        $enabledCount = Cache::remember(
            'dashboard_enabled_count',
            now()->addMinutes(5),
            fn() => $shopify->countCustomersByState('enabled')
        );
        $recentCustomers = $shopify->getCustomers([
            'limit' => 10,
            'created_at_min' => Carbon::now()->subWeek()->toDateString(),
            'fields' => 'id,first_name,last_name,email,state,created_at',
            'order' => 'created_at asc',
        ]);

        return response()->json([
            'counts' => [
                'customers' => $customerCount,
                'invited' => $invitedCount,
                'enabled' => $enabledCount,
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
