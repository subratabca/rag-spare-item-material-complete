@extends('client.layout.master')

@section('title', 'Client || Order Details')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> Order Details Information
@endsection

@section('content')
    @include('client.components.order.order-details')
@endsection