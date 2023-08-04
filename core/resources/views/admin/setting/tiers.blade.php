@extends('admin.layouts.app')

@section('panel')

    <div class="row">

        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Min Savings Balance')</th>
                                <th>@lang('Max Savings Balance')</th>
                                <th>@lang('Cashback')</th>
                                <th>@lang('Updated At')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($tiers as $data)
                                <tr>
                                    <td data-label="@lang('Name')">{{ $data->name }}</td>
                                    <td data-label="@lang('Min Savings Balance')">{{$general->cur_sym}}{{ number_format($data->min,2) }}</td>
                                    <td data-label="@lang('Max Savings Balance')">{{$general->cur_sym}}{{ number_format($data->max,2) }}</td>
                                    <td data-label="@lang('Cashback ')">{{ number_format($data->discount,2) }}%</td>
                                    <td data-label="@lang('Updated At')">{{ showDateTime($data->created_at) }}</td>
                                    <td data-label="@lang('Action')">
                                        <a href="javascript:void(0)"
                                           data-id="{{ $data->id }}"
                                           data-name="{{ $data->name }}"
                                           data-min="{{ $data->min }}"
                                           data-max="{{ $data->max }}"
                                           class="icon-btn btn--primary ml-1 editModalBtn" data-toggle="tooltip"
                                           data-original-title="@lang('Edit')">
                                            <i class="las la-edit"></i>
                                        </a>
                                        <a href="javascript:void(0)"
                                        data-id="{{ $data->id }}"
                                        class="icon-btn btn--danger ml-1 removeModalBtn" data-toggle="tooltip"
                                        data-original-title="@lang('Remove')">
                                         <i class="las la-trash"></i>
                                     </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">No Data Found</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div> 
            </div><!-- card end -->
        </div>


    </div>



    {{-- Create Tier MODAL --}}
    <div id="createModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Create New Tier')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group mb-2">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group mb-2">
                        <label>Minimum Savings Balance</label>
                        <input type="number" name="min" class="form-control">
                        </div>
                        <div class="form-group mb-2">
                        <label>Maximum Savings Balance</label>
                        <input type="number" name="max" class="form-control">
                        </div>
                        <div class="form-group mb-2">
                        <label>Cashback <b>(%)</b></label>
                        <input type="text" name="discount" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success">@lang('Create')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Create Tier MODAL --}}
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Edit New Tier')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.setting.tier.update') }}" method="POST">
                    @csrf
                    <input name="id" hidden>
                    <div class="modal-body">
                        <div class="form-group mb-2">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control">
                        </div>
                        <div class="form-group mb-2">
                        <label>Minimum Savings Balance</label>
                        <input type="number" name="min" class="form-control">
                        </div>
                        <div class="form-group mb-2">
                        <label>Maximum Savings Balance</label>
                        <input type="number" name="max" class="form-control">
                        </div>

                        <div class="form-group mb-2">
                            <label>Cashback</label>
                            <input type="text" name="discount" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--success">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Remove Subscriber MODAL --}}
    <div id="removeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Are you sure to remove?')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.setting.tier.remove') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="id">
                        <p><span class="font-weight-bold subscriber-email"></span> @lang('will be removed.')</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--danger">@lang('Remove')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <a href="#" class="btn btn-sm btn--primary box--shadow1 text--small createModalBtn" ><i class="fa fa-fw fa-paper-plane"></i>@lang('Create New Tier')</a>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";
            $('.editModalBtn').on('click', function() {
                $('#editModal').find('input[name=id]').val($(this).data('id'));
                $('#editModal').find('input[name=name]').val($(this).data('name'));
                $('#editModal').find('input[name=min]').val($(this).data('min'));
                $('#editModal').find('input[name=max]').val($(this).data('max'));
                $('#editModal').find('input[name=discount]').val($(this).data('discount'));
                $('#editModal').modal('show');
            });

            $('.removeModalBtn').on('click', function() {
                $('#removeModal').find('input[name=id]').val($(this).data('id'));
                $('#removeModal').modal('show');
            });
            $('.createModalBtn').on('click', function() { 
                $('#createModal').modal('show');
            });

        })(jQuery);

    </script>
@endpush
