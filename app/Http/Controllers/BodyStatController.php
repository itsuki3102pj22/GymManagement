<?php

namespace App\Http\Controllers;

use App\Models\BodyStat;
use App\Models\Client;
use App\Services\NutritionService;
use Illuminate\Http\Request;

class BodyStatController extends Controller
{
    public function __construct(
        private NutritionService $nutrition,
    ) {}

    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'weight'              => 'required|numeric|min:20|max:300',
            'body_fat_percentage' => 'nullable|numeric|min:1|max:70',
            'muscle_mass'         => 'nullable|numeric|min:10|max:150',
            'measured_at'         => 'required|date',
        ]);

        // BMIを自動算出
        $validated['bmi'] = $this->nutrition->calcBmi(
            $validated['weight'],
            $client->height
        );
        $validated['client_id'] = $client->id;

        BodyStat::create($validated);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', '計測データを記録しました。');
    }

    public function destroy(Client $client, BodyStat $bodyStat)
    {
        abort_if($bodyStat->client_id !== $client->id, 403);
        $bodyStat->delete();

        return redirect()
            ->route('clients.show', $client)
            ->with('success', '計測データを削除しました。');
    }
}