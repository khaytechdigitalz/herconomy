<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Extension;
use App\Models\UserLogin;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;

use Illuminate\Http\Request;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    protected $username;

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }

    public function showLoginForm()
    {

        $pageTitle = "Sign In";
        return view(activeTemplate() . 'user.auth.login', compact('pageTitle'));
    }

    public function login(Request $request)
    {



        //Call HEKONOMY AUTH API//

        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => env('HECONOMY_BASE_URL').'rest-auth/login/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "email":"'.$request->username.'",
            "password": "'.$request->password.'"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
        ),
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        $reply = json_decode($resp,true);
      //  return $reply;
        if(!isset($reply['token']))
        {
            $notify[] = ['error', ' Error, we cant login with this login details'];
            return back()->withNotify($notify);
        }
        if($reply['user']['is_blocked'])
        {
            $notify[] = ['error', ' Error!! You Heconomy Account Appears Blocked'];
            return back()->withNotify($notify);
        }
        
        //CHECK DB USER\\
        $exist = User::whereEmail($request->username)->first();
        if(!$exist)
        {
        //User Create If Not Available 
        $user = new User();
        $user->firstname = isset($reply['user']['first_name']) ? $reply['user']['first_name'] : null;
        $user->lastname = isset($reply['user']['last_name']) ? $reply['user']['last_name'] : null;
        $user->email = strtolower(trim(@$reply['user']['email']));
        $user->password = Hash::make(@$request->password);
        $user->username = trim(@$reply['user']['username']);
        $user->country_code = @$reply['user']['country_code'];
        $user->mobile = @$reply['user']['phone_number'];
        $user->gender = @$reply['user']['gender'];
        $user->tier = @$reply['user']['tier'];
        $user->user_tag = @$reply['user']['user_tag'];
        $user->remember_token = @$reply['token'];
        $user->address = [
            'address' => '',
            'state' => @$reply['user']['location'],
            'zip' => '',
            'country' => null,
            'city' => ''
        ];
        $user->status = 1;
        $user->ev = 1;
        $user->sv = 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->save();
        }
        if($exist)
        {
        $exist->password = Hash::make(@$request->password);
        $exist->remember_token = @$reply['token'];
        $exist->gender = @$reply['user']['gender'];
        $exist->tier = @$reply['user']['tier'];
        $exist->user_tag = @$reply['user']['user_tag'];
        $exist->unique_id = @$reply['user']['id'];
        $exist->remember_token = @$reply['token'];
        
        $exist->address = [
            'address' => '',
            'state' => @$reply['user']['location'],
            'zip' => '',
            'country' => null,
            'city' => ''
        ];
        
        $exist->save();
        }
        
        $this->validateLogin($request);

        if(isset($request->captcha)){
            if(!captchaVerify($request->captcha, $request->captcha_secret)){
                $notify[] = ['error',"Invalid captcha"];
                return back()->withNotify($notify)->withInput();
            }
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);


        return $this->sendFailedLoginResponse($request);
    }

    public function findUsername()
    {
        $login = request()->input('username');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function validateLogin(Request $request)
    {
        $customRecaptcha = Extension::where('act', 'custom-captcha')->where('status', 1)->first();
        $validation_rule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        if ($customRecaptcha) {
            $validation_rule['captcha'] = 'required';
        }

        $request->validate($validation_rule);

    }

    public function logout()
    {
        $this->guard()->logout();

        request()->session()->invalidate();

        $notify[] = ['success', 'You have been logged out.'];
        return redirect()->route('user.login')->withNotify($notify);
    }

    public function authenticated(Request $request, $user)
    {
        if ($user->status == 0) {
            $this->guard()->logout();
            $notify[] = ['error','Your account has been deactivated.'];
            return redirect()->route('user.login')->withNotify($notify);
        }


        $user = auth()->user();
        $user->tv = $user->ts == 1 ? 0 : 1;
        $user->save();

        $ip = $_SERVER["REMOTE_ADDR"];
        $exist = UserLogin::where('user_ip',$ip)->first();
        $userLogin = new UserLogin();
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        }else{
            /*
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',',$info['long']) ?? null;
            $userLogin->latitude =  @implode(',',$info['lat']) ?? null;
            $userLogin->city =  @implode(',',$info['city']) ?? null;
            $userLogin->country_code = @implode(',',$info['code']) ?? null;
            $userLogin->country =  @implode(',', $info['country']) ?? null;
            */
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'] ?? null;
        $userLogin->os = @$userAgent['os_platform'] ?? null;
        $userLogin->save();

        //Check Cart
        Cart::insertUserToCart(auth()->user()->id, session('session_id'));

        return redirect()->route('user.home');
    }


}
