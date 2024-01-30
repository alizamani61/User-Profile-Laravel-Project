<?php

namespace App\Http\Controllers\Profile;

use App\User;
use App\Resume;
use App\Permission;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ResumeController extends \App\Http\Controllers\Controller
{
    /**
     * سازنده کلاس
     *
     * @return void
     */
    public function __construct()
    {
        /**************************************************************************************************************************
         * کاربر باید لاگین کرده باشد
         *************************************************************************************************************************/
        $this->middleware('auth');
    }
    
    /**
     * نمایش صفحه اصلی پروفایل
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::where('id', '=', Auth::id())->first();
        
        return view('profile.resume', ['user' => $user, 'resume' => Resume::firstOrNew(['user_id' => Auth::id()])]);
    }
    
    /**
     * بروز رسانی رزومه کاربر جاری
     * @param Request $request
     * @param type $id
     * @return type
     */
    public function update(Request $request, Resume $resume)
    {
        /*************************************************************************************************************************
         * کاربر جاری
         * ********************************************************************************************************************** */
        $user = User::find(Auth::id());
        
        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        Validator::extend('requireArray', function ($attribute, $value, $parameters, $validator){
            foreach ($value as $v){
                if(empty($v)) return false;
            }

            return true;
        });
        
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'work_position' => 'requireArray',
            'work_place' => 'requireArray',
            'work_fromdate' => 'requireArray',
            'work_todate' => 'requireArray',         
            'edu_degree' => 'requireArray',
            'edu_place' => 'requireArray',
            'edu_fromdate' => 'requireArray',
            'edu_todate' => 'requireArray',
         ]);
        
        if ($validator->fails())
            return redirect()->route('profile.resume.index')->withErrors($validator)->withInput();
        
        $work_temp_arr = array();
        /**************************************************************************************************************************
         * خروجی مشابه
         * [{"work_position":"5555","work_place":"5555","work_fromdate":"5555","work_todate":"5555"}]
         *************************************************************************************************************************/
        
        if($request["work_position"]){
            for ($v=0; $v < count($request["work_position"]); $v++){
                array_push($work_temp_arr, [
                    'work_position' => $request["work_position"][$v], 
                    'work_place' => $request["work_position"][$v], 
                    'work_fromdate' => $request["work_fromdate"][$v], 
                    'work_todate' => $request["work_todate"][$v],
                    'work_desc' => $request["work_desc"][$v],
                    ]
                );
            }
        }
        
        $edu_temp_arr = array();
        /**************************************************************************************************************************
         * خروجی مشابه
         * [{"work_position":"5555","work_place":"5555","work_fromdate":"5555","work_todate":"5555"}]
         *************************************************************************************************************************/
        
        if($request["edu_degree"]){
            for ($v=0; $v < count($request["edu_degree"]); $v++){
                array_push($edu_temp_arr, [
                    'edu_degree' => $request["edu_degree"][$v], 
                    'edu_place' => $request["edu_place"][$v], 
                    'edu_fromdate' => $request["edu_fromdate"][$v], 
                    'edu_todate' => $request["edu_todate"][$v],
                    ]
                );
            }
        }
        /**************************************************************************************************************************
         * ویرایش رزومه
         *************************************************************************************************************************/
        //این کار برای اینست که برای هر کاربر
        //چند رکورد در دیتابیس ذخیره نشود
        //هر کاربر یک رکورد برای رزومه
        $r = Resume::where('user_id', Auth::id())->first();
        
        $resume = $r? $r : $resume;
        
        $resume->user_id = Auth::id();
        $resume->status = 1; // 1 => حالت ویرایش (در انتظار تایید)
        $resume->title = $request->get("title");
        $resume->work_records = json_encode($work_temp_arr);
        $resume->education_records = json_encode($edu_temp_arr);
        
        //if(!$resume->save()){
            /**
             * مشکل در ثبت
             */
        //    return redirect()->route('profile.resume.index')->withErrors([__("messages.save-failure")]);
        //}

        
        /**************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         *************************************************************************************************************************/
        //ثبت اعلان
        //Helper::addNotification(1, __('messages.user-change-profile',['username' => $user->username, "section" => __('sections.resume')]), route('view.profile.resume',['username' => $user->username]));
        
        return back()->withStatus(__("messages.save-successfully"));

    }
}
