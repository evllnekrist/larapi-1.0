<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MasterMenu extends Model
{
    protected $table = 'master_menu';
    // protected $primaryKey = 'id';

    protected $fillable = ['created_by','name','path','display_name','description'];

    public function user(){
    	return $this->belongsTo('App\User', 'created_by');
    }
}
