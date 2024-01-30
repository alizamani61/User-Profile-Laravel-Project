<?php
namespace App\Helper;

use Illuminate\Support\Facades\Auth;
use App\Notification;
use Morilog\Jalali\Jalalian;

class Helper
{
    public static function addNotification($to_user, $title, $url){
        $notification = new Notification([
            'from_user' => Auth::id(),
            'to_user' => $to_user,
            'note_title' => $title,
            'note_text' => '',
            'create_date' => Jalalian::forge('now')->format('Y/m/d'),
            'create_time' => Jalalian::forge('now')->format('H:i'),
            'target_url' => $url,
        ]);
        
        $notification->save();
    }
}