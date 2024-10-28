@extends('client.layout.master')

@section('title', 'Client || Search Report')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> Report Information By search
@endsection

@section('content')
    @include('client.components.report.search-report')
@endsection