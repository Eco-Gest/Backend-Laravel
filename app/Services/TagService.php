<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Post;
use App\Models\TagPost;
use Illuminate\Support\Facades\DB;

use DateTime;

class TagService
{
    public function addTagsToPost(array $tags)
    {
        $allTags = Tag::get();
        $tagsToAttach = [];
        
        foreach ($tags as $tag) {
            if (!in_array($tag['label'], $allTags->pluck('label')->toArray())) {
                $newTag = Tag::create(['label' => $tag['label']]);
                $newTag->save();
                $tagsToAttach[] = $newTag->id;
            } else {
                $tagsToAttach[] = $allTags->where('label', $tag['label'])->first()->id;
            }
        }

        return $tagsToAttach;
    }

    public function updateTagsToPost(Post $post, array $updatedTags)
    {
        $oldTags = []; //
        $newTags = $this->addTagsToPost($updatedTags); // 1, 3, 8
        $tagsToDetach = [];
        $tagsToAttach = [];

        // dd($updatedTags); 
        foreach (($post->tags)->toArray() as $tag) {
            $oldTags[] = $tag['id'];
            if (!in_array($tag['id'], $newTags)) {
                $tagsToDetach[] = $tag['id'];
            }
        }

        foreach ($newTags as $newTag => $newId) {
            if (!in_array($newId, $oldTags)) {
                $tagsToAttach[] = $newId;
            }
        }

        $tagsToUpdate = array('attach' => $tagsToAttach, 'detach' => $tagsToDetach);

        $post->tags()->attach($tagsToUpdate['attach']);
        $post->tags()->detach($tagsToUpdate['detach']);
        $postUpdated = $post;

        return $postUpdated;
    }
}