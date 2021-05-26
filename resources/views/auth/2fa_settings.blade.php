@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><strong>Two Factor Authentication</strong></div>
                    <div class="card-body">
                        <p>Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.</p>

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(!$data['user']->google2fa_secret)
                            <form class="form-horizontal" method="POST" action="{{ route('generate2faSecret') }}">
                                @csrf
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        Generate Secret Key to Enable 2FA
                                    </button>
                                </div>
                            </form>
                        @elseif(!$data['user']['2fa_enabled'])
                            1. Scan this QR code with your Google Authenticator App. Alternatively, you can use the code: <code>{{ $data['secret'] }}</code><br/>
                            <?php echo $data['google2fa_url'];?>
                            <br/><br/>
                            2. Enter the pin from Google Authenticator app:<br/><br/>
                            <form class="form-horizontal" method="POST" action="{{ route('enable2fa') }}">
                                @csrf
                                <div class="form-group{{ $errors->has('verify-code') ? ' has-error' : '' }}">
                                    <label for="secret" class="control-label">Authenticator Code</label>
                                    <input id="secret" type="password" class="form-control col-md-4" name="secret" required>
                                    @if ($errors->has('verify-code'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('verify-code') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    Enable 2FA
                                </button>
                            </form>
                        @elseif($data['user']['2fa_enabled'])
                            <div class="alert alert-success">
                                2FA is currently <strong>enabled</strong> on your account.
                            </div>
                            @if (session('is_google2fa_verified'))
                                <div class="form-group">
                                    <a class="btn btn-primary" href="{{ route('home') }}">
                                        Go To Main Page
                                    </a>
                                </div>
                            @endif
                            <p>If you are looking to disable Two Factor Authentication. Please confirm your password and Click Disable 2FA Button.</p>
                            
                            <form class="form-horizontal" method="POST" action="{{ route('disable2fa') }}">
                                @csrf
                                <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                                    <label for="change-password" class="control-label">Current Password</label>
                                        <input id="current-password" type="password" class="form-control col-md-4" name="current-password" required>
                                        @if ($errors->has('current-password'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('current-password') }}</strong>
                                        </span>
                                        @endif
                                </div>
                                <button type="submit" class="btn btn-primary ">Disable 2FA</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection