<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'id','content','status','publication_id','user_id',
      ];

    protected $hidden = [
      
      ];

      public function user(){
        return $this->belongsTo(User::class);
     }

     public function publication(){
      return $this->belongsTo(Publication::class);
   }
}
