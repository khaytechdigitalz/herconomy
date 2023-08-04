    <!-- ===========Cart=========== -->
    <div class="cart-sidebar-area" id="cart-sidebar-area">
        @include($activeTemplate.'partials.side_modal_logo')
        <div class="bottom-content">
            <div class="cart-products cart--products">

            </div>
        </div>
    </div>
    <!-- ===========Cart End=========== -->

    <!-- ===========Wishlist=========== -->
    <div class="cart-sidebar-area" id="wish-sidebar-area">
        @include($activeTemplate.'partials.side_modal_logo')

        <div class="bottom-content">
            <div class="cart-products wish-products">

            </div>
        </div>
    </div>
    <!-- ===========Wishlist End=========== -->


    <!-- Header Section Ends Here -->
    <div {{gradient()}} class="dashboard-menu before-login-menu d-flex flex-wrap justify-content-center flex-column" id="account-sidebar-area">
        <span class="side-sidebar-close-btn"><i class="las la-times"></i></span>
         

        @auth
        @include($activeTemplate.'user.partials.dp')
        <ul class="cl-white" >
            @include($activeTemplate.'user.partials.sidebar')
        </ul>
        @endauth

    </div>
