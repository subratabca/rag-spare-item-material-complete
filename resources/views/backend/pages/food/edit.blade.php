@extends('backend.layout.master')

@section('title', 'Admin || Edit Item')

@section('breadcum')
    <span class="text-muted fw-light">Admin /</span> Update Item
@endsection

@section('content')
    @include('backend.components.food.edit')
@endsection