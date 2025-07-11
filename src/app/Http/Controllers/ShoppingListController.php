<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\ShoppingItemAdded;
use App\Models\ShoppingList;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ShoppingListRequest;

class ShoppingListController extends Controller
{
    public function index()
    {
        $categories = Category::where('pair_id', $this->pair->id)->get();

        $shoppingItems = ShoppingList::where('pair_id', $this->pair->id)
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        $shoppingLists = $shoppingItems->groupBy(function ($item) {
            return optional($item->category)->name ?? 'その他';
        });

        return view('shopping.index', compact('categories', 'shoppingLists'));
    }

    public function store(ShoppingListRequest $request)
    {
        DB::beginTransaction();
        try {
            $item = ShoppingList::create([
                'pair_id' => $this->pair->id,
                'user_id' => $this->authUser->id,
                'item_name' => $request->item_name,
                'quantity' => $request->quantity,
                'status' => '未購入',
                'category_id' => $request->category_id,
            ]);

            $partner = $this->pair->user1_id === $this->authUser->id
                ? User::find($this->pair->user2_id)
                : User::find($this->pair->user1_id);

            if ($partner) {
                $partner->notify(new ShoppingItemAdded($item));
            }

            DB::commit();
            return redirect()->route('shopping.index')->with('success', '買い物リストに追加しました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '登録中にエラーが発生しました。')->withInput();
        }
    }

    public function updateStatus($id)
    {
        $item = ShoppingList::findOrFail($id);
        $item->status = $item->status === '未購入' ? '購入済み' : '未購入';
        $item->save();

        return response()->json([
            'success' => true,
            'newStatus' => $item->status
        ]);
    }

    public function destroy($id)
    {
        $item = ShoppingList::findOrFail($id);
        $item->delete();

        return back()->with('success', 'アイテムを削除しました！');
    }
}
