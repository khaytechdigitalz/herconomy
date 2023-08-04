@extends('seller.layouts.app')

@section('panel')

<div class="row justify-content-center">

    <div class="loader-container text-center d-none">
        <span class="loader">
            <i class="fa fa-circle-notch fa-spin" aria-hidden="true"></i>
        </span>
    </div>
    

    <div class="col-lg-12">


        <form action="" id="addForm" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="card p-2 has-select2">
                <div class="card-header">
                    <h5 class="card-title mb-0">@lang('Update Settlement Setting')</h5>
                </div>

                <div class="card-header">
                <div class="alert alert-primary" role="alert">
                  <span class=" ml-3"> To update your product sales settlement into your wallet, please select a payment cycle of choice and enter account password to validate and update settlement settings</span>
                </div>
                </div>
                
                <div class="card-body">
                   
                    <div class="form-group row">
                        <div class="col-md-2">
                            <label class="font-weight-bold">@lang('Settlement Cycle') <span class="text--danger">*</span></label>
                        </div>
                        <div class="col-md-10">
                            <select class="form-control select2-basic" name="cycle" required id="brand">
                                <option selected disabled value="">@lang('Select One')</option>

                                <option @if( seller()->settlement_cycle == 0) selected @endif value="0">@lang('Instantly')</option>
                                <option @if( seller()->settlement_cycle == 1) selected @endif value="1">@lang('24 Hours')</option>
                                <option @if( seller()->settlement_cycle == 7) selected @endif value="7">@lang('Weekly')</option>
                                <option @if( seller()->settlement_cycle == 30) selected @endif value="30">@lang('Monthly')</option>
                                 
                            </select>
                        </div>
                    </div>
 

                    <button type="submit" class="btn btn--success mt-3">Update</button>
                </div>

               

            </div>

             
        </form>
    </div>
</div>
 
@endsection

@push('breadcrumb-plugins')

@endpush

@push('script-lib')
<script src="{{ asset('assets/dashboard/js/image-uploader.min.js') }}"></script>
@endpush

@push('style-lib')
<link href="https://fonts.googleapis.com/css?family=Lato:300,700|Montserrat:300,400,500,600,700|Source+Code+Pro&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/dashboard/css/image-uploader.min.css') }}">
@endpush


@push('script')
 

@endpush
