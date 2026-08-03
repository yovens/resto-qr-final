<?php


namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        Category::create($request->all());
        return redirect('/admin/categories');
    }

    public function destroy($id)
    {
        Category::destroy($id);
        return redirect('/admin/categories');
    }
}