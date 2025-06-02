<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Blog') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <form method="POST" action="{{ route('admin.blog.update', $blog->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6 p-6">
                        <!-- Title -->
                        <div class="mb-4">
                            <label for="judul" class="block text-sm font-medium text-gray-700">Judul</label>
                            <input type="text" name="judul" id="judul" value="{{ old('judul', $blog->judul) }}" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            @error('judul') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="desc" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea name="desc" id="desc" rows="5" required
                                      class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('desc', $blog->desc) }}</textarea>
                            @error('desc') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Image Field and other fields if required -->
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700">Gambar</label>
                            <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <small>Current Image:</small>
                            <img src="{{ asset('storage/blogs/' . $blog->image) }}" class="w-16 h-16 object-cover mt-2" alt="{{ $blog->judul }}">
                            @error('image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4 flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
