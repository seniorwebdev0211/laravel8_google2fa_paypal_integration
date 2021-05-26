<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class Google2faController extends Controller
{
    //
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
     * Show 2FA Setting form
     */
    public function show2faForm(Request $request){
        $user = Auth::user();
        $qrCodeUrl = "";
        $secretKey = "";

        if($user->google2fa_secret){

            $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

            $qrCodeUrl = $google2fa->getQRCodeInline(
                config('app.name'),
                $user->email,
                $user->google2fa_secret
            );
            
        }

        $data = array(
            'user' => $user,
            'secret' => $user->google2fa_secret,
            'google2fa_url' => $qrCodeUrl
        );

        return view('auth.2fa_settings')->with('data', $data);
    }

    /**
     * Generate 2FA secret key
     */
    public function generate2faSecret(Request $request){
        $user = Auth::user();
        // Initialise the 2FA class
        $google2fa = new Google2FA();

        $user->google2fa_secret = $google2fa->generateSecretKey();
        $user->save();

        return redirect('/2fa')->with('success',"Secret key is generated.");
    }

    /**
     * Enable 2FA
     */
    public function enable2fa(Request $request){
        $user = Auth::user();
        $google2fa = new Google2FA();

        $secret = $request->input('secret');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $secret);

        if($valid){
            $user['2fa_enabled'] = 1;
            $user->save();
            session(['is_google2fa_verified' => true]);
            return redirect('/2fa')->with('success',"2FA is enabled successfully.");
        }else{
            return redirect('/2fa')->with('error',"Invalid verification Code, Please try again.");
        }
    }

    /**
     * Disable 2FA
     */
    public function disable2fa(Request $request){
        if (!(Hash::check($request->get('current-password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your password does not matches with your account password. Please try again.");
        }

        $validatedData = $request->validate([
            'current-password' => 'required',
        ]);
        $user = Auth::user();
        $user['2fa_enabled'] = 0;
        $user->save();
        return redirect('/2fa')->with('success',"2FA is now disabled.");
    }

    /**
     * Show Google 2FA Verify Form
     */
    public function show2faverify(Request $request) {
        return view('auth.2fa_verify');
    }

    /**
     * Verify Google 2FA
     */
    public function google2faverify(Request $request) {
        $google2fa = new Google2FA();
        $user = Auth::user();

        $secret = $request->input('secret');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $secret);

        if ($valid) {
            // Set Session is_google2fa_verified = true
            session(['is_google2fa_verified' => true]);
            // return redirect('/home');
            return redirect()->back()->with("success","You are successfully verified by Google 2FA.");
        }
        else {
            return redirect()->back()->with("error","one time password is incorrect. Please try again.");
        }
    }
}
