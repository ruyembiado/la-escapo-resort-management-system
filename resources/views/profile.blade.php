@extends('layouts.auth') <!-- Extend the main layout -->

@section('content')
    <!-- Start the content section -->
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0">Profile</h1>
    </div>

    <!-- Content Row -->
    <div class="card col-12 mb-2 px-0">
        <div class="card-body">
            <form action="{{ route('profile.update') }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="d-flex justify-content-center align-items-center gap-5">
                    <div class="col-5">
                        <div class="col-8 m-auto">
                            <div class="mb-2 text-center">
                                <img class="border" style="border-radius: 100%;"
                                    src="{{ $user->avatar ? asset('public/img/' . $user->avatar) : asset('public/img/logo.png') }}"
                                    width="250px" height="250px" alt="user-avatar">
                                <div class="mt-2 mb-2 m-auto">
                                    <input type="file"
                                        class="form-control border-success @error('avatar') is-invalid @enderror"
                                        id="avatar" name="avatar">
                                    @error('avatar')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2">
                                    <input type="text"
                                        class="form-control border-success text-center @error('name') is-invalid @enderror"
                                        id="name" name="name" placeholder="Name" value="{{ $user->name }}"
                                        required>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-2">
                                    <div
                                        class="bg-success text-center text-light p-2 rounded d-flex gap-3 justify-content-center align-items-center">
                                        <img class="border" style="border-radius: 100%;"
                                            src="{{ asset('public/img/logo.png') }}" width="30" alt="laescapo-logo">
                                        <span>Administrative Personnel</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 border-success border rounded">
                            <div class="mb-2">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="fa fa-home text-success"></i>
                                    <input type="text"
                                        class="form-control border-success @error('address') is-invalid @enderror"
                                        id="address" placeholder="Home Address" name="address"
                                        value="{{ $user->address }}" required>
                                </div>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="fa fa-calendar text-success"></i>
                                    <input type="date"
                                        class="form-control border-success @error('birth_date') is-invalid @enderror"
                                        id="birth_date" placeholder="Birthdate" name="birth_date"
                                        value="{{ $user->birth_date }}" required>
                                </div>
                                @error('birth_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="fa fa-phone text-success"></i>
                                    <input type="text"
                                        class="form-control border-success @error('phone_number') is-invalid @enderror"
                                        id="phone_number" placeholder="Phone Number" name="phone_number"
                                        value="{{ $user->phone_number }}" required>
                                </div>
                                @error('phone_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="fa fa-envelope text-success"></i>
                                    <input type="email"
                                        class="form-control border-success @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="Email Address"
                                        value="{{ $user->email }}" required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="fa fa-user-circle text-success"></i>
                                    <input type="text"
                                        class="form-control border-success @error('username') is-invalid @enderror"
                                        id="username" name="username" placeholder="Username" value="{{ $user->username }}"
                                        required>
                                </div>
                                @error('username')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-2">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="fa fa-key text-success"></i>
                                    <input type="password"
                                        class="form-control border-success @error('password') is-invalid @enderror"
                                        id="password" placeholder="Password" name="password">
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-2">
                                <div class="d-flex gap-3 align-items-center">
                                    <i class="fa fa-key text-success"></i>
                                    <input type="password"
                                        class="form-control border-success @error('confirm_password') is-invalid @enderror"
                                        id="confirm_password" placeholder="Confirm Password" name="confirm_password">
                                </div>
                                @error('confirm_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="text-end mt-2">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Content Row -->
@endsection <!-- End the content section -->
