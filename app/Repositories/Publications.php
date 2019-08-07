<?php namespace App\Repositories;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Validator;
use Input;
use Redirect;
use Carbon\Carbon;
use DateTime;
use \App\Publication;
use Illuminate\Database\Eloquent\Collection as CollecionesEloquent;

class PublicationRepository
{
    public function create($publicationData)
    {
        try {

            //iniciamos transaction
            DB::beginTransaction();

            $Publication = new Publication();
            $Publication->title = $publicationData->title;
            $Publication->contenT = $publicationData->content;
            $Publication->user_id = Auth::user()->id;
            $result = $Publication->save();
            $data = array(
                'status' => 'success',
                'message'=>'La publicacion '. $Publication->title . ' se guardo correctamente'                
                );

            DB::commit();
            return $data;

        } catch (\Exception $e) {
            
            DB::rollback();

            $data = array(
                'status' => 'danger',
                'message'=> 'Ocurrio un error registrando! '.$e->getMessage()                
                );

            return $data;
        }
    }
}
