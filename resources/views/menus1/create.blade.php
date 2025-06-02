<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex m-2 p-2">
                <a href="{{ route('koki.menus.index') }}"
                    class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">Menu Index</a>
            </div>
            <div class="m-2 p-2 bg-slate-100 rounded">
                <div class="space-y-8 divide-y divide-gray-200 w-1/2 mt-10">
                <form method="POST" action="{{ route('koki.menus.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="sm:col-span-6">
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select id="category_id" name="category_id" class="form-select block w-full mt-1">
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                        </div>
                        <div class="sm:col-span-6">
                            <label for="name" class="block text-sm font-medium text-gray-700"> Name </label>
                            <div class="mt-1">
                                <input type="text" id="name" name="name"
                                    class="block w-full appearance-none bg-white border border-gray-400 rounded-md py-2 px-3 text-base leading-normal transition duration-150 ease-in-out sm:text-sm sm:leading-5" />
                            </div>
                            @error('name')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="sm:col-span-6">
                            <label for="image" class="block text-sm font-medium text-gray-700"> Gambar </label>
                            <div class="mt-1">
                                <input type="file" id="image" name="image"
                                    class="block w-full appearance-none bg-white border border-gray-400 rounded-md py-2 px-3 text-base leading-normal transition duration-150 ease-in-out sm:text-sm sm:leading-5" />
                            </div>
                            @error('image')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="sm:col-span-6 pt-5">
                            <label for="body" class="block text-sm font-medium text-gray-700">Description</label>
                            <div class="mt-1">
                                <textarea id="body" rows="3" name="description"
                                    class="shadow-sm focus:ring-indigo-500 appearance-none bg-white border py-2 px-3 text-base leading-normal transition duration-150 ease-in-out focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                            </div>
                            @error('description')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>

                            <!-- Tampilkan radio button hanya jika kategori adalah makanan -->
                            <div class="sm:col-span-6" id="spiciness_section" style="display: none;">
                                <label class="block text-sm font-medium text-gray-700">Tingkat Kepedasan</label>
                                <div class="mt-1">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="spiciness" value="Tidak Pedas" class="form-radio text-blue-600">
                                        <span class="ml-2">Tidak Pedas</span>
                                    </label>
                                    <label class="inline-flex items-center ml-4">
                                        <input type="radio" name="spiciness" value="Sedang" class="form-radio text-yellow-600">
                                        <span class="ml-2">Sedang</span>
                                    </label>
                                    <label class="inline-flex items-center ml-4">
                                        <input type="radio" name="spiciness" value="Pedas" class="form-radio text-red-600">
                                        <span class="ml-2">Pedas</span>
                                    </label>
                                </div>
                            </div>


                            <!-- Submit -->
                            <!-- <div class="mt-6 p-4">
                                <button type="submit" class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">Simpan</button>
                            </div>
                        </form> -->


                        <!-- <div class="sm:col-span-6">
                            <label class="block text-sm font-medium text-gray-700">Tingkat Kepedasan</label>
                                <div class="mt-1">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="spiciness" value="Tidak Pedas" class="form-radio text-blue-600">
                                        <span class="ml-2">Tidak Pedas</span>
                                    </label>
                                    <label class="inline-flex items-center ml-4">
                                        <input type="radio" name="spiciness" value="Sedang" class="form-radio text-yellow-600">
                                        <span class="ml-2">Sedang</span>
                                    </label>
                                    <label class="inline-flex items-center ml-4">
                                        <input type="radio" name="spiciness" value="Pedas" class="form-radio text-red-600">
                                        <span class="ml-2">Pedas</span>
                                    </label>
                                </div>
                                @error('spiciness')
                                    <div class="text-sm text-red-400">{{ $message }}</div>
                                @enderror
                            </div> -->
                        <div class="sm:col-span-6">
                            <label for="price" class="block text-sm font-medium text-gray-700">Harga</label>
                            <div class="mt-1">
                                <input type="number" min="0.00" max="10000000" step="0.01" id="price" name="price"
                                    value="{{ old('price') }}" class="block w-full appearance-none bg-white border border-gray-400 rounded-md py-2 px-3 text-base leading-normal transition duration-150 ease-in-out sm:text-sm sm:leading-5" />
                            </div>
                            @error('price')
                                <div class="text-sm text-red-400">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- <div class="sm:col-span-6 pt-5">
                            <label for="categories" class="block text-sm font-medium text-gray-700">Categories</label>
                            <div class="mt-1">
                                <select id="categories" name="categories[]" class="form-multiselect block w-full mt-1"
                                    multiple>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> -->
                        <div class="mt-6 p-4">
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-500 hover:bg-indigo-700 rounded-lg text-white">Store</button>
                        </div>
                    </form>
                    <script>
                            // Menyembunyikan/menampilkan bagian tingkat kepedasan berdasarkan kategori
                            document.getElementById('category_id').addEventListener('change', function() {
                                var spicinessSection = document.getElementById('spiciness_section');
                                if (this.value == 'Makanan') { // Ganti dengan ID kategori makanan
                                    spicinessSection.style.display = 'block';
                                } else {
                                    spicinessSection.style.display = 'none';
                                }
                            });
                        </script>
                </div>

            </div>
        </div>
    </div>
</x-admin-layout>