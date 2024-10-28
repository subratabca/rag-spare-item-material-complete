<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['firstName','lastName','email','mobile','password','image','role','accept_registration_tnc','otp','is_email_verified'];
    protected $attributes = ['otp' => '0'];

    public function foods()
    {
        return $this->hasMany(Food::class);
    }

    public function scopeWithNonPendingFoodCount($query)
    {
        return $query->withCount(['foods' => function ($query) {
            $query->where('status', '!=', 'pending');
        }]);
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function complains()
    {
        return $this->hasMany(Complain::class);
    }

    public function ordersBasedOnRole()
    {
        return $this->hasMany(Order::class, 'client_id')->where('role', 'client')
            ->orWhere('user_id', $this->id)->where('role', 'user');
    }


}


