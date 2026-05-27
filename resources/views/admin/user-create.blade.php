@extends('admin.layouts.admin')

@section('content')

<div class="user-form-page">

    <div class="music-form-card">

        <div class="form-glow"></div>

        <h2 class="form-title">
            Create New User
        </h2>

        <form method="POST"
              action="/admin/users/store">

            @csrf

            <div class="form-grid">

                <div class="form-group">

                    <label>Name</label>

                    <input
                        type="text"
                        name="name"
                        required>

                </div>

                <div class="form-group">

                    <label>Email</label>

                    <input
                        type="email"
                        name="email"
                        required>

                </div>

                <div class="form-group">

                    <label>Password</label>

                    <input
                        type="password"
                        name="password"
                        required>

                </div>

                <div class="form-group">

                    <label>Role</label>

                    <select name="role">

                        <option value="user">
                            User
                        </option>

                        <option value="admin">
                            Admin
                        </option>

                    </select>

                </div>

            </div>

            <div class="submit-wrapper">

                <button type="submit" class="save-btn">

                    <i class="fas fa-user-plus"></i>

                    Create User

                </button>

            </div>

        </form>

    </div>

</div>

@endsection