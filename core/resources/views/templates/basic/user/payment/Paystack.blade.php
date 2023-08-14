@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="container padding-bottom padding-top">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-6 col-xl-4">
            <div class="card text-center">
                <div class="card-body">
                    <img src="{{$deposit->gatewayCurrency()->methodImage()}}"   alt="@lang('Image')"  class="card-img-top w-25" >
                    <div>
                        <ul class="list-group list-group-flush text-center ">
                            <li class="list-group-item d-flex justify-content-between align-items-center">@lang('Amount'): <strong>{{showAmount($deposit->amount)}} {{$general->cur_text}}</strong></li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Charge'):
                                <span><strong>{{showAmount($deposit->charge)}}</strong> {{$general->cur_text}}</span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Payable'): <strong>{{showAmount($deposit->amount + $deposit->charge)}} {{$general->cur_text}}</strong>
                            </li>
                        </ul>
                         
                        <button type="button" class="btn--base mt-4 d-block  w-100" id="btn-confirm" onClick="payWithRave()">@lang('Pay Now')</button>
                    </div>
                    <form action="{{ route('ipn.'.$deposit->gateway->alias) }}" method="POST" class="text-center">
                        <script
                        src="//js.paystack.co/v1/inline.js"
                        data-key="{{ $data->key }}"
                        data-email="{{ $data->email }}"
                        data-amount="{{$data->amount}}"
                        data-currency="{{$data->currency}}"
                        data-ref="{{ $data->ref }}"
                        data-custom-button="btn-confirm"
                        >
                       </script>
                   </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
