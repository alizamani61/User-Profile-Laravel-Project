<?php

namespace App\Http\Controllers\Profile;

use App\User;
use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AccountInfoController extends \App\Http\Controllers\Controller
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
        return view('profile.accountinfo', ['user' => User::find(Auth::id())]);
    }
}
