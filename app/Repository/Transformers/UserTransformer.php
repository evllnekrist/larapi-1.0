<?php

namespace App\Repository\Transformers;

class UserTransformer extends Transformer{
	public function transform($user){
		return [
			'id' => $user->id,
			'fullname' => $user->name,
			'email' => $user->email,
			'sex' => $user->sex,
			'authority' => $user->authority,
			'institution' => $user->institution,
			'api_token' => $user->api_token,
		];
	}
}