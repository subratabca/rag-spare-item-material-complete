@extends('client.layout.master')

@section('title', 'Client || Complaint List')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> Complaint List
@endsection

@section('content')
    @include('client.components.complain.complain-list')
    @include('client.components.complain.reply')
@endsection