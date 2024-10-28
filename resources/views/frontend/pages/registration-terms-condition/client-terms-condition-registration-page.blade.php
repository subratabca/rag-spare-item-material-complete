@extends('frontend.layout.app')
@section('title', 'Client Registration T&C')
@section('content')
    @include('frontend.components.registration-terms-condition.client-registration-terms-condition')
    <script>
        (async () => {
            await ClientRegistrationTCInfo();
        })()
    </script>
@endsection