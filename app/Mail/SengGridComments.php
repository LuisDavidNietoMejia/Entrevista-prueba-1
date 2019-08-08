<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\Publication as PublicationModel;

class SengGridComments extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $userComment;
    protected $publication;
    protected $comment;

    public function __construct(User $user, PublicationModel $publication,$comment)
    {
        $this->userComment = $user;
        $this->publication = $publication;        
        $this->comment = $comment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {       
        return $this->view('emails.comment')
        ->with(['userComment' => $this->userComment,        
        'comment' => $this->comment,
        'publication' => $this->publication
         ]);
    }
}
