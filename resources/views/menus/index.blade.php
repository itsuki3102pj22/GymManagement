<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display"
            style="font-size:26px;font-weight:400;color:var(--navy);letter-spacing:1px">
            種目マスタ管理
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- 追加フォーム --}}
            <div class="rounded-2xl p-5 bg-white"
                 style="border:1px solid var(--card-border);border-top:3px solid var(--gold)">
                <p class="text-xs font-semibold tracking-widest uppercase mb-4"
                   style="color:var(--gold)">種目を追加</p>
                <form action="{{ route('menus.store') }}" method="POST">
                    @csrf
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <input type="text" name="name"
                                   value="{{ old('name') }}"
                                   placeholder="種目名（例：ケトルベルスイング）"
                                   class="w-full rounded-xl px-4 py-2.5 text-sm"
                                   style="border:1px solid var(--card-border);color:var(--navy)">
                            @error('name')
                            <p class="text-xs mt-1" style="color:#dc2626">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="w-44">
                            <input type="text" name="category"
                                   value="{{ old('category') }}"
                                   placeholder="カテゴリ"
                                   list="category-list"
                                   class="w-full rounded-xl px-4 py-2.5 text-sm"
                                   style="border:1px solid var(--card-border);color:var(--navy)">
                            <datalist id="category-list">
                                @foreach($menus->keys() as $cat)
                                <option value="{{ $cat }}">
                                @endforeach
                            </datalist>
                        </div>
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white whitespace-nowrap"
                                style="background:var(--royal)">
                            追加
                        </button>
                    </div>
                </form>
            </div>

            {{-- カテゴリ別一覧 --}}
            @foreach($menus as $category => $items)
            <div class="rounded-2xl overflow-hidden bg-white"
                 style="border:1px solid var(--card-border)">
                <div class="px-5 py-3 flex items-center justify-between"
                     style="background:var(--surface);border-bottom:1px solid var(--card-border)">
                    <p class="text-xs font-semibold tracking-widest uppercase"
                       style="color:var(--royal)">{{ $category }}</p>
                    <span class="text-xs" style="color:var(--text-muted)">
                        {{ $items->count() }} 種目
                    </span>
                </div>
                <div class="divide-y" style="border-color:var(--card-border)">
                    @foreach($items as $menu)
                    <div class="flex items-center justify-between px-5 py-2.5"
                         onmouseover="this.style.background='var(--surface)'"
                         onmouseout="this.style.background=''">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-sm" style="color:var(--navy)">
                                {{ $menu->name }}
                            </span>
                            @if($menu->is_custom)
                            <span class="text-xs px-2 py-0.5 rounded-full"
                                  style="background:var(--blue-bg);color:var(--royal);
                                         border:1px solid var(--blue-border)">
                                カスタム
                            </span>
                            @endif
                        </div>
                        <form action="{{ route('menus.destroy', $menu) }}"
                              method="POST"
                              onsubmit="return confirm('「{{ $menu->name }}」を削除しますか？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs px-3 py-1 rounded-lg transition"
                                    style="color:#dc2626;border:1px solid #fecaca;background:#fef2f2">
                                削除
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

        </div>
    </div>
</x-app-layout>