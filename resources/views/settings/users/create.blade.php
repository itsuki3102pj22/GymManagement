<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            トレーナー追加
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form action="{{ route('users.store') }}" method="POST" class="space-y-5">
                    @csrf
                    @include('settings.users._form', ['user' => null])
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            パスワード *
                        </label>
                        <input type="password" name="password"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2
                                      text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                        @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">
                            パスワード（確認）*
                        </label>
                        <input type="password" name="password_confirmation"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2
                                      text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('users.index') }}"
                           class="border border-gray-300 text-gray-600 px-5 py-2
                                  rounded-lg text-sm hover:bg-gray-50">
                            キャンセル
                        </a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-5 py-2 rounded-lg
                                       text-sm hover:bg-blue-700">
                            作成する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>