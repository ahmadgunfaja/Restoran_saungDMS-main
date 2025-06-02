<?php

namespace App\Http\Controllers\Koki;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuStoreRequest;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::all();
        return view('koki.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('koki.menus.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuStoreRequest $request)
{
    $image = $request->file('image')->store('public/menus');

    $menu = Menu::create([
        'name' => $request->name,
        'description' => $request->description,  // Pastikan deskripsi disimpan
        'image' => $image,
        'price' => $request->price,
        'category_id' => $request->category_id, // Menyimpan kategori
        'tingkatpedas' => $request->tingkatpedas // Menyimpan tingkat kepedasan jika ada
    ]);

    if ($request->has('categories')) {
        $menu->categories()->attach($request->categories);
    }

    return to_route('koki.menus.index')->with('success', 'Menu created successfully.');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('koki.menus.edit', compact('menu', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
{
    $request->validate([
        'name' => 'required',
        'description' => 'required',
        'price' => 'required',
        // 'category_id' => $request->category_id, // Menyimpan kategori
        // 'tingkatpedas' => $request->tingkatpedas // Menyimpan tingkat kepedasan jika ada
    ]);

    $image = $menu->image;
    if ($request->hasFile('image')) {
        Storage::delete($menu->image);
        $image = $request->file('image')->store('public/menus');
    }

    $menu->update([
        'name' => $request->name,
        'description' => $request->description,  // Pastikan deskripsi diperbarui
        'image' => $image,
        'price' => $request->price
        
    ]);

    if ($request->has('categories')) {
        $menu->categories()->sync($request->categories);
    }

    return to_route('koki.menus.index')->with('success', 'Menu updated successfully.');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        Storage::delete($menu->image);
        $menu->categories()->detach();
        $menu->delete();
        return to_route('koki.menus.index')->with('danger', 'Menu deleted successfully.');

    }
}
