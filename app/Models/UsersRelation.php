<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersRelation extends Model
{
    use HasFactory;

    
    protected $table = 'users_relation';

    protected $fillable = [
        'id',
        'follower_id',
        'following_id',
        'status',
    ];

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function following()
    {
        return $this->belongsTo(User::class, 'following_id');
    }

}
