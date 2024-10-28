@extends('client.layout.master')

@section('title', 'Client || T&C Details')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> T&C Details Information
@endsection

@section('content')
    @include('client.components.terms-condition.details')
@endsection