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
                                <th>@lang('Name')</th>
                                <th>@lang('Limit')</th>
                                <th>@lang('Total Return')</th>
                                <th>@lang('Interest Type')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($plan as $data)
                            <tr>
                                <td data-label="@lang('Name')">
                                    <span class="font-weight-bold">
                                        {{ __($data->name) }}
                                    </span>
                                </td>

                                <td data-label="@lang('Limit')">
                                    <span class="font-weight-bold" data-toggle="tooltip" data-original-title="@lang('Limitation of Amount')">
                                        {{ $general->cur_sym }} {{ showAmount($data->min_amount) }} -
                                        {{ $general->cur_sym }} {{ showAmount($data->max_amount) }}
                                    </span>
                                </td>

                                <td data-label="@lang('Total Return')">
                                    {{ $data->total_return }} @lang('Times')
                                </td>

                                <td data-label="@lang('Interest Type')">
                                    @if($data->interest_type == 0)
                                        @lang('Fixed')
                                    @else
                                        @lang('Percent')
                                    @endif
                                </td>

                                <td data-label="@lang('Status')">
                                    @if($data->status == 0)
                                        <span class="badge badge--danger">
                                            @lang('Disable')
                                        </span>
                                    @else
                                        <span class="badge badge--success">
                                            @lang('Enable')
                                        </span>
                                    @endif
                                </td>

                                <td data-label="@lang('Action')">
                                    <a href="#0"

                                    data-id='{{ $data->id }}'
                                    data-name='{{ $data->name }}'
                                    data-status='{{ $data->status }}'
                                    data-min_amount='{{ getAmount($data->min_amount) }}'
                                    data-max_amount='{{ getAmount($data->max_amount) }}'
                                    data-total_return='{{ $data->total_return }}'
                                    data-interest_type='{{ $data->interest_type }}'
                                    data-interest='{{ getAmount($data->interest_amount) }}'

                                    class="icon-btn editBtn"
                                    data-toggle="tooltip"
                                    title="@lang('Edit')"
                                    data-original-title="@lang('Edit')"
                                    >
                                        <i class="las la-edit text--shadow"></i>
                                    </a>
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
                    {{ paginateLinks($plan) }}
                </div>
            </div>
        </div>

    </div>

{{-- ADD METHOD MODAL --}}
<div id="addModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Plan')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.plan.create') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name">@lang('Name')</label>
                                <input type="text" name="name" class="form-control" id="name" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="min_amount">@lang('Minimum Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="min_amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" id="min_amount" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="max_amount">@lang('Maximum Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="max_amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" id="max_amount" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="total_return">@lang('How Many Return')</label>
                                <div class="input-group">
                                    <input type="number" id="total_return" class="form-control" name="total_return" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            @lang('Times')
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="interest_type">@lang('Interest Type')</label>
                                <div class="input-group">
                                    <select name="interest_type" id="interest_type" required class="form-control">
                                        <option>@lang('Select An Option')</option>
                                        <option value="1">@lang('Percent')</option>
                                        <option value="0">@lang('Fixed')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="interest">@lang('Interest Amount')</label>
                                <div class="input-group">
                                    <input type="text" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" name="interest" id="interest" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span id="change_interest_symbol">%</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="status">@lang('Status')</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1">@lang('Enable')</option>
                                    <option value="0">@lang('Disable')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary">@lang('Save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ADD METHOD MODAL --}}
<div id="editModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Plan')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.plan.edit') }}" method="POST">
                @csrf

                <input type="hidden" name="id" required>

                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="edit_name">@lang('Name')</label>
                                <input type="text" name="name" class="form-control" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="edit_min_amount">@lang('Minimum Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="min_amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" id="edit_min_amount" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="edit_max_amount">@lang('Maximum Amount')</label>
                                <div class="input-group">
                                    <input type="text" name="max_amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" id="edit_max_amount" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">{{ __($general->cur_text) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="edit_total_return">@lang('How Many Return')</label>
                                <div class="input-group">
                                    <input type="number" id="edit_total_return" class="form-control" name="total_return" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            @lang('Times')
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="edit_interest_type">@lang('Interest Type')</label>
                                <div class="input-group">
                                    <select name="interest_type" id="edit_interest_type" required class="form-control">
                                        <option>@lang('Select An Option')</option>
                                        <option value="1">@lang('Percent')</option>
                                        <option value="0">@lang('Fixed')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="edit_interest">@lang('Interest Amount')</label>
                                <div class="input-group">
                                    <input type="text" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" name="interest" id="edit_interest" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <span id="update_interest_symbol">%</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="edit_status">@lang('Status')</label>
                                <select name="status" id="edit_status" class="form-control" required>
                                    <option value="1">@lang('Enable')</option>
                                    <option value="0">@lang('Disable')</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary">@lang('Update')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
    <a href="javascript:void(0)" class="btn btn-sm btn--primary box--shadow1 text-white text--small addBtn"><i class="fa fa-fw fa-plus"></i>@lang('Add New')</a>
@endpush

@push('script')
<script>
    (function ($) {

        "use strict";

        $('.addBtn').on('click', (e)=> {
            var modal = $('#addModal');
            modal.modal('show');
        });

        $('#interest_type').on('change', (e)=>{
            var $this = e.currentTarget;

            var result = null;

            if($this.value == 0){
                result = '{{ __($general->cur_text) }}';
            }else{
                result = '%';
            }

            $('#change_interest_symbol').text(result);

        });

        $('#edit_interest_type').on('change', (e)=>{
            var $this = e.currentTarget;

            var result = null;

            if($this.value == 0){
                result = '{{ __($general->cur_text) }}';
            }else{
                result = '%';
            }

            $('#update_interest_symbol').text(result);

        });

        $('.editBtn').on('click', (e)=> {
            var $this = $(e.currentTarget);
            var modal = $('#editModal');

            var result = null;

            if($this.data('interest_type') == 0){
                result = '{{ __($general->cur_text) }}';
            }else{
                result = '%';
            }

            $('#update_interest_symbol').text(result);

            modal.find('input[name=id]').val($this.data('id'));
            modal.find('input[name=name]').val($this.data('name'));
            modal.find('input[name=total_return]').val($this.data('total_return'));
            modal.find('input[name=max_amount]').val($this.data('max_amount'));
            modal.find('input[name=min_amount]').val($this.data('min_amount'));
            modal.find('input[name=interest]').val($this.data('interest'));
            modal.find('select[name=status]').val($this.data('status'));
            modal.find('select[name=interest_type]').val($this.data('interest_type'));
            modal.modal('show');
        });

    })(jQuery);

</script>
@endpush
