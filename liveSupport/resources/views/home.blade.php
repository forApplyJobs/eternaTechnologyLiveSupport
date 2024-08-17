@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-center">
        <div class="w-full">
            <div class="bg-white shadow-md rounded-lg">
                <div class="bg-gray-800 text-white font-bold text-lg p-4 rounded-t-lg">
                    Eterna Live Support
                </div>

                <div class="p-6" id="app">
                    <main-component :user="{{ auth()->user() }}"></main-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
