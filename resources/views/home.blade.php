@extends('layouts.app')

@section('content')
    <h1 class="">Dashboard</h1>
    <hr>
    <div class="row mt-2">
        <div class="row">
            <div class="col-md-6">
                <latest-transactions></latest-transactions>
            </div>
            <div class="col-md-6">
                <best-selling></best-selling>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <expired-products></expired-products>
            </div>
            <div class="col-md-6">
                <out-stock></out-stock>
            </div>


        </div>
    </div>
@endsection
