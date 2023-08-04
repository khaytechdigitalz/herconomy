@extends($activeTemplate.'layouts.frontend')
@section('content')


<div class="dashboard-section padding-bottom padding-top">
    <div class="container">
        <div class="row">
            <div class="col-xl-3">
                <div class="dashboard-menu">
                    @include($activeTemplate.'user.partials.dp')
                    <ul>
                        @include($activeTemplate.'user.partials.sidebar')
                    </ul>
                </div>
            </div>
            <div class="col-xl-9">
                <div class="row justify-content-center mb-30-none">

                        {{--}}
                        @if(Auth::user()->affiliate != null)
                        <div class="card shadow-md border-0 mb-2">
                            <div class="card-header bg-transparent">
                                <h5 class="">@lang('Affiliate Account')</h5>
                            </div>
                            <div class="card-header bg-transparent">
                                <h5 class="">@lang('You can share the QR code to your downline to register using your affiliate code')</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input type="text" name="key" value="{{url('/')."/user/register?affiliatecode=".Auth::user()->affiliate}}" class="form-control form-control-lg shadow-none outline-0" id="referralURL" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn--base input-group-text copytext border-0" id="copyBoard"> <i class="fa fa-copy"></i> </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mx-auto text-center">
                                    <img class="mx-auto" width="100" src="{{cryptoQR(url('/')."/user/affiliate?affiliatecode=".Auth::user()->affiliate)}}">
                                </div> 
                            </div>
                        </div>
                        @endif

                        @push('script')
                        <script>
                            (function($){
                                "use strict";

                                $('.copytext').on('click',function(){
                                    var copyText = document.getElementById("referralURL");
                                    copyText.select();
                                    copyText.setSelectionRange(0, 99999);
                                    document.execCommand("copy");
                                    iziToast.success({message: "Copied: " + copyText.value, position: "topRight"});
                                });
                            })(jQuery);
                        </script>
                        @endpush

                        @if(Auth::user()->ref != null)
                        <div class="alert alert-icon alert-primary" role="alert">
                            <em class="icon ni ni-alert-circle"></em> 
                            <strong>You were refered by: {{Auth::user()->ref}}</strong> . 
                        </div>
                        @endif
                        {{--}}
                        
                        <div class="col-sm-6 col-lg-6">
                            <div class="dashboard-item">
                                <a href="#" class="d-block">
                                    <span class="dashboard-icon">
                                        <i class="las la-wallet"></i>
                                    </span>
                                    <div class="cont">
                                         <div class="dashboard-header">
                                            <h2 class="title">{{$general->cur_sym}}{{ number_format(Auth::user()->balance,2) }}</h2>
                                        </div>
                                        @lang('Wallet Balance')
                                    </div>
                                </a>
                            </div> 
                        </div>

                        <div class="col-sm-6 col-lg-6">
                            <div class="dashboard-item">
                                <a href="{{route('user.cashback')}}" class="d-block">
                                    <span class="dashboard-icon">
                                        <i class="las la-wallet"></i>
                                    </span>
                                    <div class="cont">
                                         <div class="dashboard-header">
                                            <h2 class="title">{{$general->cur_sym}}{{ number_format(Auth::user()->cashback_balance,2) }}</h2>
                                        </div>
                                        @lang('Cashback Wallet')
                                    </div>
                                </a>
                            </div> 
                        </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'all')}}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-list-ol"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('All Orders')
                                </div>
                            </a>
                        </div>
                    </div>


                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'pending')}}">
                                <span class="dashboard-icon">
                                    <i class="las la-clipboard-list"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status', 0)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Pending Orders')
                                </div>
                            </a>
                        </div>
                    </div>


                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'processing')}}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-list-ul"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status', 1)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Processing Orders')
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'dispatched')}}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-th-list"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status',2)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Dispatched Orders')
                                </div>
                            </a>
                        </div>
                    </div>


                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'completed')}}" class="d-block">
                                <span class="dashboard-icon">
                                    <i class="las la-list-alt"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status', 3)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Order Completed')
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-4">
                        <div class="dashboard-item">
                            <a href="{{route('user.orders', 'canceled')}}">
                                <span class="dashboard-icon">
                                    <i class="las la-times"></i>
                                </span>
                                <div class="cont">
                                    @php $number = numberShortFormat($orders->where('status', 4)->count()) @endphp
                                    <div class="dashboard-header">
                                        <h2 class="title">{{ $number[0] }}</h2>
                                        <h2 class="title">{{  $number[1] }}</h2>
                                    </div>
                                    @lang('Canceled Orders')
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
