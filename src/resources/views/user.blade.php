@extends('layouts.vue_app')

@section('title', 'ユーザーページ')
@section('content')
<div id="app">
@isset($id)
    <user-component req-id={{ $id }}></user-component>
@else
    <user-component></user-component>
@endisset
</div>
@endsection