@extends('backend.layout.master')

@section('title', 'Admin || New Item')

@section('breadcum')
    <span class="text-muted fw-light">Admin /</span> Create New Item
@endsection

@section('content')
    @include('backend.components.food.create')
@endsection