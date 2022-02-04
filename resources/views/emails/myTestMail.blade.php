@extends('emails.layouts.layout')
@section('content')
    <h1>{{ $details['msg_title'] }}</h1>
    <p>{{ $details['msg_body'] }}</p>
   
    <p>Thank you</p>
@endsection