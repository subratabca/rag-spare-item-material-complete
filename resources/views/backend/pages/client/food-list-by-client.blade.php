@extends('backend.layout.master')

@section('title', 'Admin || Food List')

@section('breadcum')
    <span class="text-muted fw-light">Admin /</span> Food List By Client
@endsection

@section('content')
    @include('backend.components.client.food-list-by-client')
@endsection