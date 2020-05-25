@extends('layouts.app')

@section('content')
    <component-prize :prizes="{{$dateInRaffle}}"></component-prize>
@endsection
