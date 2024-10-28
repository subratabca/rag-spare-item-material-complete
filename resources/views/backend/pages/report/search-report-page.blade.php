@extends('backend.layout.master')

@section('title', 'Admin || Search Report')

@section('breadcum')
    <span class="text-muted fw-light">Admin /</span> Report Information By search
@endsection

@section('content')
    @include('backend.components.report.search-report')
@endsection