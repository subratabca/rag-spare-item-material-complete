@extends('backend.layout.master')

@section('title', 'Admin || Complain Details')

@section('breadcum')
    <span class="text-muted fw-light">Admin /</span> Complain Details Information
@endsection

@section('content')
    @include('backend.components.complain.complain-details')
    @include('backend.components.complain.complain-feedback-by-admin')
@endsection

