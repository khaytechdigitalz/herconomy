@extends('admin.layouts.app')

@section('panel')

<div class="row">

    <div class="col-xl-12 col-md-12 mb-30">
        <div {{gradient()}} class="widget bb--3 border--success b-radius--10 bg--white p-4 box--shadow2 has--link">
            <div class="widget__icon b-radius--rounded bg--dark"><i class="las la-gift"></i></div>
            <div class="widget__content">
                <p class="text-uppercase text-white">{{$pageTitle}}</p>
                <h1 class="text--white font-weight-bold">
                    {{ $general->cur_sym.showAmount($sum) }}
                </h1> 
            </div>
        </div><!-- widget end -->
    </div>

    <div class="col-lg-12">
        
        <div class="card b-radius--10">
            <div class="card-body p-0">
                 
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th class="text-white">@lang('Transaction ID')</th>
                                <th class="text-white">@lang('Customer')</th>
                                <th class="text-white">@lang('Products')</th>
                                <th class="text-white">@lang('Amount')</th>
                                <th class="text-white">@lang('Customer Cashback')</th>
                                @if(request()->routeIs('admin.cashback.admin'))
                                <th class="text-white">@lang('Admin Earning')</th>
                                @endif
                                <th class="text-white">@lang('Time')</th> 
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                             @forelse($cashbacks as $k=>$data)
                             {{--}}
                            @php
                            $user = App\Models\User::whereId($data->user_id)->first(); 
                             $product = App\Models\Product::whereId($data->product_id)->first();   
                            @endphp
                            {{--}}
                            @php
                            $return = 0;
                            $amount = 0;
                            $admin_fee = 0;
                            @endphp
                            @foreach($data as $cashback)
                            @php $return += $cashback->cashback; @endphp
                            @php $admin_fee += $cashback->admin_fee; @endphp
                            @php $amount += $cashback->amount; @endphp
                            @php $user = App\Models\User::whereId($cashback->user_id)->first();  @endphp

                            @endforeach
                                <tr>
                                    <td data-label="#@lang('Order Trx')">{{$cashback->order_ref}}</td>
                                    <td data-label="#@lang('Customers')">{{$user->username ?? 'N/A'}}</td>
                                     <td data-label="@lang('Products')">{{count($data)}} Products</td>
                                    <td data-label="@lang('Amount')"> 
                                        <strong>{{$general->cur_sym}}{{number_format($amount,2)}}</strong>
                                    </td>
                                    <td>
                                        <a class="text-success">{{$general->cur_sym}}{{number_format($return,2)}}</a>
                                    </td>
                                    @if(request()->routeIs('admin.cashback.admin'))
                                    <td>
                                        <a class="text-success">{{$general->cur_sym}}{{number_format($admin_fee,2)}}</a>
                                    </td>
                                    @endif
                                    <td data-label="@lang('Time')">
                                        {{ showDateTime($cashback->created_at, 'd M, Y') }}
                                    </td>
                                    <td><button class="btn btn--primary" href="#" data-toggle="modal" data-target="#cashbackModal{{$k}}">Details</button></td>

                                    
                                </tr>


                                <!-- Modal -->
                                <div class="modal fade" id="cashbackModal{{$k}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Cashback Details</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group">
                                            @php 
                                            $return = 0;
                                            $adminreturn = 0;
                                            @endphp
                                            @foreach($data as $cashback)
                                            @php $return += $cashback->cashback; @endphp
                                            @php $adminreturn += $cashback->admin_fee; @endphp
                                            @php $amount += $cashback->amount; @endphp
                                            @php $user = App\Models\User::whereId($cashback->user_id)->first();  @endphp
                                            @php $product = App\Models\Product::whereId($cashback->product_id)->first();  @endphp  
                                            <li class="list-group-item">{{$product->name ?? "N/A"}} : {{$general->cur_sym}}{{number_format($cashback->cashback,2)}}</li>
                                            @endforeach 
                                            <li class="list-group-item"><b>CUSTOMER'S CASHBACK:  {{$general->cur_sym}}{{number_format($return,2)}}</b>
                                                @if(request()->routeIs('admin.cashback.admin'))
                                                <li class="list-group-item"><b>ADMIN CASHBACK:  {{$general->cur_sym}}{{number_format($adminreturn,2)}}</b>
                                                @endif
                                          </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn--primary" data-dismiss="modal">Close</button>
                                    </div>
                                    </div>
                                </div>
                                </div>
  
                        @empty
                            <tr>
                                <td colspan="100%">No cashback record at the moment</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                   {{--}} {{$cashbacks->appends(request()->all())->links()}} {{--}}
                </div>
            </div>
         
        </div>
    </div>
</div>

 
@endsection


@push('script')
    <script>
        (function ($) {
            "use strict";
            $('.moreViewBtn').on('click', function() {
                var modal = $('#approveModal');
                modal.find('.withdraw-amount').text($(this).data('amount'));
                modal.find('.withdraw-charge').text($(this).data('charge'));
                modal.find('.withdraw-after_charge').text($(this).data('after_charge'));
                modal.find('.withdraw-rate').text($(this).data('rate'));
                modal.find('.withdraw-payable').text($(this).data('payable'));

                if($(this).data('method_code') >999){

                    var list = [];
                    var details =  Object.entries($(this).data('info'));

                    var ImgPath = "{{asset(imagePath()['verify']['deposit']['path'])}}/";
                    var singleInfo = '';

                    for (var i = 0; i < details.length; i++) {
                        if (details[i][1].type == 'file') {
                            singleInfo += `<li class="list-group-item">
                                                <span class="font-weight-bold "> ${details[i][0].replaceAll('_', " ")} </span> : <img src="${ImgPath}/${details[i][1].field_name}" alt="@lang('Image')" class="w-100">
                                            </li>`;
                        }else{
                            singleInfo += `<li class="list-group-item">
                                                <span class="font-weight-bold "> ${details[i][0].replaceAll('_', " ")} </span> : <span class="font-weight-bold ml-3">${details[i][1].field_name}</span>
                                            </li>`;
                        }
                    }

                    if (singleInfo)
                    {
                        modal.find('.withdraw-detail').html(`<br><strong class="my-3">@lang('Payment Information')</strong>  ${singleInfo}`);
                    }else{
                        modal.find('.withdraw-detail').html(`${singleInfo}`);
                    }
                }


                modal.modal('show');
            });

            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');
                var feedback = $(this).data('admin_feedback');
                modal.find('.withdraw-detail').html(`<p> ${feedback} </p>`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush

