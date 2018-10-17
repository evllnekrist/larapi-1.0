<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = "articles";

    protected $fillable = ['user_id','title','slug','excerpts','body'];

    public function user(){
    	return $this->belongsTo('App\User');
    }
}
