<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Notifications\VideoPosted;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $pair = $this->pair;

        $query = Video::where('pair_id', $pair->id);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $videos = $query->orderBy('registered_at', 'desc')->paginate(9);

        return view('videos.index', compact('videos'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'youtube_url' => 'required|url',
            'comment' => 'nullable|string|max:255',
            'category' => 'required|string',
            'registered_at' => 'required|date',
        ]);

        $user = $this->authUser;
        $pair = $this->pair;

        DB::beginTransaction();
        try {
            $video = Video::create([
                'pair_id' => $pair->id,
                'user_id' => $user->id,
                'youtube_url' => $request->youtube_url,
                'comment' => $request->comment,
                'category' => $request->category,
                'registered_at' => $request->registered_at,
            ]);

            $partner = \App\Models\User::where('pair_id', $pair->id)
                ->where('id', '!=', $user->id)
                ->first();

            if ($partner) {
                $video->user = $user;
                $partner->notify(new VideoPosted($video));
            }

            DB::commit();
            return redirect()->route('videos.index')->with('success', '動画を投稿しました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '動画投稿に失敗しました。')->withInput();
        }
    }

    public function show(Video $video)
    {
        //
    }

    public function edit(Video $video)
    {
        if ($video->pair_id !== $this->pair->id) {
            abort(403);
        }

        return view('videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        if ($video->pair_id !== $this->pair->id) {
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

    public function destroy(Video $video)
    {
        if ($video->pair_id !== $this->pair->id) {
            abort(403);
        }

        $video->delete();

        return redirect()->route('videos.index')->with('success', '動画を削除しました。');
    }
}
