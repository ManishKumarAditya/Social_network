<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeRegistrationMail;
use App\Models\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function store(Request $request)
    {
        // validate incoming request
        $validator = Validator::make($request->all(), [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'string', 'email', 'max:100', 'unique:customers'],
            'phone'           => ['required', 'digits:10', 'regex:/^[0-9]{10}$/'],
            'profile_image'   => ['nullable', 'mimes:jpg', 'max:10240'],
            'address'         => ['required', 'string', 'max:255'],
            'country_code'    => ['required', 'string', 'regex:/^\+\d{1,5}$/'],
        ]);

        if ($validator->fails()) {
            $response_data['errors'] = $validator->errors()->all();
            return response()->json(['data' => $response_data], 422);
        }

        $token = Str ::random(10);

        $user = [
            'name'          => $request['name'],
            'email'         => $request['email'],
            'phone'         => $request['phone'],
            'token'         => $token,
            'address'       => $request['address'],
            'country_code'  => $request['country_code'],
        ];

        // save supporting document in assets/uploads/customer_profile folder
        if ($request->hasFile('profile_image')) {
            $directory_assets_appointment = 'assets/uploads/customer_profile';
            File::isDirectory($directory_assets_appointment) or File::makeDirectory($directory_assets_appointment, 0777, true, true);

            $supporting_document = $request['profile_image'];
            $supporting_document_with_extension = changeFileName($supporting_document, 'customer', Carbon::now()->timestamp);
            $supporting_document->move($directory_assets_appointment, $supporting_document_with_extension);
            $user['profile_image'] = $supporting_document_with_extension;
        }

        $customers = Customer::create($user);

        // send welcome mail to the user
        $title = '[Social_network] Welcome User mail';
        $sendmail = Mail::to($user['email'])->send(new WelcomeRegistrationMail($title, $user['name'], $token));

        // response
        $response_data['message'] = 'Welcome mail sent on your email id.';
        return response()->json(['data' => $response_data], 201);
    }
}
