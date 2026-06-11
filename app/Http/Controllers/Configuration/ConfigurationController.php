<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SystemConfigService;

class ConfigurationController extends Controller
{
    protected $config;

    public function __construct(SystemConfigService $config)
    {
        $this->config = $config;
    }

    /**
     * GET /api/configuration
     */
    public function index()
    {
        $settings = $this->config->getAll();

        return response()->json([
            'settings' => $settings
        ]);
    }

    /**
     * POST /configuration/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.name' => 'required|string',
            'settings.*.value' => 'required'
        ]);

        $userId = session('web_user_id');

        $result = $this->config->bulkUpdate(
            $request->settings,
            $userId
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Configuration updated successfully',
            'updated' => $result
        ]);
    }
}