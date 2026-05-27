@extends('admin.layouts.admin')

@section('content')

<div class="users-page">

    <!-- HEADER -->
    <div class="page-top">

        <div>

            <h1>User Management</h1>

            <p class="page-subtitle">
                Manage admins and users in Aanaya system
            </p>

        </div>

        <a href="/admin/users/create" class="pink-btn">

            <i class="fas fa-user-plus"></i>

            Add User

        </a>

    </div>

    <!-- TABLE -->
    <div class="users-table-card">

        <table class="users-table">

            <thead>

                <tr>

                    <th>User</th>

                    <th>Email</th>

                    <th>Role</th>

                    <th>Joined</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @foreach($users as $user)

                    <tr>

                        <td>

                            <div class="user-info">

                                <div class="user-avatar">

                                    {{ strtoupper(substr($user->name, 0, 1)) }}

                                </div>

                                <div>

                                    <h4>{{ $user->name }}</h4>

                                </div>

                            </div>

                        </td>

                        <td>{{ $user->email }}</td>

                        <td>

                            <span class="role-badge {{ $user->role }}">

                                {{ ucfirst($user->role) }}

                            </span>

                        </td>

                        <td>

                            {{ $user->created_at->format('d M Y') }}

                        </td>

                        <td>

                            <div class="table-actions">

                                <a href="/admin/users/{{ $user->id }}/edit"
                                   class="edit-btn">

                                    <i class="fas fa-pen"></i>

                                    Edit

                                </a>

                                <form method="POST"
                                      action="/admin/users/{{ $user->id }}/delete">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="delete-btn">

                                        <i class="fas fa-trash"></i>

                                        Delete

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection