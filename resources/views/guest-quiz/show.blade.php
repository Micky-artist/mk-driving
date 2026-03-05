@extends('layouts.app')

@section('content')
<!-- Use quiz-with-companion for consistency, but companion will be limited for guests -->
<x-quiz-with-companion 
    :quiz="$quiz" 
    :show-header="true"
    :compact-mode="false"
    :allow-navigation="true"
    :show-companion="true"
/>
@endsection