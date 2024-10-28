@extends('frontend.layout.app')
@section('title', 'Home')
@section('content')

@include('frontend.components.home.food-list')
<script>
        (async () => {
           await FoodList();
        })();
</script>

@endsection

