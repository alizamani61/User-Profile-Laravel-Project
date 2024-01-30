<?php

namespace App\Http\Controllers\Base;

use App\BaseSkill;
use App\User;
use App\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BaseSkillsController extends \App\Http\Controllers\Controller
{

    /**
     * سازنده کلاس
     *
     * @return void
     */
    public function __construct()
    {
        /**
         * کاربر باید لاگین کرده باشد
         */
        $this->middleware('auth');
    }
    
    /**
     * لیست مهارت ها
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $skills = BaseSkill::paginate(10);

        return view('base.skills.index', ['skills' => $skills]);
    }
    
    
    /**
     * صفحه ایجاد مهارت جدید
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("base.skills.create");
    }
    
    /**
     * ذخیره سازی یک مهارت
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        $validator = Validator::make($request->all(), ['name' => 'required', 'group' => 'required',]);

        
        if ($validator->fails())
            return redirect()->route('baseskills.create')->withErrors($validator)->withInput();
        
        /*************************************************************************************************************************
         * ایجاد مهارت
         ************************************************************************************************************************/
        $baseSk = new BaseSkill(['name' => $request->get('name'), 'group' => $request->get('group'),]);

        if(!$baseSk->save()){
            /**
             * مشکل در ثبت
             */
            return redirect()->route('baseskills.create')->withErrors([__("messages.save-failure")]);
        }

        
        /*************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         ************************************************************************************************************************/
        return redirect()->route('baseskills.index')->withStatus(__("messages.save-successfully"));
    }
    
    
    /**
     * صفحه ویرایش مهارت
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $baseSkill = BaseSkill::whereId($id)->first();

        return view("base.skills.edit", ['baseSkill' => $baseSkill]);
    }

    /**
     * بروزرسانی مهارت
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $baseSkill = BaseSkill::whereId($id)->first();

        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        $validator = Validator::make($request->all(), ['name' => 'required', 'group' => 'required',]);

        if ($validator->fails())
            return redirect()->route('baseskills.edit',  $baseSkill->id)->withErrors($validator)->withInput();

        /**************************************************************************************************************************
         * ویرایش مهارت
         *************************************************************************************************************************/
        $baseSkill->name = $request->name;
        $baseSkill->group = $request->group;

        if(!$baseSkill->save()){
            /**
             * مشکل در ثبت
             */
            return redirect()->route('baseskills.edit',  $baseSkill->id)->withErrors([__("messages.save-failure")]);
        }

        
        /**************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         *************************************************************************************************************************/
        return back()->withStatus(__("messages.save-successfully"));

    }
    
    /**
     * حذف یک مجوز
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        
        $baseSkill = BaseSkill::whereId($request['id'])->first();
        
        /**************************************************************************************************************************
         * مشکل در حذف
         *************************************************************************************************************************/
        if(!$baseSkill->delete()) return redirect()->route('baseskills.index')->withErrors([__("messages.delete-failure")]);
        
        /**************************************************************************************************************************
         * حذف با موفقیت انجام شده
         *************************************************************************************************************************/
        return back()->withStatus(__("messages.delete-successfully"));
    }
}


