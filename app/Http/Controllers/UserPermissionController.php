<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Permission;
use Illuminate\Support\Facades\Validator;

class UserPermissionController extends Controller
{
    /**
     * سازنده کلاس
     *
     * @return void
     */
    public function __construct() {
        /*         * ************************************************************************************************************************
         * کاربر باید لاگین کرده باشد
         * *********************************************************************************************************************** */
        $this->middleware('auth');
    }  
   
    /**
     * صفحه اختصاص مجوزها
     * @param type $userid
     * @return type
     */
    public function assign($userid) {
        $user = User::withTrashed()->whereId($userid)->first();

        return view("access.userpermission.assign", ["user" => $user, 'permissions' => Permission::all()]);
    }
    
    /**
     * ذخیره سازی نقش های یک کاربر
     * @param Request $request
     * @return type
     */
    public function store(Request $request)
    {
        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'permission_id' => 'required',
        ]);

        $user = User::withTrashed()->whereId($request->get("user_id"))->first();
        
        if ($validator->fails()) {
            return redirect()->route('userpermission.assign', $user->id)
                ->withErrors($validator)
                ->withInput();
        }

        /*************************************************************************************************************************
         * تمام مجوزهای انتخاب شده توسط کاربر
         * را می گیرد
         * سپس مجوزهای جاری کاربر را حذف کرده
         * و دوباره ست می کند
         ************************************************************************************************************************/
        $permissions = Permission::whereIn('id', $request->get("permission_id"))->get();
        
        
        //$user->permissions()->detach(); //حذف مجوزهای کاربر
        //$user->permissions()->attach($permissions);
        
        
        /**************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         *************************************************************************************************************************/
        return redirect()->route('userpermission.assign', $user->id)->withStatus(__("messages.save-successfully"));
    }
}
