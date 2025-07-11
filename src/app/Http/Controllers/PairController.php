<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pair;
use App\Models\User;
use App\Models\FunctionRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PairController extends Controller
{
    public function setup()
    {
        return view('pair.setup', [
            'user' => $this->authUser,
            'pair' => $this->pair
        ]);
    }

    public function invite(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $partner = User::where('email', $request->email)->first();

        if (!$partner || $partner->id === $this->authUser->id) {
            return redirect()->back()->with('error', '無効なユーザーです');
        }

        $existingPair = Pair::where('user1_id', $this->authUser->id)->where('status', 'pending')->first();

        if ($existingPair) {
            return redirect()->back()->with('success', "すでに発行済みの招待コード: {$existingPair->invite_code}");
        }

        $inviteCode = strtoupper(bin2hex(random_bytes(4)));

        Pair::create([
            'user1_id' => $this->authUser->id,
            'invite_code' => $inviteCode,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', "招待コード: $inviteCode");
    }

    public function accept(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|exists:pairs,invite_code',
        ]);

        $user = $this->authUser;

        $pair = Pair::where('invite_code', $request->invite_code)
                    ->whereNull('user2_id')
                    ->first();

        if (!$pair || $pair->user1_id === $user->id) {
            return redirect()->back()->with('error', '無効な招待コードです。');
        }

        DB::beginTransaction();
        try {
            $user1 = User::find($pair->user1_id);
            $pairName = $user1->name . ' & ' . $user->name;
            $defaultImagePath = 'images/default_pair.png';

            $pair->update([
                'user2_id' => $user->id,
                'pair_name' => $pairName,
                'pair_image' => $defaultImagePath,
                'status' => 'accepted'
            ]);

            User::whereIn('id', [$pair->user1_id, $pair->user2_id])->update(['pair_id' => $pair->id]);

            DB::commit();
            return redirect()->route('pair.show')->with('success', 'ペアが成立しました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'ペアの承認処理中にエラーが発生しました。');
        }
    }

    public function show()
    {
        if (!$this->pair) {
            return redirect()->route('pair.setup')->with('error', 'ペアが設定されていません');
        }

        $partner = ($this->pair->user1_id === $this->authUser->id)
            ? User::find($this->pair->user2_id)
            : User::find($this->pair->user1_id);

        if (!$partner) {
            return redirect()->route('pair.setup')->with('error', 'ペアの相手が見つかりませんでした。');
        }

        $latestFunctions = FunctionRecord::where('user_id', $this->authUser->id)
            ->latest()
            ->take(3)
            ->get();

        return view('pair.show', [
            'user' => $this->authUser,
            'partner' => $partner,
            'pair' => $this->pair,
            'latestFunctions' => $latestFunctions
        ]);
    }

    public function edit()
    {
        if (!$this->pair) {
            return redirect()->route('pair.setup')->with('error', 'ペアが設定されていません。');
        }

        $partner = ($this->pair->user1_id === $this->authUser->id)
            ? User::find($this->pair->user2_id)
            : User::find($this->pair->user1_id);

        if (!$partner) {
            return redirect()->route('pair.setup')->with('error', 'ペアの相手が見つかりませんでした。');
        }

        return view('pair.edit', [
            'user' => $this->authUser,
            'partner' => $partner,
            'pair' => $this->pair
        ]);
    }

    public function updateImage(Request $request)
    {
        $request->validate([
            'pair_image' => 'image|mimes:jpg,jpeg,png,webp,heic|max:4096',
        ]);

        if (!$this->pair) {
            return redirect()->back()->with('error', 'ペアが設定されていません。');
        }

        if ($request->hasFile('pair_image')) {
            $path = $request->file('pair_image')->store('pair_images', 'public');

            if ($this->pair->pair_image) {
                Storage::disk('public')->delete($this->pair->pair_image);
            }

            $this->pair->update(['pair_image' => $path]);
        }

        return redirect()->route('pair.edit')->with('success', 'ペア画像を更新しました！');
    }

    public function updateName(Request $request)
    {
        $request->validate([
            'pair_name' => 'required|string|max:50',
        ]);

        if (!$this->pair) {
            return redirect()->back()->with('error', 'ペアが設定されていません。');
        }

        $this->pair->update(['pair_name' => $request->pair_name]);

        return redirect()->route('pair.edit')->with('success', 'ペアネームを更新しました！');
    }
}
