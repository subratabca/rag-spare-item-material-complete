@extends('backend.layout.master')

@section('title', 'Admin || Item Details')

@section('breadcum')
    <span class="text-muted fw-light">Admin /</span> Item Details
@endsection

@section('content')
    @include('backend.components.food.food-details')
@endsection