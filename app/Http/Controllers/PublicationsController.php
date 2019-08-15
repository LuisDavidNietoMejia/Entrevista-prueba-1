<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Clases\MyPublications;
use App\Repositories\PublicationRepository;
use App\Http\Requests\PublicationRequest;
use App\Publication;

class PublicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ObjPublications = new MyPublications();
        $publications = $ObjPublications->getAllPublications();

        return view('publications.index')->with(['publications' => $publications]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('publications.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PublicationRequest $request,PublicationRepository $publication)
    {
        $result = $publication->create($request);
        session()->flash($result['status'],$result['message']);
        return redirect()->action('PublicationsController@index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $publication = Publication::findorfail($id);
        $ObjPublications = new MyPublications();
        $comments = $ObjPublications->getAllCommentPublication($id);
        $countUser = $ObjPublications->getCountUserComment(Auth::user()->id, $id);
        return view('publications.show')->with(['publication' => $publication,'comments' => $comments,'countUser' => $countUser]);
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
