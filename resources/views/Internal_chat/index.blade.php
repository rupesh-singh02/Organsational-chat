@extends('Layouts.app')

@section('individual_style')
    @include('Internal_chat.style')
@endsection

@section('leftsidebar')
    @include('Internal_chat.Components.leftsidebar')
@endsection

@section('coversationLayout')
    <div id="coversation-layout">
        @include('Internal_chat.Components.Conversation_layout.welcome_layout')
    </div>
@endsection

@section('individual_script')
    @include('Internal_chat.script')
@endsection
