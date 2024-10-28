<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;use Exception; Use DB;
use Illuminate\Support\Str;

class AccountController extends BaseController
{
    
    /**
     * Create Account API
     */
    public function create_account(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [ 
                'name' => 'required', 
                'email' => 'required|email|unique:users', 
                'password' => 'required', 
                'c_password' => 'required|same:password', 
            ]);
            if ($validator->fails()) { 
                return $this->sendError('Validation Error.', $validator->errors(), 200);
            }
            DB::beginTransaction();
            
            $uuid = Str::uuid();
            $input = $request->all();
            $input['uuid'] = $uuid;
            $user = User::create($input);
            
            $success['token'] =  $user->createToken('MyTailor')->plainTextToken; 
            $success['user_id'] =  $user->id;
            $success['name'] =  $user->name;
            
            DB::commit();
            
            return $this->sendResponse($success, 'User register successfully.');
            
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Something Went Wrong.', $e->getMessage(), 200);
        }
    }
    /**
     * Login User API
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required', 
                'password' => 'required', 
            ]);
            if ($validator->fails()) { 
                return $this->sendError('Validation Error.', $validator->errors(), 200);
            }
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return $this->sendError('Invalid login credentials', $credentials, 200);
            }
            $success['user'] = Auth::user();
            $success['token'] =  $success['user']->createToken('MyApp')->plainTextToken;
            
            return $this->sendResponse($success, 'User Login successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Something Went Wrong.', $e->getMessage(), 200);
        }
    }
}
