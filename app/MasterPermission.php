<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterPermission extends Model
{
    protected $table = 'master_permission';
    // protected $primaryKey = 'id';

    protected $fillable = ['menu','authority'];

    public function masterMenu(){
    	return $this->belongsTo('App\MasterMenu', 'menu');
    }
    public function masterAuthority(){
    	return $this->belongsTo('App\MasterAuthority', 'authority');
    }
}
