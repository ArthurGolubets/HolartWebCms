<?php

namespace HolartWeb\HolartCMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use HolartWeb\HolartCMS\Models\TPanelSettings;

class SettingsController extends Controller
{
    /**
     * Get all settings.
     */
    public function index()
    {
        return response()->json(TPanelSettings::all_settings());
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
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
