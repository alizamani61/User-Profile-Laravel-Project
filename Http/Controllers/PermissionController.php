<?php
namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
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
     * لیست مجوز ها
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::paginate(10);

        return view('access.permissions.index', ['permissions' => $permissions]);
    }
    
    
    /**
     * صفحه ایجاد مجوز
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("access.permissions.create", ['roles' => Role::all()]);
    }
    
    /**
     * ذخیره سازی یک مجوز
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
            'role_id' => 'required',
        ]);

        
        if ($validator->fails()) {
            return redirect()->route('permission.create')
                ->withErrors($validator)
                ->withInput();
        }
        
        /*************************************************************************************************************************
         * ایجاد مجوز
         ************************************************************************************************************************/
        $permission = new Permission([
            'name' => $request->get('name'),
            'slug' => $request->get('slug'),
        ]);

        //if(!$permission->save()){
            /**
             * مشکل در ثبت
             */
        //    return redirect()->route('permission.create')->withErrors([__("messages.save-failure")]);
        //}

        /*************************************************************************************************************************
         * نقش هایی که این مجوز را دارند
         * مشخص می کند
         ************************************************************************************************************************/
        $roles = Role::whereIn('id', $request->get("role_id"))->get();
        
        //$permission->roles()->attach($roles);
        
        /*************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         ************************************************************************************************************************/
        return redirect()->route('permissions.index')->withStatus(__("messages.save-successfully"));
    }
    
    
    /**
     * صفحه ویرایش مجوز
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::whereId($id)->first();

        return view("access.permissions.edit", ['permission' => $permission, 'roles' => Role::all()]);
    }

    /**
     * بروزرسانی مجوز
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::whereId($id)->first();

        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'slug' => 'required',
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('permission.edit',  $permission->id)
                ->withErrors($validator)
                ->withInput();
        }

        /**************************************************************************************************************************
         * ویرایش مجوز
         *************************************************************************************************************************/
        $permission->name = $request->name;
        $permission->slug = $request->slug;

        //if(!$permission->save()){
            /**
             * مشکل در ثبت
             */
        //    return redirect()->route('permission.edit',  $permission->id)->withErrors([__("messages.save-failure")]);
        //}

        /*************************************************************************************************************************
         * نقش هایی که این مجوز را دارند
         * مشخص می کند
         * برای ویرایش باید حذف و اضافه شوند
         ************************************************************************************************************************/
        $roles = Role::whereIn('id', $request->get("role_id"))->get();
        
        //$permission->roles()->detach();
        //$permission->roles()->attach($roles);
        
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
        
        $permission = Permission::whereId($request['id'])->first();
        
        
        //$permission->roles()->detach();
        
        //$permission->users()->detach();
        
        /**************************************************************************************************************************
         * مشکل در حذف
         *************************************************************************************************************************/
        //if(!$permission->delete()) return redirect()->route('roles.index')->withErrors([__("messages.delete-failure")]);
        
        /**************************************************************************************************************************
         * حذف با موفقیت انجام شده
         *************************************************************************************************************************/
        return back()->withStatus(__("messages.delete-successfully"));
    }
}
