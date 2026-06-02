<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ShopifyService;
use Illuminate\Support\Facades\Storage;

class CustomerImportController extends Controller
{
    protected $shopify;

    public function __construct(ShopifyService $shopify)
    {
        $this->shopify = $shopify;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            return view('customers.import.content');
        }

        return view('customers.import.index');
    }

    public function downloadTemplate()
    {
        $headers = ['first_name', 'last_name', 'email'];

        $fileName = 'customer_template.csv';

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        rewind($handle);

        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename={$fileName}");
    }

    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $rows = array_map('str_getcsv', file($path));
        $header = array_shift($rows);

        $created = 0;
        $failed = [];

        foreach ($rows as $index => $row) {

            $data = array_combine($header, $row);

            $payload = [
                'customer' => [
                    'first_name' => $data['first_name'] ?? null,
                    'last_name'  => $data['last_name'] ?? null,
                    'email'      => $data['email'] ?? null,
                ]
            ];

            $response = $this->shopify->createCustomerFromArray($data);

            if (($response['status'] ?? 0) === 201) {
                $created++;
            } else {
                $failed[] = [
                    'row' => $index + 2,
                    'error' => $response['body'] ?? 'Unknown error'
                ];
            }
        }

        return response()->json([
            'created' => $created,
            'failed' => $failed
        ]);
    }
}
