<?php

namespace App\Http\Controllers;

use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
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
     * لیست نقش ها
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::paginate(10);

        return view('access.roles.index', ['roles' => $roles]);
    }

    /**
     * صفحه ایجاد نقش
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("access.roles.create");
    }

    /**
     * ذخیره سازی یک نقش
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('role.create')
                ->withErrors($validator)
                ->withInput();
        }

        /**************************************************************************************************************************
         * ایجاد نقش
         *************************************************************************************************************************/
        $role = new Role([
            'name' => $request->get('name'),
            'slug' => $request->get('slug'),

        ]);

        /**************************************************************************************************************************
         * مشکل در ثبت
         *************************************************************************************************************************/
        //if(!$role->save()) return redirect()->route('role.create')->withErrors([__("messages.save-failure")]);

        /**************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         *************************************************************************************************************************/
        return redirect()->route('roles.index')->withStatus(__("messages.save-successfully"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * صفحه ویرایش نقش
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::whereId($id)->first();

        return view("access.roles.edit", ['role' => $role]);
    }

    /**
     * بروزرسانی نقش
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::whereId($id)->first();

        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('role.edit',  $role->id)
                ->withErrors($validator)
                ->withInput();
        }

        /**************************************************************************************************************************
         * ویرایش نقش
         *************************************************************************************************************************/
        $role->name = $request->name;
        $role->slug = $request->slug;

        //if(!$role->save()){
            /**
             * مشکل در ثبت
             */
        //    return redirect()->route('role.create')->withErrors([__("messages.save-failure")]);
        //}

        /**************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         *************************************************************************************************************************/
        return back()->withStatus(__("messages.save-successfully"));

    }

    /**
     * حذف یک نقش
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        

        // return back()->with(['errors' => ['phone_number' => __('messages.WrongPhoneNumber',['phoneNumber'=>Numbers::toPersianNumbers($booking['phone_number'])])]]);
        //return redirect()->route('session.index', ['visit' => $request['visit']])->withStatus(['success',__('messages.SessionsAdded', ['number' => $i])]);
        $role = Role::whereId($request['id'])->first();
        /**************************************************************************************************************************
         * مشکل در حذف
         *************************************************************************************************************************/
        //if(!$role->delete()) return redirect()->route('roles.index')->withErrors([__("messages.delete-failure")]);
        
        /**************************************************************************************************************************
         * حذف با موفقیت انجام شده
         *************************************************************************************************************************/
        return back()->withStatus(__("messages.delete-successfully"));
    }
}
