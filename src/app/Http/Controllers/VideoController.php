<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Notifications\VideoPosted;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ログインユーザーのペアIDで動画を取得
        $pairId = Auth::user()->pair_id;

        // ペアに紐づく動画一覧を日付降順で取得
        $query = Video::where('pair_id', $pairId);

        // カテゴリ検索
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $videos = $query->orderBy('registered_at', 'desc')->paginate(9);

        return view('videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'youtube_url' => 'required|url',
            'comment' => 'nullable|string|max:255',
            'category' => 'required|string',
            'registered_at' => 'required|date',
        ]);

        $user = Auth::user();
        $pairId = $user->pair_id;
    
        if (!$pairId) {
            return redirect()->route('pair.setup')->with('error', 'ペアを設定してください。');
        }
        
        $video = Video::create([
            'pair_id' => $pairId,
            'user_id' => $user->id,
            'youtube_url' => $request->youtube_url,
            'comment' => $request->comment,
            'category' => $request->category,
            'registered_at' => $request->registered_at,
        ]);

        $partner = \App\Models\User::where('pair_id', $pairId)
                ->where('id', '!=', $user->id)
                ->first();

        if ($partner) {
            $video->user = $user; // 通知用に user をセット
            $partner->notify(new VideoPosted($video));
        }

        return redirect()->route('videos.index')->with('success', '動画を投稿しました！');
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        // 他のペアの動画を編集できないよう制限
        if ($video->pair_id !== Auth::user()->pair_id) {
            abort(403);
        }

        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        if ($video->pair_id !== Auth::user()->pair_id) {
            abort(403);
        }
    
        $request->validate([
            'youtube_url' => 'required|url',
            'comment' => 'nullable|string|max:255',
            'category' => 'required|string',
            'registered_at' => 'required|date',
        ]);
    
        $video->update([
            'youtube_url' => $request->youtube_url,
            'comment' => $request->comment,
            'category' => $request->category,
            'registered_at' => $request->registered_at,
        ]);
    
        return redirect()->route('videos.index')->with('success', '動画情報を更新しました！');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        // 他のペアの動画を削除できないように制限
        if ($video->pair_id !== Auth::user()->pair_id) {
            abort(403);
        }

        $video->delete();

        return redirect()->route('videos.index')->with('success', '動画を削除しました。');
    }
}
