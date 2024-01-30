<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class AccessController extends Controller
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
     * لیست دسترسی ها
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::withTrashed()->paginate(10);

        return view('access.index', ['users' => $users]);
    }
    
    /**
     * اطلاعات یک کاربر را نشان دهد
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view("access.show"); 
    }

    /**
     * حذف یک کاربر
     * کاربر فقط غیر فعال می شود
     * درنتیجه نیاز به حذف نقش ها و مجوزهای کاربر نمی باشد
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        
        $user = User::withTrashed()->whereId($request['id'])->first();
       
        /**************************************************************************************************************************
         * مشکل در حذف
         *************************************************************************************************************************/
        //if(!$user->delete()) return redirect()->route('access.index')->withErrors([__("messages.delete-failure")]);
        
        /**************************************************************************************************************************
         * حذف با موفقیت انجام شده
         *************************************************************************************************************************/
        return back()->withStatus(__("messages.delete-successfully"));
    }
    
    /**
     * بازیابی کاربر
     * @param Request $request
     * @return type
     */
    public function restore(Request $request)
    {
        
        $user = User::withTrashed()->whereId($request['id'])->first(); 
       
        /**************************************************************************************************************************
         * مشکل در بازیابی
         *************************************************************************************************************************/
        //if(!$user->restore()) return redirect()->route('access.index')->withErrors([__("messages.restore-failure")]);
        
        /**************************************************************************************************************************
         * بازیابی با موفقیت انجام شده
         *************************************************************************************************************************/
        return back()->withStatus(__("messages.restore-successfully"));
    }
}
