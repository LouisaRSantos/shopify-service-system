<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ShopifyService;
use App\Models\CustomerActivityLog;
use Illuminate\Support\Carbon;

class CustomerCreateController extends Controller
{
    private ShopifyService $shopify;

    public function __construct(ShopifyService $shopify)
    {
        $this->shopify = $shopify;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return view('customers.create.content');
        }

        return view('customers.create.index');
    }

    public function store(Request $request)
    {
        $log = $this->logActivityStart(
            'manual',
            $request->all()
        );

        if (!$request->ajax()) {
            $this->logActivityFail(
                $log->id,
                'Invalid request type'
            );
            return response()->json([
                "status" => "error",
                "message" => "Invalid request"
            ], 400);
        }

        $first = $request->input('first_name');
        $last  = $request->input('last_name');
        $email = $request->input('email');

        if (empty($email)) {
            $this->logActivityFail(
                $log->id,
                'Email is required'
            );

            return response()->json([
                "status" => "error",
                "message" => "Email is required"
            ]);
        }

        // -----------------------------
        // DOMAIN VALIDATION (same logic)
        // -----------------------------
        $allowedDomains = [
            "@bounty.com.ph",
            "@bvapcloud.com"
        ];

        $allowed = false;

        foreach ($allowedDomains as $domain) {
            if (str_ends_with($email, $domain)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            $this->logActivityFail(
                $log->id,
                'Email must end with @bounty.com.ph or @bvapcloud.com'
            );
            return response()->json([
                "status" => "error",
                "message" => "Email must end with @bounty.com.ph or @bvapcloud.com"
            ]);
        }

        // -----------------------------
        // CREATE CUSTOMER
        // -----------------------------
        $response = $this->shopify->createCustomer($first, $last, $email);
        $customer = $response['json'];

        // -----------------------------
        // SHOPIFY ERROR HANDLING (MIGRATED)
        // -----------------------------
        if (isset($customer['errors'])) {

            $rawError = $customer['errors'];
            $errorMessage = "Something went wrong";

            if (isset($rawError['email'][0])) {

                $emailError = $rawError['email'][0];

                switch ($emailError) {

                    case "has already been taken":
                        $errorMessage = "This customer is already registered. Please use a different email.";
                        break;

                    default:
                        $errorMessage = $emailError;
                        break;
                }
            }

            $this->logActivityFail(
                $log->id,
                $errorMessage
            );

            return response()->json([
                "status" => "error",
                "message" => $errorMessage
            ]);
        }

        // -----------------------------
        // VALIDATION: SUCCESS CHECK
        // -----------------------------
        if (!isset($customer['customer']['id'])) {
            $this->logActivityFail(
                $log->id,
                "Failed to create customer"
            );
            return response()->json([
                "status" => "error",
                "message" => "Failed to create customer"
            ]);
        }

        // -----------------------------
        // SEND INVITE
        // -----------------------------
        $customerId = $customer['customer']['id'];
        $this->shopify->sendInvite($customerId);

        $this->logActivitySuccess(
            $log->id,
            $customer,
            1
        );

        return response()->json([
            "status" => "success",
            "message" => "Customer created and activation email sent"
        ]);
    }

    private function logActivityStart($type, $payload)
    {
        return CustomerActivityLog::create([
            'user_id' => session('web_user_id'),
            'activity_type' => $type,
            'status' => 'started',
            'payload' => $payload,
            'started_at' => Carbon::now(),
        ]);
    }

    private function logActivitySuccess($logId, $response = [], $count = 1)
    {
        CustomerActivityLog::where('id', $logId)->update([
            'status' => 'completed',
            'response_payload' => $response,
            'count_added' => $count,
            'completed_at' => Carbon::now(),
        ]);
    }

    private function logActivityFail($logId, $message)
    {
        CustomerActivityLog::where('id', $logId)->update([
            'status' => 'failed',
            'response_payload' => [
                'message' => $message
            ],
            'completed_at' => Carbon::now(),
        ]);
    }
}