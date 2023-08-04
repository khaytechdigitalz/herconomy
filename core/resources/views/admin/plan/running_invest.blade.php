@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Plan')</th>
                                <th>@lang('Total Return')</th>
                                <th>@lang('Paid Return')</th>
                                <th>@lang('Interest Amount')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Invested')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($investments as $data)
                            <tr>
                                <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{$data->user->fullname}}</span>
                                        <br>
                                    <span class="small">
                                        <a href="{{ route('admin.users.detail', $data->user_id) }}"><span>@</span>{{ $data->user->username }}</a>
                                    </span>
                                </td>

                                <td data-label="@lang('Plan')">
                                    <span class="font-weight-bold" data-toggle="tooltip" data-original-title="@lang('Limitation of Amount')">
                                        {{ $data->plan->name }}
                                    </span>
                                </td>

                                <td data-label="@lang('Total Return')">
                                    {{ $data->total_return }} @lang('Times')
                                </td>

                                <td data-label="@lang('Paid Return')">
                                    {{ $data->total_paid }} @lang('Times')
                                </td>

                                <td data-label="@lang('Interest Amount')">
                                    {{ showAmount($data->interest_amount) }}
                                    {{ __($general->cur_text) }}
                                </td>

                                <td data-label="@lang('Status')">
                                    @if($data->status == 0)
                                        <span class="badge badge--primary">
                                            @lang('Running')
                                        </span>
                                    @elseif($data->status == 1)
                                    <span class="badge badge--success">
                                        @lang('Completed')
                                    </span>
                                    @endif
                                </td>

                                <td data-label="@lang('Invested')">
                                    {{ showDateTime($data->created_at) }} <br> {{ diffForHumans($data->created_at) }}
                                </td>

                            </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($Investments) }}
                </div>
            </div>
        </div>

    </div>


@endsection
