<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    use HasFactory;

    protected $fillable = ['order_id','food_id','user_id','message','status','cmp_date','cmp_time','clnt_cmp_date','clnt_cmp_time','clnt_cmp_feedback_date','clnt_cmp_feedback_time'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function conversations()
    {
        return $this->hasMany(ComplainConversation::class);
    }

}
