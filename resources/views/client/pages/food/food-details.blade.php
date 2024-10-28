@extends('client.layout.master')

@section('title', 'Client || Item Details')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> Item Details
@endsection

@section('content')
    @include('client.components.food.food-details')
@endsection