<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['name','email','phone1','phone2','logo','address','city','country','zip_code','facebook','linkedin','youtube','description','refund','terms','privacy'];
}
