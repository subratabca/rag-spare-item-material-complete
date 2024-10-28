@extends('backend.layout.master')

@section('title', 'Admin || Todays Report')

@section('breadcum')
    <span class="text-muted fw-light">Admin /</span> Todays Report Information
@endsection

@section('content')
    @include('backend.components.report.todays-report')
@endsection