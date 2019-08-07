<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
      protected $fillable = [
        'id','content','user_id',
      ];

      protected $hidden = [
      
      ];
}
