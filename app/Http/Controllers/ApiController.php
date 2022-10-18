<?php

namespace App\Http\Controllers;

use App\Models\Detail;
use App\Models\MonthlyWinner;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscription;
use App\Models\ContactUs;
use Cron\MonthField;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Support\Facades\Hash;

class ApiController extends Controller
{
    //=============================== User Login Api==========================
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:255',
            'password' => 'required'
        ];

        $validator = FacadesValidator::make($request->all(), $rules);

        if ($validator->fails()) {
            $err = $validator->errors()->getMessages();
            $msg = array_values($err)[0][0];
            $res['status'] = false;
            $res['message'] = $msg;

            return response()->json($res);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                $res['status'] = true;
                $res['message'] = "Password Matched! You have Login successfully!";
                $res['data'] = User::find($user->id);
                return response()->json($res);
            } else {

                $res['status'] = false;
                $res['message'] = "Password mismatch";
                return response()->json($res);
            }
        } else {

            $res['status'] = false;
            $res['message'] = "User does not exist";
            return response()->json($res);
        }
    }

    //=========================== Add Users Api ======================================
    public function add_users(Request $request)
    {
        if ($request->file('profile_img') == null) {
            $image_name = "";
        } else {
            $path_title = $request->file('profile_img')->store('public/images');

            $image_name = basename($path_title);
        }

        $rules = [
            'profile_img' => 'required',
            'first_name' => 'required',
            'last_name' => 'required ',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'required ',
            'address' => 'required',


        ];

        $validator = FacadesValidator::make($request->all(), $rules);
        if ($validator->fails()) {
            $err = $validator->errors()->getMessages();
            $msg = array_values($err)[0][0];
            $res['status'] = false;
            $res['message'] = $msg;

            return response()->json($res);
        }
        $request['password'] = Hash::make($request['password']);
        // $request['remember_token'] = Str::random(10);
        //$users = User::create($request->all());
        $users = new User();
        $users->profile_img = "images/" . $image_name;
        $users->first_name     = $request->first_name;
        $users->last_name = $request->last_name;
        $users->email = $request->email;
        $users->password = $request->password;
        $users->phone = $request->phone;
        $users->address = $request->address;
        $users->save();

        if (is_null($users)) {

            $res['status'] = false;
            $res['message'] = "User Can't Insert Sucessfully";
            return response()->json($res);
        } else {

            $userss = User::where('email', $request->email)->first();
            $res['status'] = true;
            $res['message'] = "User Insert Sucessfully";
            $res['data'] = $userss;
            return response()->json($res);
        }
        return response()->json($users);
    }

    //================== Edit Users Api ====================================
    public function edit_user(Request $request)
    {

        $finduser = User::find($request->user_id);

        if ($request->file('profile_img') == null) {

            $image_name = $finduser->profile_img;
        } else {

            $path_title = $request->file('profile_img')->store('public/images');

            $image_name = "images/" .  basename($path_title);
        }

        if (is_null($finduser)) {

            $res['status'] = false;
            $res['message'] = "User not found";
            return response()->json($res);
        }

        $rules = [

            // 'first_name' => 'required',
            // 'last_name' => 'required ',
            // 'phone' => 'required ',
            // 'address' => 'required',


        ];

        $validator = FacadesValidator::make($request->all(), $rules);
        if ($validator->fails()) {
            $err = $validator->errors()->getMessages();
            $msg = array_values($err)[0][0];
            $res['status'] = false;
            $res['message'] = $msg;

            return response()->json($res);
        }
        $request['password'] = Hash::make($request['password']);
        // $request['remember_token'] = Str::random(10);
        //$users = User::create($request->all());
        $users = User::find($request->user_id);
        $users->profile_img = $image_name;
        $users->first_name = $request->first_name;
        $users->last_name = $request->last_name;
        $users->phone = $request->phone;
        $users->address = $request->address;
        $users->save();

        if (is_null($users)) {

            $res['status'] = false;
            $res['message'] = "User Can't Updated Sucessfully";
            return response()->json($res);
        } else {

            $users = User::find($request->user_id);
            $res['status'] = true;
            $res['message'] = "User Updated Sucessfully";
            $res['data'] = $users;
            return response()->json($res);
        }
        return response()->json($users);
    }

    //=================================Details Api =============================================
    public function details()
    {

        $details = Detail::all();
        $data = [];
        if (is_null($details)) {

            $res['status'] = false;
            $res['message'] = "Details Not Found!";
            return response()->json($res, 404);
        } else {

            foreach ($details as $que) {

                $query = Detail::where('details.id', $que->id)
                    ->join('detail_imgs', 'details.id', '=', 'detail_imgs.detail_id')
                    ->select('detail_imgs.det_img', 'detail_imgs.id')->get();
                //dd($query);
                if (is_null($query)) {
                    $res['status'] = false;
                    $res['message'] = "details_images Not Found!";
                    return $res;
                }
                $que->details_images = $query;
                array_push($data, $que);
            }

            $res['status'] = true;
            $res['message'] = "details_list";
            $res['data'] = $data;
            return $res;
        }
    }

    //========================Subscription Api =======================================
    public function subscription(Request $request)
    {
        //dd($request);
        $rules = ([
            'user_id' => 'required',
            'amount' => 'required',
            'date' => 'required',
            'status' => 'required',
        ]);
        $validator = FacadesValidator::make($request->all(), $rules);
        if ($validator->fails()) {
            $err = $validator->errors()->getMessages();
            $msg = array_values($err)[0][0];
            $res['status'] = false;
            $res['message'] = $msg;

            return response()->json($res);
        }


        $subscription = new Subscription();
        $subscription->user_id = $request->user_id;
        $subscription->amount = $request->amount;
        $subscription->date = $request->date;
        $subscription->status = $request->status;
        $subscription->save();

        if (is_null($subscription)) {

            $res['status'] = false;
            $res['message'] = "Subscription Can't Insert Sucessfully";
            return response()->json($res);
        } else {

            $res['status'] = true;
            $res['message'] = "Subscription data Insert Sucessfully";
            $res['data'] = $subscription;
            return response()->json($res);
        }
    }

    public function winners()
    {
        $month1 = date('F'); // Get month name

        $winners_list = MonthlyWinner::whereNotIn('winning_month', [$month1])->get();

        $winner = MonthlyWinner::where('winning_month', [$month1])->get();



        $res['status'] = true;
        $res['Winner'] = $winner;
        $res['Previous_winner_list'] = $winners_list;
        return response()->json($res);
    }

    public function subscription_against_user(Request $request)
    {
        $subs = Subscription::where('user_id', $request->user_id)->get();

        $res['status'] = true;
        $res['message'] = "subscription_against_user  list ";
        $res['data'] = $subs;
        return response()->json($res);
    }

    //========================Contact us Api =======================================
    public function contact(Request $request)
    {
        $rules = ([
            'user_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'message' => 'required',
        ]);
        $validator = FacadesValidator::make($request->all(), $rules);
        if ($validator->fails()) {
            $err = $validator->errors()->getMessages();
            $msg = array_values($err)[0][0];
            $res['status'] = false;
            $res['message'] = $msg;

            return response()->json($res);
        }


        $contact = new ContactUs();
        $contact->user_id = $request->user_id;
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->message = $request->message;
        $contact->save();

        if (is_null($contact)) {

            $res['status'] = false;
            $res['message'] = "contact us data Can't Insert Sucessfully";
            return response()->json($res);
        } else {

            $res['status'] = true;
            $res['message'] = "contact us  data Insert Sucessfully";
            $res['data'] = $contact;
            return response()->json($res);
        }
    }

    public function forgot_password(Request $request){

        $rules = ([
            'email' => 'required|string|email|max:255',
        ]);
        $validator = FacadesValidator::make($request->all(), $rules);
        if ($validator->fails()) {
            $err = $validator->errors()->getMessages();
            $msg = array_values($err)[0][0];
            $res['status'] = false;
            $res['message'] = $msg;

            return response()->json($res);
        }

        $update_pass = User::where('email', $request->email)->first();
        // return $update_pass;
        if (is_null($update_pass)) {

            $res['status'] = false;
            $res['message'] = "User Can't Exist";
            return response()->json($res);

        } else {
            $code = random_int(10000000, 99999999);

            Mail::to($request->email)->send(new PasswordReset($code));


            $hashed_random_password = Hash::make($code);
            // return $hashed_random_password;

            $update_pass = User::where('email',  $request->email)->first();
            $update_pass->password = $hashed_random_password;
            $update_pass->save();

            $res['status'] = True;
            $res['message'] = "Password Send to its email";
            return response()->json($res);

        }
    }

    public function pass_update(Request $request){

        $rules = ([
            'password' => 'required|min:8',
        ]);
        $validator = FacadesValidator::make($request->all(), $rules);
        if ($validator->fails()) {
            $err = $validator->errors()->getMessages();
            $msg = array_values($err)[0][0];
            $res['status'] = false;
            $res['message'] = $msg;

            return response()->json($res);
        }

        $hashed_password = Hash::make($request->password);
        

        $password = User::find($request->user_id);
        $password->password = $hashed_password;
        $password->save();

        $res['status'] = True;
        $res['message'] = "Password Updated Sucessfully!!";
        return response()->json($res);

    }
}
