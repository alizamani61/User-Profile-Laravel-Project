<?php

namespace App\Http\Controllers\Profile;

use App\User;
use App\Role;
use App\City;
use App\Province;
use App\Permission;
use App\Notification;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;


class ProfileController extends \App\Http\Controllers\Controller {

    /**
     * سازنده کلاس
     *
     * @return void
     */
    public function __construct() {
        /**************************************************************************************************************************
         * کاربر باید لاگین کرده باشد
         * *********************************************************************************************************************** */
        $this->middleware('auth');
    }

    /**
     * نمایش صفحه اصلی پروفایل
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        //تمام کاربرانی که نقش آنها ادمین است
        //را برمیگرداند
        //dd(User::whereIn('id',DB::table('users_roles')->where('role_id',1)->pluck('user_id')->toArray())->get());
        
        //لیست کاربران و نقش های آنها
        //dd(json_encode(User::with("roles")->get()));
        
        //$user_roles = DB::table("users")
        //                ->select('users.username', 'users_roles.role_id')
        //                ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
        //                ->get();
        
        //dd(json_encode($user_roles));
        
        $user = User::find(Auth::id());
        
        return view('profile.index', ['user' => $user, 'provinces' => Province::all(), 'city' => City::where('id', $user->city_id)->first()]);
    }

    /**
     * ذخیره سازی پروفایل
     * @param Request $request
     * @return type
     */
    public function store(Request $request) {
        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         * *********************************************************************************************************************** */
        $validator = Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            //'email' => 'required|email|unique:users,email,1', 
            'email' => ['required', 'email', Rule::unique('users')->ignore(Auth::id())],
            'province_id' => 'integer',
            'city_id' => 'integer',
            'marital_status' => 'integer',
            'gender' => 'integer',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.index')
                            ->withErrors($validator)
                            ->withInput();
        }

        /*************************************************************************************************************************
         * کاربر جاری
         * ********************************************************************************************************************** */
        $user = User::find(Auth::id());

        /*************************************************************************************************************************
         * آپلود تصویر پروفایل کاربر
         * ********************************************************************************************************************** */
        $avatar_filename = "";

        if ($request->hasFile('avatar')) {
            /**
             * فایل آواتار قبلی در صورت وجود
             * پاک شود
             */
            //$pav = public_path('images') . "/" . $user->avatar;

            //if (!empty($user->avatar) && file_exists($pav)) {
            //    unlink($pav);
            //}

            /**
             * در این قسمت اگر فایل آواتار آپلود شده باشد
             * و مجاز باشد ذخیره شده و نام فایل درون
             * متغیر قرار می گیرد
             */
            if (!Validator::make($request->all(), ['avatar' => 'mimes:png,jpg'])->fails()) {

                $a = $request->file('avatar');

                //$avatar_filename = $file->getClientOriginalName();
                $avatar_filename = 'av_' . $user->id . '_' . now()->timestamp . '.' . $a->getClientOriginalExtension();

                //$a->move(public_path('images'), $avatar_filename);
            }
        }
        
        /*************************************************************************************************************************
         * آپلود تصویر روی جلد کاربر
         * ********************************************************************************************************************** */
        $banner_filename = "";

        if ($request->hasFile('banner')) {
            /**
             * فایل آواتار قبلی در صورت وجود
             * پاک شود
             */
            //$pav = public_path('images') . "/" . $user->avatar;

            //if (!empty($user->avatar) && file_exists($pav)) {
            //    unlink($pav);
            //}

            /**
             * در این قسمت اگر فایل تصویر روی جلد آپلود شده باشد
             * و مجاز باشد ذخیره شده و نام فایل درون
             * متغیر قرار می گیرد
             */
            if (!Validator::make($request->all(), ['banner' => 'mimes:png,jpg'])->fails()) {

                $a = $request->file('banner');

                $banner_filename = 'ban_' . $user->id . '_' . now()->timestamp . '.' . $a->getClientOriginalExtension();

                //$a->move(public_path('images'), $banner_filename);
            }
        }

        /*************************************************************************************************************************
         * بروز رسانی پروفایل
         * ********************************************************************************************************************** */
        $user->firstname = $request->get('firstname');
        $user->lastname = $request->get('lastname');
        $user->email = $request->get('email');
        $user->gender = $request->get('gender');
        $user->marital_status = $request->get('marital_status');
        $user->province_id = $request->get('province_id'); //استان
        $user->city_id = $request->get('city_id'); //شهر
        $user->status = 1; // 1 => حالت ویرایش (در انتظار تایید)
        $user->avatar = ($avatar_filename != "" ? $avatar_filename : $user->avatar); //تصویر پروفایل
        $user->image_url = ($banner_filename != "" ? $banner_filename : $user->image_url); //تصویر روی جلد

        //if (!$user->save()) {
            /**
             * مشکل در ثبت
             */
        //    return redirect()->route('profile.index')->withErrors([__("messages.save-failure")]);
        //}
        
        /*************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         * ***********************************************************************************************************************/
        
        //ثبت اعلان
        //Helper::addNotification(1, __('messages.user-change-profile',['username' => $user->username, "section" => __('sections.personal-information')]), route('view.profile',['username' => $user->username]));
        
        return redirect()->route('profile.index')->withStatus(__("messages.save-successfully"));
    }

    
    /**
     * 
     * @param type $province_id
     * @return type
     */
    public function cities(Request $request){
        
        $cities = City::where('province_id', $request->get('province_id'))->get();
        
        return response()->json($cities); 
    }

}
