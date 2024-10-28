<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplainConversation extends Model
{
    use HasFactory;

    protected $fillable = ['complain_id','sender_id','reply_message','sender_role'];

    public function complain()
    {
        return $this->belongsTo(Complain::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
