<?php

namespace App\Http\Controllers\Profile;

use App\User;
use App\Role;
use App\Permission;
use App\Skill;
use App\BaseSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SkillsController extends \App\Http\Controllers\Controller
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
     * نمایش صفحه اصلی مهارتهای کاربر
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('profile.skills', ['user' => User::find(Auth::id()), 'skills' => BaseSkill::all()]);
    }
    
    /**
     * بروزرسانی مهارتها
     * @param Request $request
     * @param Resume $resume
     * @return type
     */
    public function update(Request $request)
    {
        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        
        $validator = Validator::make($request->all(), [
            'base_skill_id' => 'required',
        ]);
        
        if ($validator->fails())
            return redirect()->route('profile.skills.index')->withErrors($validator)->withInput();
        
        $user = User::whereId(Auth::id())->first();
        
        $skills = BaseSkill::whereIn('id', $request->get("base_skill_id"))->get();
        
        //$user->skills()->detach(); //حذف مهارت های کاربر
        //$user->skills()->attach($skills);
        
        
        /**************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         *************************************************************************************************************************/
        return back()->withStatus(__("messages.save-successfully"));

    }
}
