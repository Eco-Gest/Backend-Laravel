<?php

namespace App\Models;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public $timestamp = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
     protected $fillable = [
        'id',
        'label',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'tag_post', 'post_id', 'tag_id');
    }
}
