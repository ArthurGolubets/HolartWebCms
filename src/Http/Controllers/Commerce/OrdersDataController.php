<?php

namespace HolartWeb\HolartCMS\Http\Controllers\Commerce;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\TOrdersData;

class OrdersDataController extends Controller
{
    public function index()
    {
        $settings = TOrdersData::all()->mapWithKeys(function ($item) {
            return [$item->key => $item->getTypedValue()];
        });

        return response()->json($settings);
    }

    public function show($key)
    {
        $value = TOrdersData::getValue($key);

        return response()->json([
            'key' => $key,
            'value' => $value
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->settings as $key => $value) {
            $type = $this->detectType($value);
            TOrdersData::setValue($key, $value, $type);
        }

        return response()->json([
            'success' => true,
            'message' => 'Настройки сохранены успешно'
        ]);
    }

    public function update(Request $request, $key)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $type = $this->detectType($request->value);
        TOrdersData::setValue($key, $request->value, $type);

        return response()->json([
            'success' => true,
            'message' => 'Настройка обновлена успешно'
        ]);
    }

    public function destroy($key)
    {
        TOrdersData::where('key', $key)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Настройка удалена успешно'
        ]);
    }

    private function detectType($value): string
    {
        if (is_bool($value)) {
            return TOrdersData::TYPE_BOOLEAN;
        }

        if (is_int($value)) {
            return TOrdersData::TYPE_INTEGER;
        }

        if (is_array($value)) {
            return TOrdersData::TYPE_JSON;
        }

        if (is_string($value) && strlen($value) > 255) {
            return TOrdersData::TYPE_TEXT;
        }

        return TOrdersData::TYPE_STRING;
    }
}
