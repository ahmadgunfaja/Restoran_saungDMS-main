<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Koki') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.koki.update', $koki->id) }}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-6">
                    <div class="flex flex-col">
                        <label for="name" class="text-sm font-semibold">Name</label>
                        <input type="text" name="name" value="{{ $koki->name }}" class="p-2 border rounded" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="email" class="text-sm font-semibold">Email</label>
                        <input type="email" name="email" value="{{ $koki->email }}" class="p-2 border rounded" required>
                    </div>

                    <div class="flex flex-col">
                        <label for="no_hp" class="text-sm font-semibold">No. HP</label>
                        <input type="text" name="no_hp" value="{{ $koki->no_hp }}" class="p-2 border rounded" required>
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
