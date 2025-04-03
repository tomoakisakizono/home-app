<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\ShoppingItemAdded;
use App\Models\ShoppingList;
use App\Models\User;
use App\Models\Pair;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShoppingListController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 🔹 ユーザーのペア情報を取得
        $pair = Pair::where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id)
                    ->where('status', 'accepted')
                    ->first();
        
        // 🔹 ペアが存在しない場合、ペア設定ページへリダイレクト
        if (!$pair) {
            return redirect()->route('pair.setup')->with('error', 'ペアを設定してください。');
        }
        
        // 🔹 取得したペア情報から `pair_id` をセット
        $pairId = $pair->id;
    
        // 🔹 カテゴリーを取得
        $categories = Category::all();  
    
        // 🔹 ショッピングリストを取得（リレーションを含める）
        $shoppingItems = ShoppingList::where('pair_id', $pairId)
            ->with('category') // 🔹 category のリレーションをロード
            ->orderBy('created_at', 'desc')
            ->get();

        // 🔹 カテゴリーごとに分類
        $shoppingLists = $shoppingItems->groupBy(function ($item) {
            return optional($item->category)->name ?? 'その他';
            });
    
        return view('shopping.index', compact('categories', 'shoppingLists'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'category_id' => 'nullable|exists:categories,id',
        ]);
    
        $user = Auth::user();
        $pair = Pair::where('user1_id', $user->id)
                    ->orWhere('user2_id', $user->id)
                    ->where('status', 'accepted')
                    ->first();
    
        DB::beginTransaction();
        try {
            $item = ShoppingList::create([
                'pair_id' => $pair->id,
                'user_id' => $user->id,
                'item_name' => $request->item_name,
                'quantity' => $request->quantity,
                'status' => '未購入',
                'category_id' => $request->category_id,
            ]);
    
            $partner = $pair->user1_id === $user->id
                ? User::find($pair->user2_id)
                : User::find($pair->user1_id);
    
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

