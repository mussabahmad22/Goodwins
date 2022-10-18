<?php

namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Detail;
use App\Models\DetailImg;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\MonthlyWinner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{

    public function logout()
    {

        Session::flush();
        Auth::logout();
        return redirect('login');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function users()
    {
        $users =  User::where('remember_token', NULL)->get();
        return view('users', compact('users'));
    }

    public function delete_user(Request $request)
    {
        $user_id = $request->delete_user_id;
        $subs = Subscription::where('user_id', $user_id);
        $subs->delete();
        $contact = ContactUs::where('user_id', $user_id);
        $contact->delete();
        $users = User::findOrFail($user_id);
        $users->delete();
        return redirect(route('users'))->with('error', 'User Deleted successfully');
    }

    public function details()
    {
        $detail = Detail::all();

        return view('details', compact('detail'));
    }

    public function add_detail(Request $request)
    {

        $request->validate([
            'detail_title' => 'required',
            'desc' => 'required',
            'img' => 'required',
        ]);

        $detail = new Detail();
        $detail->detail_title = $request->detail_title;
        $detail->detail_desc = $request->desc;
        $detail->save();

        if ($request->file('img') == null) {

            $prud_imgs = "";
        } else {

            $files = $request->file('img');

            foreach ($files as $file) {

                $path_title = $file->store('public/images');

                $det_imgs = basename($path_title);

                $detail_img = new DetailImg();
                $detail_img->detail_id = $detail->id;
                $detail_img->det_img = "images/" .  $det_imgs;
                $detail_img->save();
            }
        }
        return redirect(route('details',  compact('detail')))->with('success', 'Details Added successfully');
    }

    public function edit_detail($id)
    {
        $detail = Detail::find($id);
        return response()->json([
            'status' => '200',
            'detail' => $detail,
        ]);
    }

    public function detail_update(Request $request)
    {

        $request->validate([

            'detail_title' => 'required',
            'desc' => 'required',

        ]);

        $detail_id = $request->query_id;

        $detail = Detail::findOrFail($detail_id);
        $detail->detail_title = $request->detail_title;
        $detail->detail_desc = $request->desc;
        $detail->save();

        if ($request->file('img') == null) {

            $det_imgs = "";
        } else {

            $delete_imgs = DetailImg::where('detail_id', $request->query_id);
            $delete_imgs->delete();

            $files = $request->file('img');

            foreach ($files as $file) {

                $path_title = $file->store('public/images');

                $det_imgs = basename($path_title);

                $details = new DetailImg();
                $details->detail_id = $detail->id;
                $details->det_img = "images/" .  $det_imgs;
                $details->save();
            }
        }

        return redirect()->back()->with('success', 'Details Updated successfully');
    }

    public function delete_detail(Request $request)
    {
        $detail_id = $request->delete_detail_id;
        $detail_img = DetailImg::where('detail_id', $detail_id);
        $detail_img->delete();
        $detail = Detail::findOrFail($detail_id);
        $detail->delete();
        return redirect(route('details'))->with('error', 'Detail Deleted successfully');
    }


    public function winners()
    {
        $winner = MonthlyWinner::all();
        return view('winners', compact('winner'));
    }

    public function delete_winner(Request $request)
    {
        $winner_id = $request->delete_winner_id;
        $winner = MonthlyWinner::findOrFail($winner_id);
        $winner->delete();
        return redirect(route('winners'))->with('error', 'Winner Deleted successfully');
    }

    public function subscription($id = 0)
    {
        $month = $id;
        $subs = DB::table('subscriptions')
            ->join('users', 'subscriptions.user_id', '=', 'users.id')
            ->SelectRaw("subscriptions.* , MONTH(date) as month, users.profile_img, users.email, users.phone, users.first_name");
        if ($id != 0) {
            $subs->whereRaw("MONTH(date)=$month");
        }
        $subscription = $subs->get();
        return view('subscription', compact('subscription', 'month'));
    }


    public function lucky_draw()
    {
        $month = date('m'); // Get current month
        $month1 = date('F'); // Get month name

        $winner_month = Subscription::whereRaw("MONTH(date)=$month")->get();
        if (count($winner_month) > 0) {

            $subs = DB::table('subscriptions')
                ->join('users', 'subscriptions.user_id', '=', 'users.id')
                ->SelectRaw("subscriptions.* , MONTH(date) as month, users.profile_img, users.email, users.phone, users.first_name")->whereRaw("MONTH(date)=$month")->get()->random();


            $winner_month = MonthlyWinner::where('winning_month', $month1)->count();

            if ($winner_month == 1) {

                $res['status'] = False;
                $res['message'] = "You have Already Announced Winner Of this month ";
                return $res;
                
            } else {

                $winner = new MonthlyWinner();
                $winner->profile_img = $subs->profile_img;
                $winner->username = $subs->first_name;
                $winner->email = $subs->email;
                $winner->phone = $subs->phone;
                $winner->winning_month = $month1;
                $winner->save();

                $res['status'] = True;
                $res['message'] = "Winner of this Month ";
                $res['Winner Data'] = $winner;
                return $res;
            }
        } else {

            $res['status'] = False;
            $res['message'] = "Please Add User Subscription Of this Month!! ";
            return $res;
        }
    }


    public function delete_subscription(Request $request)
    {
        $subs_id = $request->delete_subs_id;
        $subs = Subscription::findOrFail($subs_id);
        $subs->delete();
        return redirect(route('subscription'))->with('error', 'Subscription Deleted successfully');
    }

    public function contact()
    {
        $contact = ContactUs::all();
        return view('contact', compact('contact'));
    }


    public function delete_contact(Request $request)
    {
        $contact_id = $request->delete_contact_id;
        $contact = ContactUs::findOrFail($contact_id);
        $contact->delete();
        return redirect(route('contact'))->with('error', 'Contact Deleted successfully');
    }
}
