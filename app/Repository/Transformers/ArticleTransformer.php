<?php 

namespace App\Repository\Transformers;

class ArticleTransformer extends Transformer{
	public function transform($article){
		$userinfo = 'no user data';
		if($article->user != null){ //mencegah error apabila data object tidak ada
			$userinfo = [
				'id' => $article->user->id,
				'fullname' => $article->user->name,
				'email' => $article->user->email,
			];
		}
		return [
			'id' => $article->id, 
			'title' => $article->title,
			'slug' => $article->slug,
			'excerpts' => $article->excerpts,
			'body' => $article->body,
			'user' => $userinfo
		];
	}
}