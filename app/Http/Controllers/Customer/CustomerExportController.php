<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class CustomerExportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return view('customers.export.content');
        }

        return view('customers.export.index');
    }

    public function start(Request $request)
    {
        session([
            'export_type' => $request->type,
            'export_columns' => $request->columns ?? [],
        ]);

        $shopify = app(\App\Services\ShopifyService::class);

        /*
        |--------------------------------------------------------------------------
        | 1. EXPORT BY IDS (REST)
        |--------------------------------------------------------------------------
        */
        if ($request->type === "ids") {

            $idsRaw = $request->ids ?? '';

            if (empty($idsRaw)) {
                return response()->json([
                    "status" => "error",
                    "message" => "Customer IDs are required"
                ]);
            }

            $ids = array_filter(array_map('trim', explode(',', $idsRaw)));

            if (empty($ids)) {
                return response()->json([
                    "status" => "error",
                    "message" => "Invalid IDs format"
                ]);
            }

            $result = $shopify->getCustomersByIds($ids);

            if ($result['status'] !== 200) {
                return response()->json([
                    "status" => "error",
                    "message" => "Failed to fetch customers by IDs",
                    "debug" => $result['body']
                ]);
            }

            $customersRaw = $result['json']['customers'] ?? [];

            if (empty($customersRaw)) {
                return response()->json([
                    "status" => "error",
                    "message" => "No customers found for given IDs"
                ]);
            }

            $customers = array_map([$this, 'normalizeCustomer'], $customersRaw);

            if (empty($customers)) {
                return response()->json([
                    "status" => "error",
                    "message" => "No customers found for given IDs"
                ]);
            }

            session([
                'export_customers' => $customers,
                'export_mode' => 'ids'
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Customers fetched by IDs. Generating export..."
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. EXPORT BY EMAIL (REST SEARCH)
        |--------------------------------------------------------------------------
        */
        if ($request->type === "email") {

            $email = trim($request->email ?? '');

            if (empty($email)) {
                return response()->json([
                    "status" => "error",
                    "message" => "Email is required"
                ]);
            }

            $result = $shopify->searchCustomerByEmail($email);

            if ($result['status'] !== 200) {
                return response()->json([
                    "status" => "error",
                    "message" => "Failed to search customer by email",
                    "debug" => $result['body']
                ]);
            }

            $customersRaw = $result['json']['customers'] ?? [];

            if (empty($customersRaw)) {
                return response()->json([
                    "status" => "error",
                    "message" => "No customer found for this email"
                ]);
            }

            $customers = array_map([$this, 'normalizeCustomer'], $customersRaw);

            // OPTIONAL strict match filter (recommended)
            $customers = array_values(array_filter($customers, function ($c) use ($email) {
                return isset($c['email']) && strtolower($c['email']) === strtolower($email);
            }));

            if (empty($customers)) {
                return response()->json([
                    "status" => "error",
                    "message" => "No customer found for this email"
                ]);
            }

            session([
                'export_customers' => $customers,
                'export_mode' => 'email'
            ]);

            return response()->json([
                "status" => "success",
                "message" => "Customer fetched by email. Generating export..."
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. DEFAULT (BULK GRAPHQL EXPORT)
        |--------------------------------------------------------------------------
        */
        $queryFilter = "";

        if ($request->type === "state") {

            $state = trim($request->state ?? '');

            if (empty($state)) {
                return response()->json([
                    "status" => "error",
                    "message" => "State is required (enabled/invited)"
                ]);
            }

            // optional safety whitelist
            $allowed = ['enabled', 'invited', 'disabled'];

            if (!in_array($state, $allowed)) {
                return response()->json([
                    "status" => "error",
                    "message" => "Invalid state value"
                ]);
            }

            $queryFilter = "state:{$state}";
        }

        $result = $shopify->startCustomerBulkExport($queryFilter);

        $bulk = $result['json']['data']['bulkOperationRunQuery'] ?? null;

        if (!$bulk) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid Shopify response"
            ]);
        }

        if (!empty($bulk['userErrors'])) {
            return response()->json([
                "status" => "error",
                "message" => "Bulk export failed",
                "errors" => $bulk['userErrors']
            ]);
        }

        return response()->json([
            "status" => "success",
            "message" => "Bulk export started. Please wait..."
        ]);
    }

    public function status()
    {
        $mode = session('export_mode');

        // =========================
        // IDS MODE
        // =========================
        if (in_array($mode, ['ids', 'email'])) {

            $customers = session('export_customers', []);

            if (empty($customers)) {
                return response()->json([
                    "status" => "error",
                    "message" => "No cached customers found"
                ]);
            }

            $fileName = $this->generateExcel($customers);

            session()->forget(['export_customers', 'export_mode']);

            return response()->json([
                "status" => "COMPLETED",
                "download" => "/customers/export/download?file=" . urlencode($fileName)
            ]);
        }

        $shopify = app(\App\Services\ShopifyService::class);

        $result = $shopify->getBulkExportStatus();

        $op = $result['json']['data']['currentBulkOperation'] ?? null;

        if (!$op) {
            return response()->json([
                "status" => "error",
                "message" => "No bulk operation"
            ]);
        }

        if ($op['status'] !== "COMPLETED") {
            return response()->json([
                "status" => $op['status']
            ]);
        }

        $url = $op['url'] ?? null;

        if (!$url) {
            return response()->json([
                "status" => "error",
                "message" => "No export URL"
            ]);
        }

        $jsonl = file_get_contents($url);

        if (!$jsonl) {
            return response()->json([
                "status" => "error",
                "message" => "Failed downloading export"
            ]);
        }

        $lines = explode("\n", trim($jsonl));

        $customers = [];

        foreach ($lines as $line) {
            $decoded = json_decode($line, true);
            if ($decoded) {
                $customers[] = $decoded;
            }
        }

        $fileName = $this->generateExcel($customers);

        return response()->json([
            "status" => "COMPLETED",
            "download" => "/customers/export/download?file=" . urlencode($fileName)
        ]);
    }

    public function download(Request $request)
    {
        $file = $request->file;

        $path = storage_path("app/exports/" . $file);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    private function generateExcel($customers)
    {
        $columns = session('export_columns', []);

        $columnMap = [
            "id" => "ID",
            "email" => "Email",
            "first_name" => "First Name",
            "last_name" => "Last Name",
            "phone" => "Phone",
            "state" => "State",
            "orders_count" => "Orders",
            "total_spent" => "Total Spent",
            "created_at" => "Created At",
            "updated_at" => "Updated At",
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $colIndex = 1;

        foreach ($columns as $col) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . 1;
            $sheet->setCellValue($cell, $columnMap[$col] ?? $col);
            $colIndex++;
        }

        $row = 2;

        foreach ($customers as $customer) {

            $colIndex = 1;

            foreach ($columns as $col) {

                switch ($col) {

                    case 'first_name':
                        $value = $customer['firstName'] ?? '';
                        break;

                    case 'last_name':
                        $value = $customer['lastName'] ?? '';
                        break;

                    case 'created_at':
                        $value = $customer['createdAt'] ?? '';
                        break;

                    case 'updated_at':
                        $value = $customer['updatedAt'] ?? '';
                        break;

                    case 'orders_count':
                        $value = $customer['numberOfOrders'] ?? '';
                        break;

                    case 'total_spent':
                        $value = $customer['amountSpent']['amount'] ?? '';
                        break;

                    default:
                        $value = $customer[$col] ?? '';
                        break;
                }

                if ($col === 'id') {
                    $value = str_replace('gid://shopify/Customer/', '', $value);
                }


                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex) . $row;
                $sheet->setCellValue($cell, $value);
                $colIndex++;
            }

            $row++;
        }

        $fileName = "customers_export_" . time() . ".xlsx";
        $path = storage_path("app/exports/" . $fileName);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($path);

        session(['export_file' => $fileName]);

        return $fileName;
    }

    private function normalizeCustomer($c)
    {
        return [
            'id' => str_replace('gid://shopify/Customer/', '', $c['id'] ?? ''),
            'email' => $c['email'] ?? '',
            'firstName' => $c['first_name'] ?? '',
            'lastName' => $c['last_name'] ?? '',
            'phone' => $c['phone'] ?? '',
            'state' => $c['state'] ?? '',
            'numberOfOrders' => $c['orders_count'] ?? '',
            'amountSpent' => [
                'amount' => $c['total_spent'] ?? '',
            ],
            'createdAt' => $c['created_at'] ?? '',
            'updatedAt' => $c['updated_at'] ?? '',
        ];
    }

}
