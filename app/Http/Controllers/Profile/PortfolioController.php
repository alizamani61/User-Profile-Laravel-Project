<?php

namespace App\Http\Controllers\Profile;

use App\User;
use App\Portfolio;
use App\BaseSkill;
use App\Helper\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;

class PortfolioController extends \App\Http\Controllers\Controller
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
     * نمایش صفحه اصلی نمونه کارها
     * جدولی از تمام نمونه کارهای تعریف شده توسط
     * کاربر
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::find(Auth::id());
        
        return view('profile.portfolio.index', ['user' => $user, 'portfolios' => Portfolio::withTrashed()->where('user_id', Auth::id())->paginate(10)]);
    }
    
    /**
     * صفحه ایجاد نمونه کار
     * @return type
     */
    public function create()
    {
        return view("profile.portfolio.create", ['portfolio' => new Portfolio(), 'baseskills' => BaseSkill::all() ]);
    }
    
    
    /**
     * ذخیره سازی یک نمونه کار
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*************************************************************************************************************************
         * کاربر جاری
         * ********************************************************************************************************************** */
        $user = User::find(Auth::id());
        
        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.portfolio.create')
                ->withErrors($validator)
                ->withInput();
        }

        /*************************************************************************************************************************
         * آپلود تصویر نمونه کار کاربر
         * ********************************************************************************************************************** */
        $image_filename = "";

        if ($request->hasFile('image_url')) {

            /**
             * در این قسمت اگر فایل نمونه کار آپلود شده باشد
             * و مجاز باشد ذخیره شده و نام فایل درون
             * متغیر قرار می گیرد
             */
            if (!Validator::make($request->all(), ['image_url' => 'mimes:png,jpg'])->fails()) {

                $a = $request->file('image_url');

                //$image_filename = $file->getClientOriginalName();
                $image_filename = 'av_' . Auth::id() . '_' . now()->timestamp . '.' . $a->getClientOriginalExtension();

                //$a->move(public_path('images'), $image_filename);
            }
        }
        
        /**************************************************************************************************************************
         * ایجاد نمونه کار
         *************************************************************************************************************************/
        $portfolio = new Portfolio([
            'title' => $request->get('title'),
            'user_id' => Auth::id(),
            'portfolio_desc' => $request->get('portfolio_desc'),
            'skills' => json_encode($request->get('skills')),
            'image_url' => $image_filename,
            'status' => 1,
        ]);

        /**************************************************************************************************************************
         * مشکل در ثبت
         *************************************************************************************************************************/
        //if(!$portfolio->save()) return redirect()->route('profile.portfolio.create')->withErrors([__("messages.save-failure")]);

        //ثبت اعلان
        //Helper::addNotification(1, __('messages.user-change-profile',['username' => $user->username, "section" => __('sections.portfolios')]), route('view.profile.portfolio',['username' => $user->username]));
        
        
        /**************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         *************************************************************************************************************************/
        return redirect()->route('profile.portfolio.index')->withStatus(__("messages.save-successfully"));
    }
    
    
    /**
     * صفحه ویرایش نمونه کار
     * @return type
     */
    public function edit($id)
    {
        return view("profile.portfolio.edit", ['portfolio' => Portfolio::whereId($id)->first(), 'baseskills' => BaseSkill::all() ]);
    }
    
    /**
     * بروزرسانی یک نمونه کار
     * @param Request $request
     * @param Portfolio $portfolio
     * @return type
     */
    public function update(Request $request)
    {
        /*************************************************************************************************************************
         * کاربر جاری
         * ********************************************************************************************************************** */
        $user = User::find(Auth::id());
        
        /**************************************************************************************************************************
         * اعتبارسنجی ورودی های کاربر
         *************************************************************************************************************************/
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.portfolio.edit', ['portfolio' => $portfolio])
                ->withErrors($validator)
                ->withInput();
        }

        /*************************************************************************************************************************
         * آپلود تصویر نمونه کار کاربر
         * ********************************************************************************************************************** */
        $image_filename = "";

        if ($request->hasFile('image_url')) {
            /**
             * در این قسمت اگر فایل نمونه کار آپلود شده باشد
             * و مجاز باشد ذخیره شده و نام فایل درون
             * متغیر قرار می گیرد
             */
            if (!Validator::make($request->all(), ['image_url' => 'mimes:png,jpg'])->fails()) {

                $a = $request->file('image_url');

                //$image_filename = $file->getClientOriginalName();
                $image_filename = 'av_' . Auth::id() . '_' . now()->timestamp . '.' . $a->getClientOriginalExtension();

                //$a->move(public_path('images'), $image_filename);
            }
        }
        
        /**************************************************************************************************************************
         * بروزرسانی نمونه کار
         *************************************************************************************************************************/
        $portfolio = Portfolio::whereId($request->get('id'))->first();
        
        $portfolio->title = $request->get('title');
        $portfolio->user_id = Auth::id();
        $portfolio->portfolio_desc = $request->get('portfolio_desc');
        $portfolio->skills = json_encode($request->get('skills'));
        $portfolio->image_url = $image_filename;
        $portfolio->status = 1;
        

        /**************************************************************************************************************************
         * مشکل در ثبت
         *************************************************************************************************************************/
        //if(!$portfolio->save()) return redirect()->route('profile.portfolio.edit', ['portfolio' => $portfolio])->withErrors([__("messages.save-failure")]);

        /**************************************************************************************************************************
         * ثبت با موفقیت انجام شده
         *************************************************************************************************************************/
        //ثبت اعلان
        //Helper::addNotification(1, __('messages.user-change-profile',['username' => $user->username, "section" => __('sections.portfolios')]), route('view.profile.portfolio',['username' => $user->username]));
        
        
        return redirect()->route('profile.portfolio.index')->withStatus(__("messages.save-successfully"));
    }
}
