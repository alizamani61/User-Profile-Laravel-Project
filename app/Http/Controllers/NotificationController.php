<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;

class NotificationController extends Controller
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
     * لیست اطلاعیه ها
     * ده تا از جدید ترین اعلان ها را فراخوانی می کند
     * و سپس با تغییر فرمت تاریخ به صورت اجکس به فرانت ارسال می شود
     * 
     * @return json
     */
    public function Get()
    {
        //ده تا از جدید ترین پیام ها را فراخوانی می کند
        $notes = DB::table("notifications")
                        ->where('notifications.to_user', '=', Auth::id())
                        ->select('notifications.note_title','notifications.create_date','notifications.create_time','notifications.target_url','users.username', 'users.avatar')
                        ->join('users', 'notifications.from_user', '=', 'users.id')
                        ->take(10)
                        ->orderBy('create_date', 'DESC')
                        ->orderBy('create_time', 'DESC')
                        ->get();
        
        
        foreach($notes as $note){
            //تبدیل فرمت تاریخ خروجی به
            //جمعه, 11 آذر 1401 18:16
            $note->create_date = Jalalian::fromCarbon(Jalalian::fromFormat('Y/m/d', $note->create_date)->toCarbon())->format('%A, %d %B %Y');
        }
        
        //تعداد خوانده نشده ها
        $unread = Notification::where('notifications.to_user', '=', Auth::id())->whereNull('view_date')->count();
        
        return response()->json(['notes'=>$notes,'count'=> $unread]);
    }
    
    
    public function Set(){
        Notification::where('to_user', '=', Auth::id())
                    ->whereNull('view_date')
                    ->update(['view_date' => Jalalian::forge('now')->format('Y/m/d'), 'view_time' => Jalalian::forge('now')->format('H:i')]);
                    //->update(['view_date' => null, 'view_time' => null]);
        
    }
}
