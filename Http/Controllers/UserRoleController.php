<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Permission;

use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller {

    /**
     * سازنده کلاس
     *
     * @return void
     */
    public function __construct() {
        /**************************************************************************************************************************
         * کاربر باید لاگین کرده باشد
         *************************************************************************************************************************/
        $this->middleware('auth');
    } 

    /**
     * صفحه اختصاص نقش
     * @param type $userid
     * @return type
     */
    public function assign($userid) {
        $user = User::withTrashed()->whereId($userid)->first();

        return view("access.userrole.assign", ["user" => $user, 'roles' => Role::all()]);
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
            'role_id' => 'required',
        ]);

        $user = User::withTrashed()->whereId($request->get("user_id"))->first();
        
        if ($validator->fails()) {
            return redirect()->route('userrole.assign', $user->id)
                ->withErrors($validator)
                ->withInput();
        }

        /*************************************************************************************************************************
         * تمام نقش های انتخاب شده توسط کاربر
         * را می گیرد
         * سپس نقش های جاری کاربر را حذف کرده
         * و دوباره ست می کند
         * تمام مجوزهای کاربر را حذف می کند
         * و بر اساس نقش های انتخاب شده دوباره ست می کند
         ************************************************************************************************************************/
        $roles = Role::whereIn('id', $request->get("role_id"))->get();
        
        
        //$user->roles()->detach(); //حذف نقش های کاربر
        //$user->roles()->attach($roles);
        
        //$user->permissions()->detach(); //حذف مجوزهای کاربر
        
        foreach ($roles as $role) {
            //چون برخی از نقش ها مجوزهای اشتراکی دارند
            //اگر مجوز همراه نقش دیگری به کاربر اختصاص داده شده باشد
            //دیگر آنرا بروز نمی کند
            $permissions = $role->permissions()->get();
            
            //foreach ($permissions as $p)
                //if(!$user->permissions()->whereId($p->id)->first()) $user->permissions()->attach($p);
            
        }
        
        /**************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         *************************************************************************************************************************/
        return redirect()->route('userrole.assign', $user->id)->withStatus(__("messages.save-successfully"));
    }

} 
