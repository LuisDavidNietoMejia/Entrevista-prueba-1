<?php namespace App\Clases;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Input;
use Redirect;
use Carbon\Carbon;
use DateTime;
use \App\Publication;
use \App\Comment;
use \App\User;
use Illuminate\Database\Eloquent\Collection as CollecionesEloquent;

class MyPublications
{
    protected static $MypublicationId;
    protected static $DateNow;

    public function __construct($MypublicationId = "")
    {
        self::$MypublicationId = publication::find($MypublicationId);
        $date = Carbon::now();
        self::$DateNow = $date->toDateString();
        // self::$Persons = null;
    }

    public static function getAllPublications() : CollecionesEloquent
    {
        // obtener todos los datos de las publicaciones:
        $publicaciones = null;

        $publicaciones = Publication::all();

        return $publicaciones;
    }

    public static function getAllCommentPublication($id) : CollecionesEloquent
    {
        
       // obtener todos los comentarios de una publicacion:
        $comments = Publication::find($id)->comment;
    
        return $comments;
    }

    public static function getValidateUserComment($user, $publication) : int
    {
        // obtener todos los comentarios de una publicacion:
        // $comments = Publication::find($id)->comment
        $commentsUser = Comment::join('users', 'comments.user_id', '=', 'users.id')
                  ->join('publications', 'comments.publication_id', 'publications.id')
                  ->where('publications.id', '=', $publication)
                     ->where('users.id', '=', $user)
                  ->count();
                               
        return $commentsUser;
    }

    public static function getCommentsHola()
    {
        $commentsHola = Publication::join('comments', 'publications.id', '=', 'comments.publication_id')
        ->where('comments.content', 'like', 'hola')
        ->where('comments.status', '=', 'Aprobado')
        ->get();
    }
}
