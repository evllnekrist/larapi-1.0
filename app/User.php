<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'users';
    // protected $primaryKey = 'id';

    protected $fillable = ['name','email','sex','authority','institution','password'];

    protected $hidden = [
        'password', 'api_token',
    ];

    public function articles(){
        return $this->hasMany('App\Article');
    }
    public function masterAuthority(){
        return $this->belongsTo('App\MasterAuthority', 'authority');
    }
}
