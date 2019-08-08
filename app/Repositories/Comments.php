<?php namespace App\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Input;
use Redirect;
use Carbon\Carbon;
use DateTime;
use \App\Comment;
use Illuminate\Database\Eloquent\Collection as CollecionesEloquent;

class CommentRepository
{
    public function create($commentsData)
    {
        try {

            //iniciamos transaction
            DB::beginTransaction();

            $comment = new Comment();
            $comment->content = $commentsData->content;
            $comment->publication_id = $commentsData->publication;
            $comment->user_id = Auth::user()->id;
            $result = $comment->save();

            $data = array(
                'status' => 'success',
                'message'=>'El comentario se guardo correctamente'
                );

            DB::commit();
            return $data;
        } catch (\Exception $e) {
            DB::rollback();

            $data = array(
                'status' => 'danger',
                'message'=> 'Ocurrio un error comentado! '.$e->getMessage()
                );

            return $data;
        }
    }
}
