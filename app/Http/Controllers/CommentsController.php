<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Comments;
use App\Publication;
use App\User;
use App\Clases\MyPublications;
use App\Repositories\CommentRepository;
use App\Http\Requests\CommentsRequest;
use Mail;
use Redirect;
use Session;
use App\Mail\SengGridComments;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CommentsRequest $request, CommentRepository $commentObj)
    {
        try {
            
             //iniciamos transaction
            DB::beginTransaction();
            
            $ObjPublications = new MyPublications();
            $countUser = $ObjPublications->getValidateUserComment(Auth::user()->id, $request->publication);
           
            if ($countUser == 0) {
                $result = $commentObj->create($request);
            } else {
                $result = array('status' => 'danger', 'message' => 'Ya comento esta publicacion');

                session()->flash($result['status'], $result['message']);
    
                return redirect()->action(
                    'PublicationsController@show',
                    ['id' => $request->publication]
                );
            }
           
           
            $publication = Publication::findorfail($request->publication);
            $user = User::findorfail(Auth::user()->id);
            $comment = $request->content;
    
            Mail::to($publication->user->email)->send(new SengGridComments($user, $publication, $comment));
           
            db::commit();

            session()->flash($result['status'], $result['message']);
    
            return redirect()->action(
                'PublicationsController@show',
                ['id' => $request->publication]
            );
        } catch (\Exception $e) {
            
            db::rollback();

            $message = $e->getMessage();

            // dd($message);

            $data = array(
                'status' => 'danger',
                'message'=> substr($message, 2, 100)
            );
           
            Session::flash($data['status'], $data['message']);
          
            return back();
          
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
