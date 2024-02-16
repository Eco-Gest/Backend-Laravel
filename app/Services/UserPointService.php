<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\UserPointCategory;
use App\Models\Reward;
use App\Models\UserTrophy;
use DateTime;

class UserPointService
{
    private function newTrophy(UserPointCategory $userPointCategory)
    {
        $userTrophy = UserTrophy::create([
            'user_id' => $userPointCategory->user_id,
            'category_id' => $userPointCategory->category_id,
        ]);
        $userTrophy->save();
    }

    public function updateUserCurrentPointCategory(Post $post, ?UserPointCategory $userPointCategory)
    {
        $userPointCategory = $this->setUserPointCategory($userPointCategory, $post);
        $nbDays = $this->getPostNbDays($post->start_date, $post->end_date, $post->type);

        $nbPoint = $userPointCategory->current_point + (self::getLevelInPoints($post->level) * $nbDays);
        $reward = Reward::where('type', 'trophy')->firstOrFail();

        if ($nbPoint <= $reward->point) {
            $userPointCategory->current_point = $nbPoint;
        } else {
            $newCurrentPoint = $nbPoint;
            while ($newCurrentPoint >= $reward->point) {
                $newCurrentPoint = $newCurrentPoint - $reward->point;
                self::newTrophy($userPointCategory);
            }
            $userPointCategory->current_point = $newCurrentPoint;
            $userPointCategory->total_point += $nbPoint - $newCurrentPoint;
        }
        $userPointCategory->save();
    }

    public function updateUserCurrentPointCategoryPostUpdated(Post $post, array $updatedPost, ?UserPointCategory $userPointCategory)
    {
        $nbDays = $this->getPostNbDays($post->start_date, $post->end_date, $post->type);
        $updatedPostNbDays = $this->getPostNbDays($updatedPost['start_date'] ?? "", $updatedPost['end_date'] ?? "", $updatedPost['type']);

        if (
            $nbDays != $updatedPostNbDays ||
            $updatedPost['level'] != $post->level ||
            $updatedPost['category_id'] != $post->category_id
        ) {

            /* Category different 
             * remove user point in old category
             * add  user point in new category
             */
            if ($updatedPost['category_id'] != $post->category_id) {
                $userPointOldCategory = UserPointCategory::where('user_id', $post->author_id)->where('category_id', $post->category_id)->first();
                $userPointOldCategory->current_point -= (self::getLevelInPoints($post->level) * $nbDays);
                $userPointOldCategory->save();
                $updatedPost['author_id'] = $post->author_id;
                $updatePostModel = new Post($updatedPost);
                $this->updateUserCurrentPointCategory($updatePostModel, $userPointCategory);
            }
            // same category
            else {
                $nbPoint = self::getLevelInPoints($post->level) * $nbDays;
                $nbPointUpdated = self::getLevelInPoints($updatedPost['level']) * $updatedPostNbDays;

                if ($nbPoint > $nbPointUpdated) {
                    $userPointCategory->current_point = $userPointCategory->current_point - ($nbPoint - $nbPointUpdated);
                } else {
                    $userPointCategory->current_point = $userPointCategory->current_point + ($nbPointUpdated - $nbPoint);
                }
                $userPointCategory->save();
            }
        }
    }

    private static function getLevelInPoints(string $level)
    {
        switch ($level) {
            case 'easy':
                return 10;
            case 'medium':
                return 20;
            case 'hard':
                return 30;
        }
    }

    public function setNewBadge(User $user)
    {
        $userPointCategories = UserPointCategory::select('total_point')->where('user_id', $user->id)->get();
        $userTotalPoints = 0;
        foreach ($userPointCategories as $userPointCategory) {
            $userTotalPoints += $userPointCategory->total_point;
        }
        $whereData = [
            ['type', 'badge'],
            ['point', '<=', $userTotalPoints]
        ];
        $reward = Reward::orderBy('point', 'DESC')->where($whereData)->firstOrFail();
        $user->badge_id = $reward->id;
        $user->save();
    }
    public function userTotalPoints($userId): int
    {
        $user = User::where("id", $userId)->firstOrFail();

        $userPointCategories = UserPointCategory::select('total_point')->where('user_id', $user->id)->get();
        $userTotalPoints = 0;
        foreach ($userPointCategories as $userPointCategory) {
            $userTotalPoints += $userPointCategory->total_point;
        }
        return $userTotalPoints;
    }

    private function getPostNbDays(?string $start_date, ?string $end_date, ?string $type): int
    {
        if (isset($start_date) && isset($end_date) && $type == 'challenge') {
            $start_date = new DateTime(date("Y-m-d", strtotime($start_date)));
            $end_date = new DateTime(date("Y-m-d", strtotime($end_date)));
            return $start_date->diff($end_date)->days + 1;
        }
        return 1;
    }

    private function setUserPointCategory(?UserPointCategory $userPointCategory, Post $post): UserPointCategory
    {
        $userPointCategory = UserPointCategory::where(['user_id' => $post->author_id, 'category_id' => $post->category_id])->first();
        if ($userPointCategory == null) {
            $userPointCategory = UserPointCategory::create(['user_id' => $post->author_id, 'category_id' => $post->category_id, 'current_point' => 0, 'total_point' => 0]);
            $userPointCategory->save();
        }
        return $userPointCategory;
    }
}