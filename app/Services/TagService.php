<?php

namespace App\Services;

use App\Enums\TagStatus;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class TagService
{
    public function createTag(array $data): Tag
    {
        return DB::transaction(function () use ($data): Tag {
            $tag = new Tag();
            $tag->title = $data['title'];
            $tag->description = $data['description'] ?? null;
            $tag->status = $data['status'] ?? TagStatus::default();
            $tag->created_by = user()->id;
            $tag->save();

            return $tag;
        }, config('app.db_transaction_attemps', 3));
    }
}
