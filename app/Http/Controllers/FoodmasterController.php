<?php

namespace App\Http\Controllers;

use App\Models\FoodMaster;
use Illuminate\Http\Request;

class FoodMasterController extends Controller
{
    public function index(Request $request)
    {
        $query = FoodMaster::query();

        if ($request->filled('search')) {
            $query->where('food_name', 'like', '%' . $request->search . '%');
        }

        $foods = $query->orderBy('food_name')->paginate(30)->withQueryString();

        return view('food_master.index', compact('foods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
  public function store(Request $request)
    {
        $validated = $request->validate([
            'food_name' => 'required|string|max:255',
            'calories'  => 'required|integer|min:0|max:9999',
            'protein'   => 'required|numeric|min:0|max:999',
            'fat'       => 'required|numeric|min:0|max:999',
            'carb'      => 'required|numeric|min:0|max:999',
        ]);

        FoodMaster::create($validated);

        return back()->with('success', "「{$validated['food_name']}」を追加しました。");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, FoodMaster $foodMaster)
    {
        $validated = $request->validate([
            'food_name' => 'required|string|max:255',
            'calories'  => 'required|integer|min:0|max:9999',
            'protein'   => 'required|numeric|min:0|max:999',
            'fat'       => 'required|numeric|min:0|max:999',
            'carb'      => 'required|numeric|min:0|max:999',
        ]);

        $foodMaster->update($validated);

        return back()->with('success', '食品情報を更新しました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FoodMaster $foodMaster)
    {
        $foodMaster->delete();
        return back()->with('success', '食品を削除しました。');
    }
}
