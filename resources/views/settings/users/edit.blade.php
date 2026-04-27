<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('users.index') }}"
               class="text-sm" style="color:var(--text-muted)">← ユーザー管理</a>
            <h2 class="font-display mt-1"
                style="font-size:26px;font-weight:400;color:var(--navy);letter-spacing:1px">
                {{ $user->name }} さんを編集
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl p-6 bg-white"
                 style="border:1px solid var(--card-border);border-top:3px solid var(--gold)">
                <p class="text-xs font-semibold tracking-widest uppercase mb-5"
                   style="color:var(--gold)">アカウント情報を更新</p>
                <form action="{{ route('users.update', $user) }}"
                      method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    @include('settings.users._form', ['user' => $user])

                    <div class="rounded-xl p-4 space-y-4"
                         style="background:var(--surface);border:1px solid var(--card-border)">
                        <p class="text-xs" style="color:var(--text-muted)">
                            パスワードを変更する場合のみ入力してください
                        </p>
                        <div>
                            <label class="block text-xs font-semibold mb-1.5"
                                   style="color:var(--text-muted)">新しいパスワード</label>
                            <input type="password" name="password"
                                   class="w-full rounded-xl px-4 py-2.5 text-sm"
                                   style="border:1px solid var(--card-border);color:var(--navy)">
                            @error('password')
                            <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold mb-1.5"
                                   style="color:var(--text-muted)">新しいパスワード（確認）</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full rounded-xl px-4 py-2.5 text-sm"
                                   style="border:1px solid var(--card-border);color:var(--navy)">
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2"
                         style="border-top:1px solid var(--card-border)">
                        <a href="{{ route('users.index') }}"
                           class="px-5 py-2 rounded-xl text-sm"
                           style="border:1px solid var(--card-border);color:var(--text-secondary)">
                            キャンセル
                        </a>
                        <button type="submit"
                                class="px-6 py-2 rounded-xl text-sm font-semibold text-white"
                                style="background:var(--royal)">
                            更新する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>