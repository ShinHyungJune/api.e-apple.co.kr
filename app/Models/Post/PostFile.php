<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PostFile extends Model
{
    use HasFactory;

    public static function stores(Post $post, $files)
    {
        $dbFiles = [];
        foreach ($files as $file) {
            $path = Storage::disk('local')->put('posts', $file);
            if ($path) {
                $dbFiles[] = [
                    'post_id' => $post->id,
                    'origin_name' => $file->getClientOriginalName(),
                    'saved_name' => basename($path),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType(),
                ];
            }
        }

        PostFile::insert($dbFiles);
    }
}
