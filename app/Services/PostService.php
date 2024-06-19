<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Tag;
use App\Models\UserPointCategory;
use App\Models\UserPostParticipation;
use DateTime;
use Illuminate\Database\Query\JoinClause;

class PostService
{
    protected UserService $userService;

    protected TagService $tagService;

    public function __construct(UserService $userService, TagService $tagService)
    {
        $this->userService = $userService;
        $this->tagService = $tagService;
    }

    public function addAuthorPostToUserPostParticipation(Post $post)
    {
        if ($post->type === 'action') {
            $isCompleted = true;
        } else {
            $isCompleted = $post->end_date >= new DateTime() ? true : false;
        }

        $userPostParticipation = UserPostParticipation::create([
            'participant_id' => $post->author_id,
            'post_id' => $post->id,
            'is_completed' => $isCompleted
        ]);
        $userPostParticipation->save();

        $this->createUserPointCategoryWithZeroPoint($post, $userPostParticipation->participant_id);
    }

    public function createUserPointCategoryWithZeroPoint(Post $post, int $partcipantId)
    {
        $userPointCategoryAlreadyExists = UserPointCategory::where(['user_id' => $partcipantId, 'category_id' => $post->category_id])->count();
        if ($userPointCategoryAlreadyExists == 0) {
            $userPointCategory = UserPointCategory::create([
                'user_id' => $partcipantId,
                'category_id' => $post->category_id,
                'current_point' => 0,
                'total_point' => 0,
            ]);

            $userPointCategory->save();
        }
    }

    public function searchPost(string $q)
    {
        $posts_by_tags = $this->tagService->searchTag($q);
        // Participant lists with details
        $posts = Post::where('title', 'ILIKE', '%' . $q . '%')
            ->orWhere('description', 'ILIKE', '%' . $q . '%')
            ->take(10)
            ->get();

        foreach ($posts_by_tags as $post) {
            if(!$posts->where('id', $post->id)) {
                $posts->push($post);
            }
        }

        $res = [];
        foreach ($posts as $key => $post) {
            if ($this->userService->checkIfCanAccessToResource($post->author_id) && $this->userService->isUserUnblocked($post->author_id)) {
                foreach ($post->userPostParticipation as $userPostParticipation) {
                    $userPostParticipation->users;
                }
                $post->category;
                $post->tag;
                $post->like;
                $post->comment;
                $post->user->badge;
                $res[] = $post;
            }
        }

        return $res;
    }

    public function getPostsByTag(string $tag)
    {
        // Posts qui contiennent le tag suivant 
        $tagModel = Tag::where('label', $tag)->first();

        if (!$tagModel) {
            return null;
        }
        return $tagModel->posts;

    }

    public function loadPostData(Post $post)
    {
        $post->category;
        $post->tags->setHidden([
            "created_at",
            "updated_at",
            "pivot"
        ]);
        $post->like;
        $post->comment->load('users');
        $post->user->badge ?? null;
        return $post;
    }
}