<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Reward;
use App\Models\Post;
use App\Models\Subscription;
use App\Models\UserPointCategory;
use App\Models\UserPostParticipation;
use App\Models\UserTrophy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'username',
        'email',
        'password',
        'image',
        'badge_id',
        'birthdate',
        'biography',
        'position',
        'is_private',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function badge()
    {
        return $this->belongsTo(Reward::class, 'badge_id');
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public function userPointCategory()
    {
        return $this->hasMany(UserPointCategory::class);
    }

    public function userTrophy()
    {
        return $this->hasMany(UserTrophy::class, 'user_id');
    }

    public function following()
    {
        return $this->hasMany(Subscription::class, 'follower_id');
    }

    public function follower()
    {
        return $this->hasMany(Subscription::class, 'following_id');
    }

    public function like()
    {
        return $this->hasMany(Like::class);
    }
    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public function userPostParticipation()
    {
        return $this->hasMany(UserPostParticipation::class, 'participant_id');
    }

    public function deleteUserActionsPosts($userId)
    {
        $posts = Post::where(['type' => 'action', 'author_id' => $userId]);
        if ($posts->count() > 0) {
            $posts->delete();
        }
    }
}