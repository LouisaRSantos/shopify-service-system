<?php

namespace App\Services;

use App\Models\SystemSetting;
use App\Models\SystemConfigLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemConfigService
{
    public function getAll()
    {
        return SystemSetting::all();
    }

    public function get($key, $default = null)
    {
        $value = SystemSetting::where('setting_key', $key)->first();

        return $value ? $value->setting_value : $default;
    }

    public function update($key, $newValue, $userId = null)
    {
        $setting = SystemSetting::where('setting_key', $key)->first();

        if (!$setting) {
            return false;
        }

        $oldValue = $setting->setting_value;

        $setting->update([
            'setting_value' => $newValue
        ]);

        SystemConfigLog::create([
            'setting_key' => $key,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_by' => $userId,
            'changed_at' => now(),
        ]);

        // optional: clear cache later (Phase 4)
        Cache::forget("config_{$key}");

        return true;
    }

    public function bulkUpdate(array $settings, $userId = null)
    {
        $updated = [];

        DB::beginTransaction();

        try {

            foreach ($settings as $item) {

                $key = $item['name'];
                $newValue = $item['value'];

                $setting = SystemSetting::where('setting_key', $key)->first();

                if (!$setting) {
                    continue;
                }

                $oldValue = $setting->setting_value;

                // skip if no change
                if ((string)$oldValue === (string)$newValue) {
                    continue;
                }

                $setting->update([
                    'setting_value' => $newValue
                ]);

                SystemConfigLog::create([
                    'setting_key' => $key,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'changed_by' => $userId,
                    'changed_at' => now(),
                ]);

                $updated[] = [
                    'key' => $key,
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }

            DB::commit();

            return $updated;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getCached($key, $default = null)
    {
        return Cache::remember("system_config_{$key}", now()->addMinutes(60), function () use ($key, $default) {
            return $this->get($key, $default);
        });
    }
}