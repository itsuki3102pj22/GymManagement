<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display"
            style="font-size:26px;font-weight:400;color:var(--navy);letter-spacing:1px">
            食品マスタ管理
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- 追加フォーム --}}
            <div class="rounded-2xl p-5 bg-white"
                 style="border:1px solid var(--card-border);border-top:3px solid var(--gold)">
                <p class="text-xs font-semibold tracking-widest uppercase mb-4"
                   style="color:var(--gold)">食品を追加</p>
                <form action="{{ route('food-master.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                        <div class="col-span-2">
                            <input type="text" name="food_name"
                                   value="{{ old('food_name') }}"
                                   placeholder="食品名・料理名"
                                   class="w-full rounded-xl px-3 py-2.5 text-sm"
                                   style="border:1px solid var(--card-border);color:var(--navy)">
                            @error('food_name')
                            <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
                            @enderror
                        </div>
                        @foreach([
                            ['calories', 'kcal',  'integer'],
                            ['protein',  'P (g)',  'decimal'],
                            ['fat',      'F (g)',  'decimal'],
                            ['carb',     'C (g)',  'decimal'],
                        ] as [$name, $ph, $type])
                        <div>
                            <input type="number" name="{{ $name }}"
                                   value="{{ old($name) }}"
                                   placeholder="{{ $ph }}" min="0"
                                   step="{{ $type === 'decimal' ? '0.1' : '1' }}"
                                   class="w-full rounded-xl px-3 py-2.5 text-sm text-center"
                                   style="border:1px solid var(--card-border);color:var(--navy)">
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="submit"
                                class="px-5 py-2 rounded-xl text-sm font-semibold text-white"
                                style="background:var(--royal)">
                            追加
                        </button>
                    </div>
                </form>
            </div>

            {{-- 検索 --}}
            <form method="GET" action="{{ route('food-master.index') }}"
                  class="flex gap-2">
                <input type="text" name="search"
                       value="{{ request('search') }}"
                       placeholder="食品名で検索..."
                       class="flex-1 rounded-xl px-4 py-2.5 text-sm"
                       style="border:1px solid var(--card-border);color:var(--navy)">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-medium transition"
                        style="background:var(--blue-bg);color:var(--royal);
                               border:1px solid var(--blue-border)">
                    検索
                </button>
                @if(request('search'))
                <a href="{{ route('food-master.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm transition"
                   style="border:1px solid var(--card-border);color:var(--text-secondary)">
                    クリア
                </a>
                @endif
            </form>

            {{-- 一覧 --}}
            <div class="rounded-2xl overflow-hidden bg-white"
                 style="border:1px solid var(--card-border)">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:var(--surface);border-bottom:1px solid var(--card-border)">
                            @foreach(['食品名','kcal','P (g)','F (g)','C (g)',''] as $h)
                            <th class="px-5 py-3 text-left text-xs font-semibold tracking-widest uppercase"
                                style="color:var(--text-muted)">{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($foods as $food)
                        <tr style="border-bottom:1px solid var(--card-border)"
                            onmouseover="this.style.background='var(--surface)'"
                            onmouseout="this.style.background=''">
                            <td class="px-5 py-3 font-medium" style="color:var(--navy)">
                                {{ $food->food_name }}
                            </td>
                            <td class="px-5 py-3 font-bold" style="color:var(--royal)">
                                {{ number_format($food->calories) }}
                            </td>
                            <td class="px-5 py-3" style="color:var(--royal)">{{ $food->protein }}</td>
                            <td class="px-5 py-3" style="color:var(--gold)">{{ $food->fat }}</td>
                            <td class="px-5 py-3" style="color:var(--green-dark)">{{ $food->carb }}</td>
                            <td class="px-5 py-3 text-right">
                                <form action="{{ route('food-master.destroy', $food) }}"
                                      method="POST"
                                      onsubmit="return confirm('「{{ $food->food_name }}」を削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs px-3 py-1 rounded-lg transition"
                                            style="color:#dc2626;border:1px solid #fecaca;background:#fef2f2">
                                        削除
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center"
                                style="color:var(--text-muted)">
                                食品が登録されていません。
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $foods->links() }}</div>
        </div>
    </div>
</x-app-layout>