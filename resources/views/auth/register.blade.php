@extends('adminlte::auth.auth-page', ['authType' => 'register'])

@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
    }
@endphp

@section('auth_header', __('adminlte::adminlte.register_message'))

@section('auth_body')
    <form action="{{ $registerUrl }}" method="post">
        @csrf
        {{-- Name field --}}
        <div class="input-group mb-3">
            <input                 
                type="text" 
                name="name" 
                class="form-control @error('name') 
                is-invalid @enderror"
                value="{{ old('name') }}" 
                placeholder="{{ __('adminlte::adminlte.full_name') }}" 
                autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}">                        
                    </span>
                </div>
            </div>

            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input                 
                type="email" name="email" 
                class="form-control
                    @error('email') is-invalid @enderror"
                value="{{ old('email') }}" 
                placeholder="{{ __('adminlte::adminlte.email') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}">

                    </span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- chosen fee  -->
        <div>
            <div class="form-group">  
                <select                     
                    name="fee" 
                    class="form-control text-center 
                        @error('fee') is-invalid @enderror" id="fee">
                    <option value="">-Selecciona una cuota-</option>
                    <option value="monthly">Mensual</option>
                    <option value="quarterly">Trimestral</option>
                    <option value="yearly">Anual</option>
                </select>            
            </div>
            @error('fee')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>        

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input                 
                type="password" 
                name="password" 
                class="form-control @error('password') 
                    is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirm password field --}}
        <div class="input-group mb-3">
            <input                 
                type="password" 
                name="password_confirmation"
                class="form-control @error('password_confirmation')
                    is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.retype_password') }}">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- privacy -->
        <div class="input-group mb-3">
            <input                 
                type="checkbox" 
                name="privacy" 
                class="@error('privacy') 
                    is-invalid @enderror"
                id="privacy">
            <div class="input-group-append">
                <span class="ml-2">Acepto la</span>
                <a href="{{ route('privacy.policy') }}"
                    target="_blank" 
                    class="">politica de privacidad
                </a>
            </div>

            @error('privacy')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Register button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus"></span>
            {{ __('adminlte::adminlte.register') }}
        </button>
    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="{{ $loginUrl }}">
            {{ __('adminlte::adminlte.i_already_have_a_membership') }}
        </a>
    </p>
@stop
