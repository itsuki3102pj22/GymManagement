<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">顧客新規登録</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form action="{{ route('clients.store') }}" method="POST" class="space-y-5">
                    @csrf
                    @include('clients._form', ['client' => null])
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('clients.index') }}"
                           class="border border-gray-300 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50">
                            キャンセル
                        </a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700">
                            登録する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>