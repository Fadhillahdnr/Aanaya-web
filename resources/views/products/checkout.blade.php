<x-app-layout>

<div class="checkout-page">

    <div class="checkout-card">

        <h1>
            Dream Checkout ✨
        </h1>

        <form action="/checkout/process"
              method="POST">

            @csrf

            <div class="form-group">
                <label>Name</label>
                <input type="text"
                       name="name"
                       required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email"
                       name="email"
                       required>
            </div>

            <div class="form-group">
                <label>Phone</label>
                <input type="text"
                       name="phone"
                       required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea name="address"
                          rows="5"
                          required></textarea>
            </div>

            <button type="submit"
                    class="checkout-submit-btn">

                ✨ Complete Checkout

            </button>

        </form>

    </div>

</div>

</x-app-layout>