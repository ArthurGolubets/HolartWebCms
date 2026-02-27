<?php

namespace HolartWeb\HolartCMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use HolartWeb\HolartCMS\Models\TPanelSettings;
use HolartWeb\HolartCMS\Models\TAdminAction;

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

        // Get old settings for logging
        $oldSettings = TPanelSettings::all_settings();
        $changes = [];

        foreach ($data as $key => $value) {
            $type = $this->getType($key);
            $oldValue = $oldSettings[$key] ?? null;

            // Track changes
            if ($oldValue !== $value) {
                $changes[$key] = ['old' => $oldValue, 'new' => $value];
            }

            TPanelSettings::set($key, $value, $type);
        }

        // Log activity if there were changes
        if (!empty($changes)) {
            $changedKeys = implode(', ', array_keys($changes));
            TAdminAction::log('changed', 'setting', null, 'Изменены настройки: ' . $changedKeys, $changes);
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
