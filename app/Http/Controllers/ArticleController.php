<?php

namespace App\Http\Controllers;
use App\User;
use App\Article;
use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use Response;
use App\Repository\Transformers\ArticleTransformer;
use \Illuminate\Http\Response as Res;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Input;

class ArticleController extends ApiController
{
    protected $articleTransformer;

    public function __construct(ArticleTransformer $articleTransformer){
    	$this->articleTransformer = $articleTransformer;
    }

    public function index(){
    	$limit = Input::get('limit') ?: 5;
    	$articles = Article::with('user')->paginate($limit);
    	return $this->respondWithPagination(
            $articles, 
            ['articles' => $this->articleTransformer->transformCollection($articles->all())], 
            'Records Found!'
        );
    }

    public function show($id){
    	$article = Article::with('user')->find($id);

    	if(!$article){
    		$article = Article::where('slug', $id)->firstOrFail();
    	}

    	if(count($article) == 0){
    		return $this->respondWithError("Article Not Found");
    	}

    	return $this->respond([
    		'status' => 'success',
    		'status_code' => $this->getStatusCode(),
    		'message' => 'Record Found',
    		'article' => $this->articleTransformer->transform($article)
    	]);
    }

    public function store(Request $request){
    	$rules = array(
    		'title' => 'required',
    		'slug' => 'required|unique:articles',
    		'excerpts' => 'required',
    		'body' => 'required',
    	);

    	$validator = Validator::make($request->all(), $rules);

    	if($validator->fails()){
    		return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
    	}

    	$token =  $request->bearerToken(); //harus login dulu

    	try{
    		$user = JWTAuth::toUser($token);

    		$article = new Article();
    		$article->user_id = $user->id;
    		$article->title = $request['title'];
    		$article->slug = $request['slug'];
    		$article->excerpts = $request['excerpts'];
    		$article->body = $request['body'];
    		$article->save();

    		return $this->respondCreated("Article created successfully!", $this->articleTransformer->transform($article));
    	}catch(JWTException $e){
    		return $this->respondInternalError("An error occurred while performing an action!");
    	}
    }

    public function update(Request $request){
    	$rules = array(
    		'id' => 'required|integer',
    		'title' => 'required',
    		'slug' => 'required|unique:articles',
    		'excerpts' => 'required',
    		'body' => 'required'
    	);

    	$validator = Validator::make($request->all(), $rules);

    	if($validator->fails()){
    		return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
    	}

    	$token = $request->bearerToken();

    	try{
    		$user = JWTAuth::toUser($token);

    		$article = Article::find($request['id']);
    		$article->user_id = $user->id;
    		$article->title = $request['title'];
    		$article->slug = $request['slug'];
    		$article->excerpts = $request['excerpts'];
    		$article->body = $request['body'];
    		$article->save();

    		return $this->respondCreated('Article updated successfully!', $this->articleTransformer->transform($article));
    	}catch(JWTException $e){
    		return $this->respondInternalError("An error occurred while performing an action!");
    	}
    }

    public function delete($id, Request $request){
        $token = $request->bearerToken();
        $user = JWTAuth::toUser($token);
    	try{
    		$article = Article::where('id', $id)->where('user_id', $user->id); 
    		$article->delete();

    		return $this->respond([
    			'status' => 'success',
    			'status_code' => $this->getStatusCode(),
    			'message' => 'Article deleted successfully!'
    		]);
    	}catch(JWTException $e){
    		return $this->respondInternalError("An error occurred while performing an action!");
    	}
    }
}
