<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">食品マスタ管理</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- 新規追加フォーム --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">
                    食品を追加
                </h3>
                <form action="{{ route('food-master.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                        <div class="col-span-2">
                            <input type="text" name="food_name"
                                   value="{{ old('food_name') }}"
                                   placeholder="食品名・料理名"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                            @error('food_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <input type="number" name="calories"
                                   value="{{ old('calories') }}"
                                   placeholder="kcal" min="0" max="9999"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                            @error('calories')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <input type="number" name="protein" step="0.1"
                                   value="{{ old('protein') }}"
                                   placeholder="P(g)" min="0"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                        <div>
                            <input type="number" name="fat" step="0.1"
                                   value="{{ old('fat') }}"
                                   placeholder="F(g)" min="0"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                        <div>
                            <input type="number" name="carb" step="0.1"
                                   value="{{ old('carb') }}"
                                   placeholder="C(g)" min="0"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="submit"
                                class="bg-blue-600 text-white px-5 py-2 rounded-lg
                                       text-sm font-medium hover:bg-blue-700">
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
                       class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-300">
                <button type="submit"
                        class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg
                               text-sm hover:bg-gray-200">
                    検索
                </button>
                @if(request('search'))
                <a href="{{ route('food-master.index') }}"
                   class="border border-gray-200 text-gray-500 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                    クリア
                </a>
                @endif
            </form>

            {{-- 食品一覧 --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-400 uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">食品名</th>
                            <th class="px-4 py-3 text-right">kcal</th>
                            <th class="px-4 py-3 text-right">P(g)</th>
                            <th class="px-4 py-3 text-right">F(g)</th>
                            <th class="px-4 py-3 text-right">C(g)</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($foods as $food)
                        <tr class="hover:bg-gray-50" id="food-{{ $food->id }}">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $food->food_name }}
                            </td>
                            <td class="px-4 py-3 text-right text-gray-700 font-medium">
                                {{ number_format($food->calories) }}
                            </td>
                            <td class="px-4 py-3 text-right text-blue-600">
                                {{ $food->protein }}
                            </td>
                            <td class="px-4 py-3 text-right text-amber-600">
                                {{ $food->fat }}
                            </td>
                            <td class="px-4 py-3 text-right text-green-600">
                                {{ $food->carb }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form action="{{ route('food-master.destroy', $food) }}"
                                      method="POST"
                                      onsubmit="return confirm('「{{ $food->food_name }}」を削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-400 hover:text-red-600 text-xs">
                                        削除
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                食品が登録されていません。
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ページネーション --}}
            <div>{{ $foods->links() }}</div>

        </div>
    </div>
</x-app-layout>