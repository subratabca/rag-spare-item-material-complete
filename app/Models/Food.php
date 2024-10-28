<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','name','gradients','description','expire_date','collection_date','start_collection_time','end_collection_time','address','latitude','longitude','image','accept_tnc','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function foodImages()
    {
        return $this->hasMany(FoodImage::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}

