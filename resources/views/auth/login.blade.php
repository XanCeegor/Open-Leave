@extends('layouts.app')

@section('content')

<title>{{ env('APP_NAME') }}</title>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="height:100vh">
            <div class="col-lg-4" id="loginCard">
                <div class="card">
                    <div class="card-header text-white bg-primary">{{ env('APP_NAME') }}</div>
                    <div class="card-body">
                        <form method="POST" autocomplete="off" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group">
                                <label for="username">{{ __('Username') }}</label>
                                <input id="username" type="username"
                                    class="form-control {{ $errors->has('username') ? ' is-invalid' : '' }}"
                                    name="username" value="{{ old('username') }}" required autofocus>
                                @if ($errors->has('username'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="password">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="current-password">
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div><br>
                            <button type="submit" name="submit" id="sendlogin" class="btn btn-primary">Login</button>
                            @if(session('error'))
                            <br><br>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert">&times;</span>
                                </button> {{ session('error') }}
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
                @if($changes)
                <div class="text-center" style="margin-top: 1rem">
                    <small>Last updated on: {{ date('d F Y', strtotime($changes->date)) }}. <a
                            href="{{ route('changelogs.view') }}">What's new?</a></small>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
<html>


<script>
    //script for logging in button animation
        $("div#loginCard").removeClass("hidden");
        var i = getRandomInt(0,23);

        //intro animations
        var introAnimations = [];
        introAnimations.push(function () { $('#loginCard').addClass('animated lightSpeedIn delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated fadeInUpBig delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated fadeInDownBig delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated fadeInLeftBig delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated fadeInRightBig delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated flip delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated flipInX delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated flipInY delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated bounceIn delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated bounceInRight delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated bounceInLeft delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated bounceInUp delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated bounceInDown delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated rotateIn delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated rotateInDownLeft delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated rotateInDownRight delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated rotateInUpRight delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated rotateInUpLeft delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated zoomIn delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated zoomInDown delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated zoomInUp delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated zoomInLeft delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated zoomInRight delay-1s'); });
        introAnimations.push(function () { $('#loginCard').addClass('animated rollIn delay-1s'); });
        introAnimations[i]();

        //outro animations
        var outroAnimations = [];
        outroAnimations.push(function () { $('#loginCard').addClass('animated bounceOut '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated bounceOutUp '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated bounceOutDown '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated bounceOutLeft '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated bounceOutRight '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated fadeOutRight '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated fadeOutDownBig '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated fadeOutUpBig '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated fadeOutLeftBig '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated fadeOutRightBig '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated lightSpeedOut '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated rotateOutDownLeft '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated rotateOutDownRight '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated rotateOutUpLeft '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated rotateOutUpRight '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated slideOutUp '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated slideOutDown '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated slideOutLeft '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated slideOutRight '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated zoomOut '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated zoomOutDown '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated zoomOutLeft '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated zoomOutUp '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated zoomOutRight '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated rollOut '); });
        outroAnimations.push(function () { $('#loginCard').addClass('animated hinge '); });

        $('.btn').on('click', function() {
            var j = getRandomInt(0,25);
            outroAnimations[j]();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Logging in...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
        });


    function getRandomInt(min, max) {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
</script>
@endsection
