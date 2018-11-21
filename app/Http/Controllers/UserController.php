<?php

namespace App\Http\Controllers;
use App\User; 
use App\MasterMenu;
use App\MasterPermission;
use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use Response;
use App\Repository\Transformers\UserTransformer;
use \Illuminate\Http\Response as Res;
use Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Input;


class UserController extends ApiController
{
    protected $userTransformer;

    public function __construct(userTransformer $userTransformer){
    	$this->userTransformer = $userTransformer;
    }

    public function authenticate(Request $request){
    	$rules = array(
    		'email' => 'required|email',
    		'password' => 'required',
    	);

    	$validator = Validator::make($request->all(), $rules);

    	if($validator->fails()){
    		return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
    	}else{
    		$user = User::where('email', $request['email'])->first();
    		if($user){
    			$api_token = $user->api_token;
    			if($api_token == NULL){ //kalau belum pernah login
    				return $this->_login($request['email'], $request['password']);
    			}

    			try{
    				$user = JWTAuth::toUser($api_token);
    				return $this->respond([
    					'status' => 'success',
    					'status_code' => $this->getStatusCode(),
    					'message' => 'Already logged in',
    					'user' => $this->userTransformer->transform($user),
    				]);
    			}catch(JWTException $e){
    				$user->api_token = NULL;
    				$user->save();

    				return $this->respondInternalError("Login Unsuccessful. An error occurred while performing an action!");
    			}
    		}
    		else{
    			return $this->respondWithError("Invalid Email or Password");
    		}
    	}
    }

    private function _login($email, $password){
    	$credentials = ['email' => $email, 'password' => $password];

    	if(!$token = JWTAuth::attempt($credentials)){
    		return $this->respondWithError("User does not exist!");
    	}

    	$user = JWTAuth::toUser($token);

    	$user->api_token = $token;
    	$user->save();

    	return $this->respond([
    		'status' => 'success',
    		'status_code' => $this->getStatusCode(),
    		'message' => 'Login successful!',
    		'data' => $this->userTransformer->transform($user)
    	]);
    }

    public function register(Request $request){
    	$rules = array(
    		'name' => 'required|max:255',
    		'email' => 'required|email|max:255|unique:users',
            'sex' => 'required|max:10',
            'authority' => 'required',
            'institution' => 'required|max:255',
    		'password' => 'required|min:6|confirmed',
    		'password_confirmation' => 'required|min:6'
    	);

    	$validator = Validator::make($request->all(), $rules);

    	if($validator->fails()){
    		return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
    	}else{
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'sex' => $request['sex'],
                'authority' => $request['authority'],
                'institution' => $request['institution'],
                'password' => \Hash::make($request['password'])
            ]);
            return $this->_login($request['email'], $request['password']);
    	}
    }

    public function logout(Request $request){
    	try{
            $api_token = $request->bearerToken();
    		$user = JWTAuth::toUser($api_token);
    		$user->api_token = NULL;
    		$user->save();
    		JWTAuth::setToken($api_token)->invalidate();
    		$this->setStatusCode(Res::HTTP_OK);
    		return $this->respond([
    			'status' => 'success',
    			'status_code' => $this->getStatusCode(),
    			'message' => 'Logout successful!',
    		]);
    	}catch(JWTException $e){
    		return $this->respondInternalError("An error occurred while performing an action!");
    	}
    }

    public function list(){
        try{
            $limit = Input::get('limit') ?: 5;
            $users = User::paginate($limit);
            return $this->respondWithPagination(
                $users, 
                ['users' => $this->userTransformer->transformCollection($users->all())], 
                'Records Found!'
            );
        }catch(JWTException $e){
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }

    public function single($id){
        $user = User::find($id);

        if(!$user){
            $user = User::where('email', $id)->firstOrFail();
        }

        if(count($user) == 0){
            return $this->respondWithError("User Not Found");
        }

        return $this->respond([
            'status' => 'success',
            'status_code' => $this->getStatusCode(),
            'message' => 'Record Found',
            'user' => $this->userTransformer->transform($user)
        ]);
    }

    public function update(Request $request){
        $rules = array(
            'id' => 'required|integer',
            'name' => 'nullable|max:255',
            'email' => 'nullable|email|max:255',
            'sex' => 'nullable|max:10',
            'authority' => 'nullable',
            'institution' => 'nullable|max:255',
            'password' => 'nullable|min:6|confirmed',
            'password_confirmation' => 'nullable|min:3'
        );

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return $this->respondValidationError('Fields Validation Failed.', $validator->errors());
        }else{
            $api_token = $request->bearerToken();

            try{
                $user_updated = User::find($request['id']);
                if($request['name'] != null){$user_updated->name = $request['name'];}
                if($request['email'] != null){$user_updated->email = $request['email'];}
                if($request['sex'] != null){$user_updated->sex = $request['sex'];}
                if($request['authority'] != null){$user_updated->authority = $request['authority'];}
                if($request['institution'] != null){$user_updated->institution = $request['institution'];}
                if($request['password'] != null){
                    $user_updated->password = \Hash::make($request['password']);
                }
                $user_updated->save();

                return $this->respondCreated('User info updated successfully!', $this->userTransformer->transform($user_updated));
            }catch(JWTException $e){
                return $this->respondInternalError("An error occurred while performing an action!");
            }
        }
    }

    public function personal_menu(Request $request){
        try{
            $api_token = $request->bearerToken();
            $user = JWTAuth::toUser($api_token);
            $result = MasterPermission::with(
                array('masterMenu'=>function($query){
                    $query->select('id','name');
                }));
            $result = $result->where('master_permission.authority', $user->authority)->get();
            return $this->respondCreated('Menu retrieve succesfully ..', $result);

        }catch(JWTException $e){
            return $this->respondInternalError("An error occurred while performing an action!");
        }
    }


}
