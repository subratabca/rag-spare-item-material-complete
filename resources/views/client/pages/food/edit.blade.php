@extends('client.layout.master')

@section('title', 'Client || Edit Item')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> Update Item
@endsection

@section('content')
    @include('client.components.food.edit')
@endsection