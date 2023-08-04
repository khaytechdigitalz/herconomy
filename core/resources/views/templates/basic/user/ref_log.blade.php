@extends($activeTemplate.'layouts.frontend')
@section('content')

<div class="user-profile-section padding-top padding-bottom">
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
        <div class="table-responsive--md">
          <table class="table custom--table">
            <thead>
              <tr>
                <th>@lang('Name')</th>
                <th>@lang('Email')</th>
                <th>@lang('Username')</th>
                <th>@lang('Status')</th>
                <th>@lang('Date Joined')</th>
              </tr>
            </thead>
            <tbody>

            @forelse($logs as $index => $data)
            <tr>
                <td data-label="@lang('Name')">{{ $data->fullname }}</td>
                <td data-label="@lang('Email')">
                    <strong>
                        {{ $data->email }}
                    </strong>
                </td>
                <td data-label="@lang('Username')">
                    <strong>
                        {{ $data->username }}
                    </strong>
                </td>
                
                <td data-label="@lang('Status')">
                    @if($data->status == 0)
                        <span class="badge badge--info">@lang('Inactibe')</span>
                    @else
                        <span class="badge badge--success">@lang('Active')</span>
                    @endif
                </td>
                <td data-label="@lang('Next Return Date')">{{ showDateTime($data->created_at) }}</td>
            </tr>
            @empty
                <tr>
                    <td colspan="100%">{{ __($emptyMessage) }}</td>
                </tr>
            @endforelse

            </tbody>
          </table>
        </div>

        {{$logs->links()}}

      </div>
    </div>
  </div>


@endsection
