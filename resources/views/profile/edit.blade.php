<x-app-layout>

<div class="user-profile-page">

    <!-- BG -->
    <div class="user-profile-bg blur-1"></div>
    <div class="user-profile-bg blur-2"></div>


    
    <!-- HERO -->
    <section class="user-profile-hero">

        <span class="user-profile-badge">
            ✨ DREAM PROFILE
        </span>

        <h1>
            Manage Your
            <span>
                Account
            </span>
        </h1>

        <p>
            Update your profile information, secure your account,
            and personalize your dreamy Aanaya experience.
        </p>

    </section>

    <!-- CONTENT -->
    <div class="user-profile-grid">

        <!-- PROFILE INFO -->
        <div class="user-profile-card">

            <div class="user-profile-card-header">

                <h2>Profile Information</h2>

                <p>
                    Update your account details and email address.
                </p>

            </div>

            @include('profile.partials.update-profile-information-form')

        </div>

        <!-- PASSWORD -->
        <div class="user-profile-card">

            <div class="user-profile-card-header">

                <h2>Update Password</h2>

                <p>
                    Keep your account safe with a secure password.
                </p>

            </div>

            @include('profile.partials.update-password-form')

        </div>

        <!-- DELETE -->
        <div class="user-profile-card danger-card">

            <div class="user-profile-card-header">

                <h2>Delete Account</h2>

                <p>
                    Permanently remove your account and all data.
                </p>

            </div>

            @include('profile.partials.delete-user-form')

        </div>

    </div>

</div>

</x-app-layout>