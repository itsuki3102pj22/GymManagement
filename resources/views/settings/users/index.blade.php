<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                ユーザー管理
            </h2>
            <a href="{{ route('users.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
                + トレーナー追加
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-400 uppercase">
                        <tr>
                            <th class="px-6 py-3 text-left">名前</th>
                            <th class="px-6 py-3 text-left">メールアドレス</th>
                            <th class="px-6 py-3 text-center">ロール</th>
                            <th class="px-6 py-3 text-center">担当顧客数</th>
                            <th class="px-6 py-3 text-left">登録日</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                <span class="ml-2 text-xs bg-blue-100 text-blue-600
                                             px-2 py-0.5 rounded-full">あなた</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    {{ $user->isSupervisor()
                                        ? 'bg-purple-100 text-purple-700'
                                        : 'bg-gray-100 text-gray-600' }}">
                                    {{ $user->isSupervisor() ? '責任者' : 'トレーナー' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-gray-600">
                                {{ $user->clients_count }} 名
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs">
                                {{ $user->created_at->format('Y/m/d') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="text-blue-600 hover:underline text-xs">
                                        編集
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}"
                                          method="POST"
                                          onsubmit="return confirm('{{ $user->name }} さんを削除しますか？')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-400 hover:text-red-600 text-xs">
                                            削除
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 統計サマリー --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-xs text-gray-400 mb-1">総ユーザー数</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $users->count() }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-xs text-gray-400 mb-1">トレーナー</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $users->where('role', 1)->count() }}
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 text-center">
                    <p class="text-xs text-gray-400 mb-1">責任者</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $users->where('role', 2)->count() }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>