<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Friend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    // method is used to approve friend request
    public function approve($id)
    {
        $user = Auth::user()->profile_id;
        // find friend id using id
        if (!$friend = Friend::where('friend_id', $user)->first()) {
            $response_data['errors'] = 'User not found';
            return response()->json(['data' => $response_data], 404);
        }

        // check whether appointment can be approved or not
        if ($friend->customer_id != $id || $friend->status != 0) {
            $response_data['errors'] = 'Bad Request';
            return response()->json(['data' => $response_data], 400);
        }

        $friend->update(['status' => 1]);

        // response
        $response_data['message'] = 'Accept Friend Request';
        $response_data['friend'] = $friend;
        return response()->json(['data' => $response_data], 200);
    }

    // method is used to send friend request to user
    public function send_request(Request $request)
    {
        // validate incoming request
        $validator = Validator::make($request->all(), [
            'friend_id'  => ['required', 'exists:customers,id', 'unique:friends,friend_id,NULL,NULL,friend_id,' . $request->friend_id . 
            ',customer_id,' . Auth::user()->profile_id],
        ]);

        if ($validator->fails()) {
            $response_data['errors'] = $validator->errors()->all();
            return response()->json(['data' => $response_data], 422);
        }

        $customer_id = Auth::user()->profile_id;

        $data = [
            'customer_id'    => $customer_id,
            'friend_id'      => $request['friend_id'],
            'status'         => 0,
        ];

        // store information into database
        $friend = Friend::create($data);

        // response
        $response_data['message'] = 'Send Friend Request';
        return response()->json(['data' => $response_data], 201);
    }

    public function see_request() {

        $user = Auth::user()->profile->id;

        // find friend id using id
        if (!$friend = Friend::where('friend_id', $user)->where('status', 0)->pluck('customer_id')) {
            $response_data['errors'] = 'User not found';
            return response()->json(['data' => $response_data], 404);
        }

        // fetch user by friend id
        $friends_id = Customer::whereIn('id', $friend)->get();

        // $users = Customer::whereIn('id', $friends_id)->where('token', NULL)->get();

        // response
        $response_data['friends'] = $friends_id;
        return response()->json(['data' => $response_data], 201);
    }

    public function see_mutual_friend($id) {

        // find friend id using id
        if (!$customer = Customer::find($id)) {
            $response_data['errors'] = 'User not found';
            return response()->json(['data' => $response_data], 404);
        }

        $friends  = $customer->friends()->where('status', 1)->pluck('friend_id')->toArray();

        $user = Auth::user()->profile;

        $logged_in_user = $user->friends()->pluck('friend_id')->toArray();

        $result = array_intersect($friends, $logged_in_user);

        // fetch mutual friends 
        $mutual_friends = Customer::whereIn('id', $result)->get();

        // response
        $response_data['mutual_friends'] = $mutual_friends;
        return response()->json(['data' => $response_data], 201);
    }
}
