<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    protected $fillable = [
    'id','title','content','user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
     }

     public function comment()
    {
        return $this->hasMany(Comment::class);
    }

}
