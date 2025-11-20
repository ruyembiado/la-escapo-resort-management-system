@extends('layouts.public') <!-- Extend the main layout -->

@section('content')
    <main class="content px-3 py-4 col-12 home-bg d-flex justify-content-start align-items-center" id="page-top">
        <div class="container-fluid col-10 m-auto">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div class="col-6 justify-content-center align-items-center gap-3">
                    <div class="title-container">
                        <h6 class="text-light welcome-text m-0">Welcome to</h6>
                        <h1 class="text-light home-title m-0">La Escapo Resort</h1>
                    </div>
                    <div class="d-flex flex-column gap-5">
                        <div class="home-description text-light">
                            <i>"La Escapo Billing System: Empowering Accuracy, Simplifying Finances."</i>
                        </div>
                        <div class="home-description text-light">
                            <i>Let La Escapo take you to a unique experience</i>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <img src="{{ asset('public/img/biling-system.png') }}" class="img-fluid" alt="biling-system">
                </div>
            </div>
        </div>
    </main>

    <!-- Offcanvas for the Login Form -->
    <div class="offcanvas offcanvas-end bg-dark" tabindex="-1" id="loginOffcanvas" aria-labelledby="loginOffcanvasLabel">
        <div class="offcanvas-body">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-light" id="loginOffcanvasLabel">Login</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-logo text-center">
                <img src="{{ asset('public/img/logo.png') }}" alt="laescapo-logo" class="img-fluid" width="200">
            </div>
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('login') }}" method="post">
                @csrf

                <div class="mb-2">
                    <label for="username" class="form-label text-light">Username</label>
                    <div class="input-group w-100">
                        <div class="input-group-prepend bg-light rounded-start d-flex align-items-center">
                            <span class="input-group-text border-0" id="basic-addon1"><i
                                    class="fa fa-envelope bg-transparent"></i></span>
                        </div>
                        <input type="username" class="form-control @error('username') is-invalid @enderror"
                            aria-describedby="basic-addon1" id="username" name="username" value="{{ old('username') }}"
                            required>
                    </div>
                    @error('username')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="password" class="form-label text-light">Password</label>
                    <div class="input-group d-flex w-100">
                        <div class="input-group-prepend bg-light rounded d-flex align-items-center w-100">
                            <span class="input-group-text border-0" id="basic-addon2"><i
                                    class="fa fa-lock bg-transparent"></i></span>
                            <input type="password" style="border-radius: 0 6px 6px 0;"
                                class="form-control @error('password') is-invalid @enderror" id="password"
                                aria-describedby="basic-addon2" name="password" required>
                        </div>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-2">
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var loginOffcanvas = new bootstrap.Offcanvas(document.getElementById('loginOffcanvas'));
            loginOffcanvas.show();
        });
    </script>
@endif
