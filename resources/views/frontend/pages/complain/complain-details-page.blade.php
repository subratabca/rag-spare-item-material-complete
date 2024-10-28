@extends('frontend.layout.app')
@section('title', 'User || Complain Details')
@section('content')
    @include('frontend.components.complain.complain-details')
    @include('frontend.components.complain.reply')
@endsection