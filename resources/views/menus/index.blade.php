<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">種目マスタ管理</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
            @endif

            {{-- 新規追加フォーム --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">
                    種目を追加
                </h3>
                <form action="{{ route('menus.store') }}" method="POST">
                    @csrf
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <input type="text" name="name"
                                   value="{{ old('name') }}"
                                   placeholder="種目名（例：ケトルベルスイング）"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                            @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="w-40">
                            <input type="text" name="category"
                                   value="{{ old('category') }}"
                                   placeholder="カテゴリ（例：体幹）"
                                   list="category-list"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                            <datalist id="category-list">
                                @foreach($menus->keys() as $cat)
                                <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                            @error('category')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                class="bg-blue-600 text-white px-5 py-2 rounded-lg
                                       text-sm font-medium hover:bg-blue-700 whitespace-nowrap">
                            追加
                        </button>
                    </div>
                </form>
            </div>

            {{-- カテゴリ別一覧 --}}
            @foreach($menus as $category => $items)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-3 bg-gray-50 border-b">
                    <h3 class="font-semibold text-gray-600 text-sm">{{ $category }}</h3>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50">
                        @foreach($items as $menu)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-800">
                                {{ $menu->name }}
                                @if($menu->is_custom)
                                <span class="ml-2 text-xs bg-blue-100 text-blue-600
                                             px-2 py-0.5 rounded-full">カスタム</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-right">
                                <form action="{{ route('menus.destroy', $menu) }}"
                                      method="POST"
                                      onsubmit="return confirm('「{{ $menu->name }}」を削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-400 hover:text-red-600 text-xs">
                                        削除
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach

        </div>
    </div>
</x-app-layout>