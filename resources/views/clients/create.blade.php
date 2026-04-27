<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('clients.index') }}"
               class="text-sm" style="color:var(--text-muted)">← 顧客一覧</a>
            <h2 class="font-display mt-1"
                style="font-size:26px;font-weight:400;color:var(--navy);letter-spacing:1px">
                顧客新規登録
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl p-6 bg-white"
                 style="border:1px solid var(--card-border);border-top:3px solid var(--gold)">
                <p class="text-xs font-semibold tracking-widest uppercase mb-5"
                   style="color:var(--gold)">基本情報を入力</p>
                <form action="{{ route('clients.store') }}" method="POST" class="space-y-5">
                    @csrf
                    @include('clients._form', ['client' => null])
                    <div class="flex justify-end gap-3 pt-2"
                         style="border-top:1px solid var(--card-border)">
                        <a href="{{ route('clients.index') }}"
                           class="px-5 py-2 rounded-xl text-sm transition"
                           style="border:1px solid var(--card-border);color:var(--text-secondary)">
                            キャンセル
                        </a>
                        <button type="submit"
                                class="px-6 py-2 rounded-xl text-sm font-semibold text-white"
                                style="background:var(--royal)">
                            登録する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>