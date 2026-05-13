<section>

    <div class="user-profile-danger">

        <p>
            Once your account is deleted,
            all data and dreamy memories will be permanently removed.
        </p>

        <button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="user-profile-delete-btn">

            Delete My Account

        </button>

    </div>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable>

        <form method="post"
              action="{{ route('profile.destroy') }}"
              class="user-profile-modal">

            @csrf
            @method('delete')

            <h2>
                Delete Account?
            </h2>

            <p>
                Please enter your password to permanently delete your account.
            </p>

            <input
                id="password"
                name="password"
                type="password"
                class="user-profile-input"
                placeholder="Enter your password">

            <x-input-error
                :messages="$errors->userDeletion->get('password')"
                class="mt-2" />

            <div class="user-profile-modal-actions">

                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="user-profile-cancel-btn">

                    Cancel

                </button>

                <button class="user-profile-confirm-btn">

                    Delete Permanently

                </button>

            </div>

        </form>

    </x-modal>

</section>