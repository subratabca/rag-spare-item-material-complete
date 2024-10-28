@extends('backend.layout.master')

@section('title', 'Admin || Item List')

@section('breadcum')
    <span class="text-muted fw-light">Admin /</span> Item List
@endsection

@section('content')
    @include('backend.components.food.index')
    @include('backend.components.food.delete')
@endsection
