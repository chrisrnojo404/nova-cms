<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function public(): JsonResponse
    {
        $groupedSettings = Setting::query()
            ->where('is_public', true)
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group')
            ->map(function ($settings) {
                return $settings->mapWithKeys(function (Setting $setting): array {
                    return [
                        $setting->key => $setting->value['value'] ?? null,
                    ];
                });
            });

        return response()->json([
            'data' => $groupedSettings,
        ]);
    }
}
