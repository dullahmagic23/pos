@extends('layouts.app')
@section('content')
    <invoice-component :invoice="{{$invoice}}"></invoice-component>
@endsection