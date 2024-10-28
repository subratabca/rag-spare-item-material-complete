@extends('backend.layout.master')

@section('title', 'Admin || Complaint List')

@section('breadcum')
    <span class="text-muted fw-light">Admin /</span> Complaint List
@endsection

@section('content')
    @include('backend.components.complain.complain-list')
    @include('backend.components.complain.delete')
@endsection