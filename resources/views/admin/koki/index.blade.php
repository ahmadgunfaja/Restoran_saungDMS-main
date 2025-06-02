<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Koki Menunggu Persetujuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end m-2 p-2">
                <a href="{{ route('admin.koki.create') }}"
                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">Tambah Koki</a>
            </div>
            <div class="flex flex-col">
                <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block py-2 min-w-full sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow-md sm:rounded-lg">
                            <table class="min-w-full">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                                            Name
                                        </th>
                                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                                            Email
                                        </th>
                                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                                            Status
                                        </th>
                                        <th scope="col" class="py-3 px-6 text-xs font-medium tracking-wider text-left text-gray-700 uppercase dark:text-gray-400">
                                            No. HP
                                        </th>
                                        <th scope="col" class="relative py-3 px-6">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kokis as $koki)
                                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                            <td class="py-4 px-6 text-sm font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                {{ $koki->name }}
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                                {{ $koki->email }}
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                                @if ($koki->status === 'pending')
                                                    <span class="text-yellow-500">-</span> <!-- Pending icon -->
                                                @elseif ($koki->status === 'approved')
                                                    <span class="text-green-500">✔</span> <!-- Approved icon -->
                                                @elseif ($koki->status === 'rejected')
                                                    <span class="text-red-500">❌</span> <!-- Rejected icon -->
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 text-sm text-gray-500 whitespace-nowrap dark:text-gray-400">
                                                {{ $koki->no_hp }}
                                            </td>
                                            <td class="py-4 px-6 text-sm font-medium text-right whitespace-nowrap">
                                                <div class="flex space-x-2">
                                                    <form action="{{ route('admin.koki.approve', $koki) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-green-500 hover:bg-green-700 rounded-lg text-white">
                                                            Setujui
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.koki.reject', $koki) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-700 rounded-lg text-white">
                                                            Tolak
                                                        </button>
                                                    </form>

                                                    <!-- Tombol Edit Koki -->
                                                    <a href="{{ route('admin.koki.edit', $koki) }}" class="px-4 py-2 bg-blue-500 hover:bg-blue-700 rounded-lg text-white">
                                                        Edit
                                                    </a>

                                                    <!-- Tombol Delete Koki -->
                                                    <form action="{{ route('admin.koki.destroy', $koki) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-800 rounded-lg text-white">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
