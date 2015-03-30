@extends('master')

@section('content')

   <h1>Hello, {{ $faker->name }}!</h1>
   <div id="status"></div>
   <div id="fb-root"></div>
@stop

@section('footer')

@stop