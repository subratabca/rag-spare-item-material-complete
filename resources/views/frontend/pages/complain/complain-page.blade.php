@extends('frontend.layout.app')
@section('title', 'User || Complain List')
@section('content')
    @include('frontend.components.complain.complain-list')
    @include('frontend.components.complain.reply')
@endsection