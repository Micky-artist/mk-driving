@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-4rem)]">
    <div class="max-w-6xl mx-auto h-full">
        <!-- Integrated Unified Quiz Taker -->
        <x-unified-quiz-taker 
            :quiz="$quiz" 
            :show-header="true"
            :compact-mode="false"
            :allow-navigation="true"
        />
    </div>
</div>
@endsection