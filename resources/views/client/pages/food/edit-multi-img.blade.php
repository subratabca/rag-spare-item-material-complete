@extends('client.layout.master')

@section('title', 'Client || Edit Multi Img')

@section('breadcum')
    <span class="text-muted fw-light">Client /</span> Update Item Multi Image
@endsection

@section('content')
    @include('client.components.food.edit-multi-img')
@endsection