<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PhotoController extends Controller
{
    public function index(Request $request)
    {
        $pairId = Auth::user()->pair_id; // ログインユーザーのペアID

        $query = Photo::where('pair_id', $pairId);

        // 検索機能
        if ($request->filled('keyword')) {
            $query->where('comment', 'like', '%' . $request->keyword . '%');
        }

        // カテゴリフィルター
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $photos = $query->orderBy('photo_date', 'desc')->paginate(10);

        return view('photos.index', compact('photos'));
    }

    public function multipleUpload(Request $request)
    {
        $request->validate([
            'images' => 'required|array|max:10', // 画像は最大10枚
            'images.*' => 'image|max:2048', // 各画像のサイズ上限2MB
            'photo_date' => 'required|date',
            'comment' => 'nullable|string|max:255',
            'category' => 'required|string',
        ]);

        $pairId = Auth::user()->pair_id;

        // 1つの投稿（Photo）を作成
        $photo = Photo::create([
            'pair_id' => $pairId,
            'photo_date' => $request->photo_date,
            'comment' => $request->comment,
            'category' => $request->category,
        ]);

        // 投稿に紐づく画像を保存
        foreach ($request->file('images') as $image) {
            $path = $image->store('photos', 'public');

            // `photo_images` テーブルに画像を保存
            PhotoImage::create([
                'photo_id' => $photo->id,
                'image_path' => $path,
            ]);
        }

        return redirect()->route('photos.index')->with('success', '写真を投稿しました！');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'images' => 'required|array|max:10', // 画像は10枚まで
    //         'images.*' => 'image|max:2048', // 各画像のサイズ上限を2MB
    //         'photo_date' => 'required|date',
    //         'comment' => 'nullable|string|max:255',
    //         'category' => 'required|string',
    //     ]);

    //     $pairId = Auth::user()->pair_id;

    //     // 投稿データを作成
    //     $photo = Photo::create([
    //         'pair_id' => $pairId,
    //         'photo_date' => $request->photo_date,
    //         'comment' => $request->comment,
    //         'category' => $request->category,
    //     ]);

    //     // 画像を保存
    //     foreach ($request->file('images') as $image) {
    //         $path = $image->store('photos', 'public');

    //         PhotoImage::create([
    //             'photo_id' => $photo->id,
    //             'image_path' => $path,
    //         ]);
    //     }

    //     return redirect()->route('photos.index')->with('success', '写真をアップロードしました！');
    // }

    public function show(Photo $photo)
    {
        // 他のペアの写真にアクセス不可
        if ($photo->pair_id !== Auth::user()->pair_id) {
            abort(403);
        }

        $photo->load('images'); // 画像をロード

        return view('photos.show', compact('photo'));
    }

    public function edit(Photo $photo)
    {
        // 他のペアの写真にアクセス不可
        if ($photo->pair_id !== Auth::user()->pair_id) {
            abort(403);
        }

        return view('photos.edit', compact('photo'));
    }

    public function update(Request $request, Photo $photo)
    {
        // 他のペアの写真にアクセス不可
        if ($photo->pair_id !== Auth::user()->pair_id) {
            abort(403);
        }

        $request->validate([
            'photo_date' => 'required|date',
            'category' => 'required|string',
            'comment' => 'nullable|string|max:255',
        ]);

        $photo->update([
            'photo_date' => $request->photo_date,
            'category' => $request->category,
            'comment' => $request->comment,
        ]);

        return redirect()->route('photos.show', $photo)->with('success', '写真情報を更新しました！');
    }

    public function download(PhotoImage $photoImage)
    {
        // 関連する `Photo` を取得
        $photoImage->load('photo');
    
        // 画像が紐づく投稿が存在しない場合のエラー処理
        if (!$photoImage->photo) {
            return redirect()->back()->with('error', '画像の投稿データが見つかりません。');
        }
    
        // 他のペアの画像をダウンロードできないように制限
        if ($photoImage->photo->pair_id !== Auth::user()->pair_id) {
            abort(403, 'この画像にアクセスする権限がありません。');
        }
    
        // 画像のファイルパスを取得
        $filePath = public_path("storage/{$photoImage->image_path}");
        // デバッグ：パスをログに記録
        \Log::info("ダウンロード対象のファイルパス: " . $filePath);
    
        // 画像ファイルが存在しない場合のエラーハンドリング
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'ファイルが見つかりません。');
        }
    
        return response()->download($filePath, basename($photoImage->image_path));
    }

    public function downloadAll(Photo $photo)
    {
        // 投稿に紐づく画像を取得
        $photo->load('images');
    
        if ($photo->images->isEmpty()) {
            return back()->with('error', 'この投稿には画像がありません。');
        }
    
        if ($photo->pair_id !== Auth::user()->pair_id) {
            abort(403, 'この画像にアクセスする権限がありません。');
        }
    
        // ZIP保存先のディレクトリを確認・作成
        $zipDir = storage_path("app/public/zips/");
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0775, true); // ディレクトリを作成
        }
    
        // ZIPファイルの保存パス
        $zipFileName = 'photo_' . $photo->id . '_images.zip';
        $zipFilePath = $zipDir . $zipFileName;
    
        // ZIP作成処理
        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($photo->images as $image) {
                $filePath = storage_path("app/public/{$image->image_path}");
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($image->image_path)); // ZIP内のファイル名を指定
                } else {
                    \Log::error("ZIPエラー: ファイルが見つかりません: {$filePath}");
                }
            }
            if (!$zip->close()) {
                \Log::error("ZIPエラー: ZIPの保存に失敗しました。");
                return back()->with('error', 'ZIPファイルの作成に失敗しました。');
            }
        } else {
            \Log::error("ZIPエラー: ZIPのオープンに失敗しました。");
            return back()->with('error', 'ZIPファイルの作成に失敗しました。');
        }
    
        // ZIPファイルをダウンロードし、送信後に削除
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    public function deleteImage(Photo $photo, PhotoImage $photoImage)
    {
        // ユーザーのペアIDを確認し、不正な削除を防ぐ
        if ($photo->pair_id !== Auth::user()->pair_id) {
            abort(403, 'この画像を削除する権限がありません。');
        }

        // ファイルのパスを取得
        $filePath = storage_path("app/public/{$photoImage->image_path}");

        // ファイルが存在する場合は削除
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // データベースから削除
        $photoImage->delete();

        return redirect()->route('photos.edit', $photo)->with('success', '画像を削除しました。');
    }
    
    public function destroy(Photo $photo)
    {
        if ($photo->pair_id !== Auth::user()->pair_id) {
            abort(403);
        }

        // 投稿に紐づく全画像を削除
        foreach ($photo->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $photo->delete();

        return redirect()->route('photos.index')->with('success', '写真を削除しました！');
    }
}
