@extends('frontend.layout.app')
@section('title', 'Food Details')
@section('content')
    @include('frontend.components.food-details')
    <script>
        (async () => {
            await FoodDetailsInfo();
        })()
    </script>
@endsection