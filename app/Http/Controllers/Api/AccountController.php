<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
 use Exception;
 use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;


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
                return $this->sendError('Validation Error.', $validator->errors());
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
            return $this->sendError('Something Went Wrong.', $e->getMessage());
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
                return $this->sendError('Validation Error.', $validator->errors());
            }
            $credentials = $request->only('email', 'password');
            if (!Auth::attempt($credentials)) {
                return $this->sendError('Invalid login credentials', $credentials);
            }
            $success['user'] = Auth::user();
            $success['token'] =  $success['user']->createToken('MyApp')->plainTextToken;

            return $this->sendResponse($success, 'User Login successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Something Went Wrong.', $e->getMessage());
        }
    }
     /**
     * Send Code on Email API
     */
    public function send_code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::where('email', $request->email)->first();
        if (is_null($user)) {
            return $this->sendError('User not found.', $request->all());
        }

        $code = rand(1000, 9999);
        $user->email_verification_code = $code;
        $user->save();

        $success['user'] = $user;
        return $this->sendResponse($success, 'Code hase been sent successfully.');
    }
    /**
     * Verify Code on Email API
     */
    public function verify_code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'code' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::where('id', $request->user_id)->first();
        if (is_null($user)) {
            return $this->sendError('User not found.', $request->all());
        }
        if($user->email_verification_code != $request->code) {
            return $this->sendError('Code not Matached.', $request->all());
        }
        $success['user'] = $user;
        return $this->sendResponse($success, 'Code matched successfully.');
    }
    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => 'required|min:8'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::find($request->user_id);
        if (is_null($user)) {
            return $this->sendError('User not found.', $request->all());
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $success['user'] = $user;
        return $this->sendResponse($success, 'Password has been reset successfully.');
    }

    public function profile_upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::find($request->user_id);
        if (is_null($user)) {
            return $this->sendError('User not found.', $request->all());
        }

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('profile_images', $fileName, 'public/profiles');

            // Update the user's profile_image path
            $user->profile_image = $filePath;
            $user->save();

            $success['user'] = $user;
            return $this->sendResponse($success, 'Profile image uploaded successfully.');
        } else {
            return $this->sendError('Profile image not uploaded.');
        }
    }
}
