<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\UserLogin;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
        $this->username = $this->findUsername();
    }

    public function loginHeconomy(Request $request)
    {

        $validator = $this->validateLogin($request);
        
        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }
        
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
        return $reply;
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
        $user->remember_token = @$reply['token'];
        $user->address = [
            'address' => '',
            'state' => '',
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
        $exist->save();
        }
        
        $credentials = request([$this->username, 'password']);
        
        //return $request->password;
        if(!Auth::attempt($credentials)){
            //$response[] = 'Unauthorized user';
            $notify[] = ['error', ' Error!! Unauthorized user'];
            return back()->withNotify($notify);
        }


        $user = $request->user();
        $tokenResult = $user->createToken('auth_token')->plainTextToken;
        $this->authenticated($request,$user);
        $response[] = 'Login Successful';
      //  return Auth::user();
        $notify[] = ['success', 'Login Successful'];
        return redirect()->route('user.home')->withNotify($notify); 
    }

    public function login(Request $request)
    {

        $validator = $this->validateLogin($request);

        if ($validator->fails()) {
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['error'=>$validator->errors()->all()],
            ]);
        }

        $credentials = request([$this->username, 'password']);
        if(!Auth::attempt($credentials)){
            $response[] = 'Unauthorized user';
            return response()->json([
                'code'=>401,
                'status'=>'unauthorized',
                'message'=>['error'=>$response],
            ]);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('auth_token')->plainTextToken;
        $this->authenticated($request,$user);
        $response[] = 'Login Succesfull';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$response],
            'data'=>[
                'user' => auth()->user(),
                'access_token'=>$tokenResult,
                'token_type'=>'Bearer'
            ]
        ]);

        
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
        $validation_rule = [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ];

        $validate = Validator::make($request->all(),$validation_rule);
        return $validate;

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        $notify[] = 'Logout Succesfull';
        return response()->json([
            'code'=>200,
            'status'=>'ok',
            'message'=>['success'=>$notify],
        ]);
    }


    public function authenticated(Request $request, $user)
    {
        if ($user->status == 0) {
            $this->guard()->logout();
            return redirect()->route('user.login')->withErrors(['Your account is not activated.']);
        }


        $user = auth()->user();
        $user->tv = $user->ts == 1 ? 0 : 1;
        $user->save();

          $baseUrl = "http://www.geoplugin.net/";
			$endpoint = "json.gp?ip=" . request()->ip()."";
			$httpVerb = "GET";
			$contentType = "application/json"; //e.g charset=utf-8
			$headers = array (
				"Content-Type: $contentType",

        );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_URL, $baseUrl.$endpoint);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $content = json_decode(curl_exec( $ch ),true);
            $err     = curl_errno( $ch );
            $errmsg  = curl_error( $ch );
        	curl_close($ch);


             $conti = $content['geoplugin_continentName'] ?? 0;
             $country = $content['geoplugin_countryName'] ?? 0;
             $city = $content['geoplugin_city'] ?? 0;
            $area = $content['geoplugin_areaCode'] ?? 0;
           $code = $content['geoplugin_countryCode'] ?? 0;
            $long = $content['geoplugin_longitude'] ?? 0;
             $lat = $content['geoplugin_latitude'] ?? 0;

        $info = json_decode(json_encode(getIpInfo()), true);
        $ul['user_id'] = $user->id;
        $ul['user_ip'] =  request()->ip();
        $ul['long'] =  $long;
        $ul['lat'] =  $lat;
        $ul['location'] =  $city . $area . $country . $code;
        $ul['country_code'] = $code;
        $ul['browser'] = $info['browser'] ?? 0;
        $ul['os'] = $info['os_platform'] ?? 0;
        $ul['country'] =  $country;
        UserLogin::create($ul);
        
        //return $user->account_type;

        if($user->account_type == 1)
        {
        return redirect()->route('user.schoolhome');

        }
         elseif($user->account_type == 2)
        {
        return redirect()->route('user.tertiaryhome');

        }
        elseif($user->account_type == 0)
        {
        return redirect()->route('user.home');
        }
    }
    
    
    public function authenticatedless(Request $request, $user)
    {
        if ($user->status == 0) {
            auth()->user()->tokens()->delete();
            $notify[] = 'Your account has been deactivated';
            return response()->json([
                'code'=>200,
                'status'=>'ok',
                'message'=>['success'=>$notify],
            ]);
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
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',',$info['long']);
            $userLogin->latitude =  @implode(',',$info['lat']);
            $userLogin->city =  @implode(',',$info['city']);
            $userLogin->country_code = @implode(',',$info['code']);
            $userLogin->country =  @implode(',', $info['country']);
        }

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;
        
        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
    }


}
