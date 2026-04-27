<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">予約カレンダー</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('error'))
            <div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
            @endif

            {{-- 週ナビゲーション --}}
            <div class="flex items-center justify-between">
                <a href="{{ route('reservations.index', ['week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}"
                    class="border border-gray-200 px-4 py-2 rounded-lg text-sm
                          text-gray-600 hover:bg-gray-50">
                    ← 前の週
                </a>
                <div class="text-center">
                    <p class="font-semibold text-gray-800">
                        {{ $weekStart->format('Y年m月d日') }}
                        〜
                        {{ $weekEnd->format('m月d日') }}
                    </p>
                    <a href="{{ route('reservations.index') }}"
                        class="text-xs text-blue-500 hover:underline">今週に戻る</a>
                </div>
                <a href="{{ route('reservations.index', ['week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}"
                    class="border border-gray-200 px-4 py-2 rounded-lg text-sm
                          text-gray-600 hover:bg-gray-50">
                    次の週 →
                </a>
            </div>

            {{-- カレンダー本体 --}}
            <div class="grid grid-cols-7 gap-2">
                @foreach($calendarData as $dateStr => $day)
                @php
                $isToday = $day['date']->isToday();
                $dayNames = ['月','火','水','木','金','土','日'];
                $dayName = $dayNames[$day['date']->dayOfWeek === 0 ? 6 : $day['date']->dayOfWeek - 1];
                $isSat = $day['date']->isSaturday();
                $isSun = $day['date']->isSunday();
                @endphp
                <div class="bg-white rounded-xl shadow-sm overflow-hidden
                            {{ $isToday ? 'ring-2 ring-blue-400' : '' }}">

                    {{-- 日付ヘッダー --}}
                    <div class="px-2 py-2 text-center border-b
                                {{ $isToday ? 'bg-blue-600 text-white' :
                                   ($isSun ? 'bg-red-50 text-red-500' :
                                   ($isSat ? 'bg-blue-50 text-blue-500' : 'bg-gray-50 text-gray-600')) }}">
                        <p class="text-xs font-medium">{{ $dayName }}</p>
                        <p class="text-lg font-bold leading-tight">
                            {{ $day['date']->format('d') }}
                        </p>
                    </div>

                    {{-- 予約リスト --}}
                    <div class="p-2 space-y-1.5 min-h-24">
                        @forelse($day['reservations'] as $r)
                        <div class="rounded-lg px-2 py-1.5 text-xs
                                    {{ $r->status === 0 ? 'bg-yellow-50 border border-yellow-200' :
                                       ($r->status === 1 ? 'bg-green-50 border border-green-200' :
                                        'bg-gray-100 border border-gray-200 opacity-60') }}">
                            <p class="font-semibold text-gray-700 truncate">
                                {{ $r->client->name }}
                            </p>
                            <p class="text-gray-500">
                                {{ $r->start_at->format('H:i') }}〜{{ $r->end_at->format('H:i') }}
                            </p>
                            <p class="text-gray-400 truncate text-xs">
                                {{ $r->trainer->name }}
                            </p>
                            <div class="flex items-center gap-1 mt-1">
                                {{-- ステータス変更 --}}
                                <form action="{{ route('reservations.update', $r) }}"
                                    method="POST" class="flex gap-1">
                                    @csrf
                                    @method('PUT')
                                    @if($r->status !== 1)
                                    <button type="submit" name="status" value="1"
                                        class="bg-green-500 text-white rounded px-1.5 py-0.5
                                                   text-xs hover:bg-green-600">
                                        確定
                                    </button>
                                    @endif
                                    @if($r->status !== 2)
                                    <button type="submit" name="status" value="2"
                                        class="bg-gray-400 text-white rounded px-1.5 py-0.5
                                                   text-xs hover:bg-gray-500">
                                        ✕
                                    </button>
                                    @endif
                                </form>
                                {{-- 削除 --}}
                                <form action="{{ route('reservations.destroy', $r) }}"
                                    method="POST"
                                    onsubmit="return confirm('この予約を削除しますか？')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-300 hover:text-red-500 text-xs">
                                        削除
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-gray-300 text-center pt-2">予約なし</p>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>

            {{-- 凡例 --}}
            <div class="flex gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-200"></span>
                    仮予約
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded bg-green-100 border border-green-200"></span>
                    確定
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-3 h-3 rounded bg-gray-100 border border-gray-200"></span>
                    キャンセル
                </span>
            </div>

            {{-- 予約追加フォーム --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-700 mb-4 text-sm uppercase tracking-wide">
                    予約を追加
                </h3>
                <form action="{{ route('reservations.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">顧客 *</label>
                            <select name="client_id"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2
                                           text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="">— 選択 —</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">担当トレーナー *</label>
                            <select name="trainer_id"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2
                                           text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="">— 選択 —</option>
                                @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}"
                                    {{ old('trainer_id', auth()->id()) == $trainer->id ? 'selected' : '' }}>
                                    {{ $trainer->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('trainer_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">日付 *</label>
                            <input type="date" name="date"
                                value="{{ old('date', today()->format('Y-m-d')) }}"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                            @error('date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">開始時間 *</label>
                            <input type="time" name="start_time"
                                value="{{ old('start_time', '10:00') }}"
                                step="900"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                            @error('start_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">終了時間 *</label>
                            <input type="time" name="end_time"
                                value="{{ old('end_time', '11:00') }}"
                                step="900"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2
                                          text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                            @error('end_time')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">ステータス *</label>
                            <select name="status"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2
                                           text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>
                                    確定
                                </option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>
                                    仮予約
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm
                                       font-medium hover:bg-blue-700 transition">
                            予約を登録
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>