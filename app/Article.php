<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    // protected $primaryKey = 'id';

    protected $fillable = ['created_by','title','slug','excerpts','body'];

    public function user(){
    	return $this->belongsTo('App\User', 'created_by');
    }
}
