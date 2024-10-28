@extends('client.layout.master')

@section('title', 'Client || New Item')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> Create New Item
@endsection

@section('content')
    @include('client.components.food.create')
@endsection