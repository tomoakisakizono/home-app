<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\PhotoImage;
use App\Notifications\PhotoPosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PhotoRequest;
use ZipArchive;

class PhotoController extends Controller
{
    public function index(Request $request)
    {
        $query = Photo::where('pair_id', $this->pair->id);

        if ($request->filled('keyword')) {
            $query->where('comment', 'like', '%' . $request->keyword . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $photos = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('photos.index', compact('photos'));
    }

    public function multipleUpload(PhotoRequest $request)
    {
        $user = $this->authUser;

        DB::beginTransaction();
        try {
            $photo = Photo::create([
                'pair_id' => $this->pair->id,
                'family_id' => $user->family_id,
                'user_id' => $user->id,
                'photo_date' => $request->photo_date,
                'comment' => $request->comment,
                'category' => $request->category,
            ]);

            foreach ($request->file('images') as $image) {
                $path = $image->store('photos', 'public');

                PhotoImage::create([
                    'photo_id' => $photo->id,
                    'image_path' => $path,
                ]);
            }

            $partner = \App\Models\User::where('pair_id', $this->pair->id)
                ->where('id', '!=', $user->id)
                ->first();

            if ($partner) {
                $photo->user = $user;
                $partner->notify(new PhotoPosted($photo));
            }

            DB::commit();
            return redirect()->route('photos.index')->with('success', '写真を投稿しました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '投稿中にエラーが発生しました。')->withInput();
        }
    }

    public function show(Photo $photo)
    {
        if ($photo->pair_id !== $this->pair->id) {
            abort(403);
        }

        $photo->load('images');

        return view('photos.show', compact('photo'));
    }

    public function edit(Photo $photo)
    {
        if ($photo->pair_id !== $this->pair->id) {
            abort(403);
        }

        return view('photos.edit', compact('photo'));
    }

    public function update(PhotoRequest $request, Photo $photo)
    {
        if ($photo->pair_id !== $this->pair->id) {
            abort(403);
        }

        $photo->update([
            'photo_date' => $request->photo_date,
            'category' => $request->category,
            'comment' => $request->comment,
        ]);

        return redirect()->route('photos.show', $photo)->with('success', '写真情報を更新しました！');
    }

    public function download(PhotoImage $photoImage)
    {
        $photoImage->load('photo');

        if (!$photoImage->photo || $photoImage->photo->pair_id !== $this->pair->id) {
            abort(403, 'この画像にアクセスする権限がありません。');
        }

        $filePath = public_path("storage/{$photoImage->image_path}");

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'ファイルが見つかりません。');
        }

        return response()->download($filePath, basename($photoImage->image_path));
    }

    public function downloadAll(Photo $photo)
    {
        $photo->load('images');

        if ($photo->images->isEmpty() || $photo->pair_id !== $this->pair->id) {
            abort(403, 'この画像にアクセスする権限がありません。');
        }

        $zipDir = storage_path("app/public/zips/");
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0775, true);
        }

        $zipFileName = 'photo_' . $photo->id . '_images.zip';
        $zipFilePath = $zipDir . $zipFileName;

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($photo->images as $image) {
                $filePath = storage_path("app/public/{$image->image_path}");
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, basename($image->image_path));
                }
            }
            $zip->close();
        } else {
            return back()->with('error', 'ZIPファイルの作成に失敗しました。');
        }

        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    public function deleteImage(Photo $photo, PhotoImage $photoImage)
    {
        if ($photo->pair_id !== $this->pair->id) {
            abort(403, 'この画像を削除する権限がありません。');
        }

        $filePath = storage_path("app/public/{$photoImage->image_path}");

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $photoImage->delete();

        return redirect()->route('photos.edit', $photo)->with('success', '画像を削除しました。');
    }

    public function destroy(Photo $photo)
    {
        if ($photo->pair_id !== $this->pair->id) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            foreach ($photo->images as $image) {
                if (\Storage::disk('public')->exists($image->image_path)) {
                    \Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }

            $photo->delete();

            DB::commit();
            return redirect()->route('photos.index')->with('success', '写真を削除しました！');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', '写真の削除中にエラーが発生しました。');
        }
    }
}
