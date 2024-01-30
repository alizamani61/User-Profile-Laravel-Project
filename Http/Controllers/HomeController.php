<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //if ($request->user()->can('create-project')) {
            //Code goes here
        //}
        //auth()->user()->can('create-project')
        
        // $request->validate( [
        //     'file_number' => ['nullable', function ($attribute, $value, $fail) {
        //         if (empty(Customer::whereFileNumber($value)->first()))
        //             $fail(__('messages.WrongFileNumber'));
        //         return;
        //     },],
        //     'sex' => [function ($attribute, $value, $fail) use ($sex) {
        //         if ($value != $sex && $sex != null)
        //             $fail(__('messages.WrongGender'));
        //         return;
        //     }],
        //     'phone_number' => [function ($attribute, $value, $fail) use ($request) {
        //         if (empty($request->file_number ) && Customer::where('phone_number',$value)->count()){
        //             $fail(__('messages.WrongPhoneNumber'));
        //         }
        //         return;
        //     }],
        //     'round' => 'required|exists:reservations,id',
        //     'captcha' => auth()->check() ? '' : 'required|captcha' ,
        // ], [
        //     'captcha' => __('messages.wrongCaptcha')
        // ]);
        //return view('home');
        
        return redirect()->route('profile.index')
                ->withInput();

    }
}
