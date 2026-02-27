<?php

namespace HolartWeb\HolartCMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use HolartWeb\HolartCMS\Models\TPanelSettings;

class SettingsController extends Controller
{
    /**
     * Get all settings.
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();

        // Only super_admin and administrator can access settings
        if (!in_array($user->role->value, ['super_admin', 'administrator'])) {
            return response()->json(['message' => 'Доступ запрещен'], 403);
        }

        return response()->json(TPanelSettings::all_settings());
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $user = Auth::guard('admin')->user();

        // Only super_admin and administrator can update settings
        if (!in_array($user->role->value, ['super_admin', 'administrator'])) {
            return response()->json(['message' => 'Доступ запрещен'], 403);
        }

        $data = $request->all();

        foreach ($data as $key => $value) {
            $type = $this->getType($key);
            TPanelSettings::set($key, $value, $type);
        }

        return response()->json(['message' => 'Настройки сохранены']);
    }

    /**
     * Get type for setting key.
     */
    protected function getType(string $key): string
    {
        return match ($key) {
            'phones', 'addresses' => 'array',
            default => 'string',
        };
    }
}
