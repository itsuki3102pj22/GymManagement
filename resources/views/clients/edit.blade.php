<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $client->name }} さんの情報編集
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    @include('clients._form', ['client' => $client])
                    <div class="flex justify-end gap-3 pt-2">
                        <a href="{{ route('clients.show', $client) }}"
                           class="border border-gray-300 text-gray-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50">
                            キャンセル
                        </a>
                        <button type="submit"
                                class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700">
                            更新する
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>