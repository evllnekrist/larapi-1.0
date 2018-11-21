<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterAuthority extends Model
{
    protected $table = 'master_authority';
    // protected $primaryKey = 'id';

    protected $fillable = ['created_by','name','display_name','description'];

    public function user(){
    	return $this->hasMany('App\User');
    }
}
