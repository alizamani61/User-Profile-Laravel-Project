<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Notification;
use App\City;
use App\Province;
use App\Resume;
use App\Skill;
use App\BaseSkill;
use App\Portfolio;
use App\UsersLike;
use App\UsersView;
use App\Helper\Helper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class ViewProfileController extends Controller
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
     * لیست کاربران وفریلنسرها
     * @param Request $request
     * @return type
     */
    public function Users(Request $request){
        
        //پارامتر استان در جستجو
        $province = $request->get('province_id');
        //پارامتر شهر در جستجو
        $city = empty($request->get('city_id'))?null:City::whereId($request->get('city_id'))->first();
        //پارامتر مهارت در جستجو
        $skills = $request->get('skills');
        
        $users = User::where(function($query) use($province, $city, $skills){
            if (!empty($province)) {
                //فیلتر استان
                $query->where('province_id', $province);
            }
            
            if (!empty($city)) {
                //فیلتر شهر
                $query->where('city_id', $city);
            }
            
            if (!empty($skills)) {
                //فیلتر مهارت
                $query->whereIn('id', Skill::whereIn('base_skill_id', $skills)->pluck('user_id'));
            }
            
        })->paginate(15);
        
        return view('view.users', [
                'users' => $users,
                'city' => $city, 
                'province' => $province, 
                'skills' => $skills,
                'base_province' => Province::all(),
                'base_skills' => BaseSkill::all()
            ]
        );
    }
    
    /**
     * لایک کردن
     * @param type $username کاربری که لایک می شود
     * @return type
     */
    public function Like($username)
    {
        $user = User::where('username', '=', $username)->first();
        
        if(!$user)
            return;
        
        /**
         * کاربر نتواند خودش را لایک کند
         */
        if(Auth::id() == $user->id)
            return;
        
        $checkLike = UsersLike::where(['user_id' => Auth::id(), 'like_user_id' => $user->id])->first();
        
        if($checkLike == null)
        {
            /**
             * اگر لایک نشده بود
             */
            $like = new UsersLike([
                'user_id' => Auth::id(),
                'like_user_id' => $user->id,
            ]);

            $like->save();
            
            return response()->json(['like' => $like]);
        }
        
        //اگر قبلا لایک شده باشد
        UsersLike::where(['user_id' => Auth::id(), 'like_user_id' => $user->id])->delete();
        
        return response()->json(['like'=>['user_id'=>0]]);
    }
    
    /**
     * ثبت بازدید درصورتی که قبلا بازدید
     * نشده باشد
     * @param type $user
     * @return type
     */
    private function SetView($user){
        
        if(Auth::id() == $user->id)
            return;
        
        $checkView = UsersView::where(['user_id' => Auth::id(), 'view_user_id' => $user->id])->first();
        
        if($checkView == null)
        {
            /**
             * اگر بازدید نشده بود
             */
            $view = new UsersView([
                'user_id' => Auth::id(),
                'view_user_id' => $user->id,
            ]);

            $view->save();
        }
    }
    
    /**
     * مشاهده اطلاعات شخصی پروفایل
     * @param type $username
     * @return type
     */
    public function Profile($username)
    {
        $user = User::where('username', '=', $username)->first();
        
        if(!$user)
            abort(404);
        
        //لایک شده؟
        $isLiked = UsersLike::where(['user_id' => Auth::id(), 'like_user_id' => $user->id])->first()?true:false;
        
        //ثبت مشاهده
        $this->SetView($user);
        
        return view('view.profile', ['user' => $user, 'isLiked' => $isLiked, 'province' => Province::whereId($user->province_id)->first(), 'city' => City::where('id', $user->city_id)->first()]);
    }
    
    /**
     * تایید تغییرات پروفایل توسط ادمین
     * @param type $username
     * @return type
     */
    public function AcceptProfile($username){
        $user = User::where('username', '=', $username)->first();
        
        if(!$user)
            abort(404);
        
        $user->accepted_firstname = $user->firstname;
        $user->accepted_lastname = $user->lastname;
        $user->accepted_avatar = $user->avatar;
        $user->accepted_image_url = $user->image_url;
        $user->accepted_at = Carbon::now();
        $user->status = 0;
        
        
        //if($user->save())
            //ثبت اعلان
        //    Helper::addNotification($user->id, __('messages.admin-accept-profile',['username' => $user->username, "section" => __('sections.personal-information')]), route('view.profile',['username' => "admin"]));
        
        
        
        return redirect()->route('view.profile', $username);
    }
    
    
    /**
     * 
     * @param type $username
     * @return type
     */
    public function Resume($username){
        $user = User::where('username', '=', $username)->first();
        
        if(!$user)
            abort(404);
        
        //لایک شده؟
        $isLiked = UsersLike::where(['user_id' => Auth::id(), 'like_user_id' => $user->id])->first()?true:false;
        
        //ثبت مشاهده
        $this->SetView($user);
        
        return view('view.resume', ['user'=>$user, 'isLiked' => $isLiked, 'resume' => Resume::firstOrNew(['user_id' => $user->id]) ]);
    }
    
    /**
     * 
     * @param type $username
     * @return type
     */
    public function AcceptResume($username){
        $user = User::where('username', '=', $username)->first();
        
        if(!$user)
            abort(404);
        
        $resume = Resume::where('user_id', $user->id)->first();
        
        $resume->accepted_title = $resume->title;
        $resume->accepted_work_records = $resume->work_records;
        $resume->accepted_education_records = $resume->education_records;
        $resume->status = 0;
        $resume->accepted_at = Carbon::now();
        
        //if($resume->save())
            //ثبت اعلان
        //    Helper::addNotification($user->id, __('messages.admin-accept-profile',['username' => $user->username, "section" => __('sections.resume')]), route('view.profile.resume',['username' => "admin"]));
        
        
        return redirect()->route('view.profile.resume', $username);
    }
    
    /**
     * 
     * @param type $username
     * @return type
     */
    public function Skills($username){
        $user = User::where('username', '=', $username)->first();
        
        if(!$user)
            abort(404);
        
        //لایک شده؟
        $isLiked = UsersLike::where(['user_id' => Auth::id(), 'like_user_id' => $user->id])->first()?true:false;
        
        //ثبت مشاهده
        $this->SetView($user);
        
        return view('view.skills', ['user'=>$user, 'isLiked' => $isLiked, 'skills' => BaseSkill::all()]);
    }
    
    /**
     * 
     * @param type $username
     * @return type
     */
    public function Portfolio($username){
        $user = User::where('username', '=', $username)->first();
        
        if(!$user)
            abort(404);
        
        //لایک شده؟
        $isLiked = UsersLike::where(['user_id' => Auth::id(), 'like_user_id' => $user->id])->first()?true:false;
        
        //ثبت مشاهده
        $this->SetView($user);
        
        return view('view.portfolio', ['user'=>$user, 'isLiked' => $isLiked, 'portfolios' => Portfolio::withTrashed()->where('user_id', $user->id)->paginate(10)]);
    }
    
    
    public function AcceptPortfolio($username, $id){
        $user = User::where('username', '=', $username)->first();
        
        echo $id;
        
        if(!$user)
            abort(404);
        
        $portfolio = Portfolio::where(['user_id' => $user->id, 'id' => $id])->first();
        
        //dd($resume);
        $portfolio->accepted_title = $portfolio->title;
        $portfolio->accepted_skills = $portfolio->skills;
        $portfolio->accepted_image_url = $portfolio->image_url;
        $portfolio->accepted_portfolio_desc = $portfolio->portfolio_desc;
        $portfolio->status = 0;
        $portfolio->accepted_at = Carbon::now();
        
        //if($portfolio->save())
            //ثبت اعلان
        //    Helper::addNotification($user->id, __('messages.admin-accept-profile',['username' => $user->username, "section" => __('sections.portfolios')]), route('view.profile.portfolio',['username' => "admin"]));
        
        return redirect()->route('view.profile.portfolio', $username);
    }
}