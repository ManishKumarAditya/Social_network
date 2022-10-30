<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CreateUserMail;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create_user(Request $request) {
        // validate incoming request
        $validate_request = Validator::make($request->all(), [
            'token'     => ['required', 'string', 'max:30',
            function ($attribute, $value, $fail) {
                if (!DB::table('customers')->where($attribute, $value)->exists()) 
                {
                    return $fail("The provided $attribute does not exists.");
                }
            }
        ],
            'password'  => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ]);

        if($validate_request->fails()) {
            $response_data['errors'] = $validate_request->errors()->all();
            return response()->json(['data' => $response_data], 422);
        }

        if($customer = Customer::where('token', $request['token'])->first()) {
            $customer->user()->create([
                'name'      => $customer->name,
                'email'     => $customer->email,
                'password'  => Hash::make($request['password']),
            ]);

            $customer->token = NULL;
            $customer->save();
            // send login credentials in mail to admin 
            $create_user_mail = Mail::to($customer['email'])->send(new CreateUserMail($customer->name, $customer->email, $request['password'])); 
           
            // response
            $response_data['message'] = 'login credentials is sent on your email id.';
            return response()->json(['data' => $response_data], 201);
        }
    }
}
