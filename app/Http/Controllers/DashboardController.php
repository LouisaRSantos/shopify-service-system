<?php

namespace App\Http\Controllers;

use App\Services\ShopifyService;
use App\Services\SystemConfigService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function summary(ShopifyService $shopify)
    {
        $config = app(SystemConfigService::class);

        $customerCacheMinutes = $config->getCached('cache_customer_count_minutes', 10);
        $invitedCacheMinutes  = $config->getCached('cache_invited_count_minutes', 5);
        $enabledCacheMinutes  = $config->getCached('cache_enabled_count_minutes', 5);

        $weekAgo = Carbon::now()->subWeek()->toIso8601String();

        $customerCount = Cache::remember(
            'dashboard_customer_count',
            now()->addMinutes($customerCacheMinutes),
            fn() => data_get($shopify->countCustomers(), 'json.count', 0)
        );

        $invitedCount = Cache::remember(
            'dashboard_invited_count',
            now()->addMinutes($invitedCacheMinutes),
            fn() => $shopify->countCustomersByState('invited')
        );

        $enabledCount = Cache::remember(
            'dashboard_enabled_count',
            now()->addMinutes($enabledCacheMinutes),
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
