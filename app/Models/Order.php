<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','food_id','client_id','status','order_date','order_time','accept_order_request_tnc','approve_date','approve_time','accept_food_deliver_tnc','delivery_date','delivery_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function complain()
    {
        return $this->hasOne(Complain::class);
    }

}





