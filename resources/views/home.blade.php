@extends('layouts.app')

@section('content')
    <h1 class="">Dashboard</h1>
    <hr>
    <div class="row mt-2">
        <div class="row">
            <div class="col-md-12">
                <latest-transactions></latest-transactions>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <best-selling></best-selling>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <out-stock></out-stock>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <expired-products></expired-products>
            </div>
        </div>
    </div>
@endsection
