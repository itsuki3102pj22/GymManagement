<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FoodLog;
use App\Models\FoodMaster;
use App\Services\NutritionService;
use Illuminate\Http\Request;

class FoodLogController extends Controller
{
    public function __construct(
        private NutritionService $nutrition,
    ) {}

    // 食事ログ一覧
    public function index(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        $logs = FoodLog::where('client_id', $client->id)
            ->orderByDesc('logged_at')
            ->paginate(20);

        $eer = $this->nutrition->calcEer($client);

        return view('food_logs.index', compact('client', 'logs', 'eer'));
    }

    // 食事記録フォーム
    public function create(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        $foods = FoodMaster::orderBy('food_name')->get();
        $eer   = $this->nutrition->calcEer($client);

        return view('food_logs.create', compact('client', 'foods', 'eer'));
    }

    // 食事記録保存
    public function store(Request $request, Client $client)
    {
        $this->authorizeClient($request, $client);

        $validated = $request->validate([
            'logged_at'   => 'required|date',
            'meal_text'   => 'required|string',
            'food_ids'    => 'nullable|array',
            'food_ids.*'  => 'exists:food_master,id',
            'quantities'  => 'nullable|array', //数量
            'quantities.*'=> 'numeric|min:0.1|max:10',
        ]);

        // 選択された食品からカロリー・PFCを合算
        $totalCalories = 0;
        $totalProtein  = 0;
        $totalFat      = 0;
        $totalCarb     = 0;

        if (! empty($validated['food_ids'])) {
            foreach ($validated['food_ids'] as $index => $foodId) {
                $food     = FoodMaster::find($foodId);
                $quantity = $validated['quantities'][$index] ?? 1.0;

                $totalCalories += $food->calories  * $quantity;
                $totalProtein  += $food->protein   * $quantity;
                $totalFat      += $food->fat       * $quantity;
                $totalCarb     += $food->carb      * $quantity;
            }
        }

        // PFCバランス（%エネルギー）算出
        $pBalance = $totalCalories > 0
            ? round(($totalProtein * 4 / $totalCalories) * 100, 1) : 0;
        $fBalance = $totalCalories > 0
            ? round(($totalFat     * 9 / $totalCalories) * 100, 1) : 0;
        $cBalance = $totalCalories > 0
            ? round(($totalCarb    * 4 / $totalCalories) * 100, 1) : 0;

        FoodLog::create([
            'client_id'      => $client->id,
            'meal_text'      => $validated['meal_text'],
            'total_calories' => (int) round($totalCalories),
            'p_balance'      => $pBalance,
            'f_balance'      => $fBalance,
            'c_balance'      => $cBalance,
            'protein_grams'  => round($totalProtein, 1),
            'fat_grams'      => round($totalFat, 1),
            'carbs_grams'    => round($totalCarb, 1),
            'logged_at'      => $validated['logged_at'],
        ]);

        return redirect()
            ->route('food-logs.index', $client)
            ->with('success', '食事を記録しました。');
    }

    // 削除
    public function destroy(Request $request, Client $client, FoodLog $foodLog)
    {
        $this->authorizeClient($request, $client);
        abort_if($foodLog->client_id !== $client->id, 403);
        $foodLog->delete();

        return back()->with('success', '食事ログを削除しました。');
    }

    private function authorizeClient(Request $request, Client $client): void
    {
        $user = $request->user();
        if ($user->isTrainer() && $client->trainer_id !== $user->id) {
            abort(403);
        }
    }
}