<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="UTF-8" />
        <title>{{ __('Two Factor Authentication') }} - {{ config('other.title') }}</title>
        <link rel="shortcut icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon" />
        @vite('resources/sass/pages/_auth.scss')
    </head>


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
.auth-form__error, .auth-form__important-info {
    color: #dcdcdc;
    font-weight: 700;
    font-size: 16px;
}

.auth-form__header-item {
    padding: 28px 12px;
    text-decoration: none;
    text-align: center;
    line-height: 1;
    /* font-size: 15px; */
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1ch;
    transition: background-color .25s;
    color: #f3eff6;
    background-color: transparent;
    border: none;
    cursor: pointer;
    width: 100%;
}


.auth-form__header-item:hover {
    background-color: #383838;
    color: var(--White);
}
</style>
    <body>
        <main x-data="{ recovery: false, entered: false }">
            <section class="auth-form">
                <header class="auth-form__header">
                    <button
                        class="auth-form__header-item"
                        x-on:click="
                            recovery = false;
                            $nextTick(() => {
                                $refs.code.focus();
                            })
                        "
                    >
                        {{ __('auth.totp-code') }}
                    </button>
                    <button
                        class="auth-form__header-item"
                        x-on:click="
                            recovery = true;
                            $nextTick(() => {
                                $refs.recovery_code.focus();
                            })
                        "
                    >
                        {{ __('auth.recovery-code') }}
                    </button>
                </header>
                <form
                    class="auth-form__form"
                    method="POST"
                    action="{{ route('two-factor.login') }}"
                >
                    @csrf
                    <a class="auth-form__branding" href="{{ route('home.index') }}">
                        <i class="fal fa-tv-retro"></i>

                <img src="/img/logo15.png" alt="eMuwarez" class="footer__icon" style="width: 250px;">
                        <!--<span class="auth-form__site-logo">{{ \config('other.title') }}</span>-->
                    </a>
                    <ul class="auth-form__important-infos" style="text-align: center;">
                        <li class="auth-form__important-info" x-show="!recovery">
                            {{ __('auth.enter-totp') }}
                        </li>
                        <li class="auth-form__important-info" x-cloak x-show="recovery">
                            {{ __('auth.enter-recovery') }}
                        </li>
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
                    <style>
                    .auth-form__text-input-group {
                    text-align: center; /* Esto centra el contenido dentro de los elementos <p> */
                    }
                    </style>
                    <p class="auth-form__text-input-group" x-show="! recovery">
                        <label class="auth-form__label" for="code">
                            {{ __('auth.code') }}
                        </label>
                        <input
                            id="code"
                            class="auth-form__text-input"
                            autocomplete="one-time-code"
                            autofocus
                            inputmode="numeric"
                            name="code"
                            autocapitalize="off"
                            autocorrect="off"
                            spellcheck="false"
                            x-bind:required="!recovery"
                            type="tel"
                            value="{{ old('code') }}"
                            x-on:input="
                                if ($el.value.length === 6) {
                                    $el.form.submit();
                                    entered = true;
                                }
                            "
                            x-ref="code"
                        />
                    </p>
                    <p class="auth-form__text-input-group" x-cloak x-show="recovery">
                        <label class="auth-form__label" for="recovery_code">
                            {{ __('') }}
                        </label>
                        <input
                            id="recovery_code"
                            class="auth-form__text-input"
                            autocomplete="off"
                            name="recovery_code"
                            autocapitalize="off"
                            autocorrect="off"
                            spellcheck="false"
                            x-bind:required="recovery"
                            type="text"
                            x-ref="recovery_code"
                        />
                    </p>
                    @if (config('captcha.enabled'))
                        @hiddencaptcha
                    @endif

                    <div class="auth-form__button-container">
                        @if (Session::has('errors'))
                            <ul class="auth-form__errors">
                                @foreach ($errors->all() as $error)
                                    <li class="auth-form__error">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="auth-form__button-container">                 
                        <button class="auth-form__primary-button">Entrar</button>
                    </div>
<br><br><br><br><br><br><br><br>                    
                    <!--<div class="discord-div">
                    <a class="discord-widget" href="https://discord.gg/RUKj5JfEST" title="Join us on Discord">
                        <img src="https://discordapp.com/api/guilds/838217297478680596/embed.png?style=banner3">
                    </a>
                </div>-->
                </form>
            </section>
        </main>
        @vite('resources/js/app.js')
    </body>
</html>