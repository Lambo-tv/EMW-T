<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="UTF-8" />
    <title>{{ __('auth.login') }} - {{ config('other.title') }}</title>
    @section('meta')
        <meta name="description"
            content="{{ __('auth.login-now-on') }} {{ config('other.title') }} . {{ __('auth.not-a-member') }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta property="og:title" content="{{ __('auth.login') }}" />
        <meta property="og:site_name" content="{{ config('other.title') }}" />
        <meta property="og:type" content="website" />
        <meta property="og:image" content="{{ url('/img/og.png') }}" />
        <meta property="og:description" content="{{ config('unit3d.powered-by') }}" />
        <meta property="og:url" content="{{ url('/') }}" />
        <meta property="og:locale" content="{{ config('app.locale') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        @show
        <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        @vite('resources/sass/pages/_auth.scss')
</head>
<body><!-- Se agrega CSS para centrar nombre de ususario y contraseña en caja de texto -->
    <style>
        #username {
            text-align: center;
        }
    </style>

    <style>
        #password {
            text-align: center;
        }
    </style>
    <style>
body {
    min-height: auto;
    overflow-y: auto;
    background: linear-gradient(185deg, #171718, #464e50, #171718 );
    font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", Segoe UI Symbol;
}


.auth-form__text-input, .auth-form__select {
    border-radius: 9px;
    height: 55px;
    width: 350px;
    padding: 6px 15px;
    margin: 3px 0;
    border: dotted;
    /* box-sizing: border-box; */
    background-color: var(--White);
    outline: none;
    transition: outline-color .5s;
    font-weight: 400;
    color: #11001c;
    background: #d2e4eb;
}        

.auth-form__primary-button {
    height: 48px;
    border-radius: 7px;
    background-color: #3bb6db;
    border: solid;
    border-color: #34383e;
    width: 120px;
    color: #163845;
    font-weight: 800;
    font-size: 16px;
    text-transform: uppercase;
    transition: background-color .2s;
    margin-top: 24px;
}


.auth-form__primary-button:hover {
    background-color: #81c7e3;
    cursor: pointer
}
.auth-form__label {
    margin-left: 14px;
    font-size: 16px;
    color: #89bbc0;
}


.auth-form__footer-item {
    margin-top: 5px;
    text-decoration: none;
    font-size: 15px;
    transition: background-color .25s;
    color: #a0cecc;
    background-color: transparent;
    cursor: pointer;
}
</style>



    <!-- Do NOT Change! For Jackett Support -->
    <div class="Jackett" style="display: none">{{ config('unit3d.powered-by') }}</div>
    <!-- Do NOT Change! For Jackett Support -->
    <main>
        <section class="auth-form">
            <form class="auth-form__form" method="POST" action="{{ route('login') }}">
                @csrf
                <a class="auth-form__branding" href="{{ route('home.index') }}">
                    <i class="fal fa-tv-retro"></i>
                    <!-- <img class="auth-form__site-logo-lateam" src="{{ url('/img/logo15.png') }}" alt="eMuwarez Comunidad"/> -->
               
                <img src="/img/logo15.png" alt="eMuwarez" class="footer__icon" style="width: 250px;">
 </a>
                @if (Session::has('warning') || Session::has('success') || Session::has('info'))
                    <ul class="auth-form__important-infos">
                        @if (Session::has('warning'))
                            <li class="auth-form__important-info">
                                Warning: {{ Session::get('warning') }}
                            </li>
                        @endif

                        @if (Session::has('info'))
                            <li class="auth-form__important-info">
                                Info: {{ Session::get('info') }}
                            </li>
                        @endif

                        @if (Session::has('success'))
                            <li class="auth-form__important-info">
                                Success: {{ Session::get('success') }}
                            </li>
                        @endif
                    </ul>
                @endif

                <p class="auth-form__text-input-group">
                    <label class="auth-form__label" for="username">
                        {{-- {{ __('auth.username') }} --}}
                    </label>
                    <input id="username" class="auth-form__text-input" autocomplete="username" autofocus
                        name="username" required type="text" placeholder="Usuario" value="{{ old('username') }}" />
                </p>
                <p class="auth-form__text-input-group">
                    <label class="auth-form__label" for="password">
                        {{-- {{ __('auth.password') }} --}}
                    </label>
                    <input id="password" class="auth-form__text-input" autocomplete="current-password" name="password"
                        required type="password" placeholder="Contraseña" />
                </p>
                <p class="auth-form__checkbox-input-group">
                    <input id="remember" class="auth-form__checkbox-input" name="remember"
                        {{ old('remember') ? 'checked' : '' }} type="checkbox" />
                    <label class="auth-form__label" for="remember">
                        {{ __('auth.remember-me') }}
                    </label>
                </p>
                @if (config('captcha.enabled'))
                    @hiddencaptcha
                @endif
                
                <div class="auth-form__button-container">                    
                    <button class="auth-form__primary-button">Entrar</button>
                </div>

                <div class="auth-form__button-container">
                    @if (Session::has('errors'))
                        <ul class="auth-form__errors">
                            @foreach ($errors->all() as $error)
                                <li class="auth-form__error">{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </form>
            

<footer class="auth-form__footer">
                @if (!config('other.invite-only'))
                    <a class="auth-form__footer-item" href="{{ route('register') }}">
                        {{ __('auth.signup') }}
                    </a>
                @elseif (config('other.application_signups'))
                    <a class="auth-form__footer-item" href="{{ route('application.create') }}">
                        {{ __('auth.apply') }}
                    </a>
                    @endif
                <a class="auth-form__footer-item" href="{{ route('password.request') }}">
                    {{ __('auth.lost-password') }}
                </a>
<br><br><br><br><br>           
            </footer> 
        </section>
    </main>
</body><br><br>
</html>
