<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::orderBy('category')->orderBy('name')->get()
            ->groupBy('category');

        return view('menus.index', compact('menus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'category' => 'required|string|max:100',
        ]);

        $validated['is_custom'] = true;

        Menu::create($validated);

        return back()->with('success', "「{$validated['name']}」を追加しました。");
    }

    public function destroy(Menu $menu)
    {
        // ログが紐づいている種目は削除不可
        if ($menu->workoutLogs()->exists()) {
            return back()->with('error', 'この種目はログに使用されているため削除できません。');
        }

        $menu->delete();

        return back()->with('success', '種目を削除しました。');
    }
}