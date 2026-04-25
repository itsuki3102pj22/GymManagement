<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    // 責任者のみアクセス可
    private function authorizeSupervisor(Request $request): void
    {
        if (! $request->user()->isSupervisor()) {
            abort(403, '責任者のみアクセスできます。');
        }
    }

    // ユーザー一覧
    public function index(Request $request)
    {
        $this->authorizeSupervisor($request);

        $users = User::withCount('clients')
            ->orderBy('role', 'desc')
            ->orderBy('name')
            ->get();

        return view('settings.users.index', compact('users'));
    }

    // ユーザー作成フォーム
    public function create(Request $request)
    {
        $this->authorizeSupervisor($request);
        return view('settings.users.create');
    }

    // ユーザー作成処理
    public function store(Request $request)
    {
        $this->authorizeSupervisor($request);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:1,2',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', "{$validated['name']} さんのアカウントを作成しました。");
    }

    // ユーザー編集フォーム
    public function edit(Request $request, User $user)
    {
        $this->authorizeSupervisor($request);
        return view('settings.users.edit', compact('user'));
    }

    // ユーザー更新処理
    public function update(Request $request, User $user)
    {
        $this->authorizeSupervisor($request);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:1,2',
        ]);

        $user->update($validated);

        // パスワードが入力された場合のみ更新
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Password::min(8)],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()
            ->route('users.index')
            ->with('success', "{$user->name} さんの情報を更新しました。");
    }

    // ユーザー削除
    public function destroy(Request $request, User $user)
    {
        $this->authorizeSupervisor($request);

        // 自分自身は削除不可
        if ($user->id === $request->user()->id) {
            return back()->with('error', '自分自身は削除できません。');
        }

        // 担当顧客がいる場合は削除不可
        if ($user->clients()->exists()) {
            return back()->with('error', '担当顧客がいるため削除できません。先に担当を変更してください。');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "{$name} さんのアカウントを削除しました。");
    }
}