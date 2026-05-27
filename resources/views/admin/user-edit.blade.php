@extends('admin.layouts.admin')

@section('content')

<div class="user-form-page">

    <div class="music-form-card">

        <div class="form-glow"></div>

        <h2 class="form-title">
            Edit User
        </h2>

        <form method="POST"
              action="/admin/users/{{ $user->id }}/update">

            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">

                    <label>Name</label>

                    <input
                        type="text"
                        name="name"
                        value="{{ $user->name }}"
                        required>

                </div>

                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        value="{{ $user->email }}"
                        required>

                </div>

                <div class="form-group">

                    <label>New Password</label>

                    <input
                        type="password"
                        name="password">

                </div>

                <div class="form-group">

                    <label>Role</label>

                    <select name="role">

                        <option value="user"
                            {{ $user->role == 'user' ? 'selected' : '' }}>
                            User
                        </option>

                        <option value="admin"
                            {{ $user->role == 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                    </select>

                </div>

            </div>

            <div class="submit-wrapper">

                <button type="submit" class="save-btn">

                    <i class="fas fa-save"></i>

                    Update User

                </button>

            </div>

        </form>

    </div>

</div>

@endsection