<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:categories,name'
        ]);

        Category::create(['name' => $request->name]);

        return redirect()->route('categories.index')->with('success', 'カテゴリーを追加しました！');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // 🔹 カテゴリーに関連する買い物リストがある場合、削除前にチェック
        if ($category->shoppingLists()->exists()) {
            return redirect()->back()->with('error', 'このカテゴリーにはアイテムが含まれているため削除できません。');
        }

        $category->delete();
        return redirect()->back()->with('success', 'カテゴリーを削除しました！');
    }

}

