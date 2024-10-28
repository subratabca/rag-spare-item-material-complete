@extends('client.layout.master')

@section('title', 'Client || Todays Report')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> Todays Report Information
@endsection

@section('content')
    @include('client.components.report.todays-report')
@endsection