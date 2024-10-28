@extends('client.layout.master')

@section('title', 'Client || Item List')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> Item List
@endsection

@section('content')
    @include('client.components.food.index')
    @include('client.components.food.delete')
@endsection
