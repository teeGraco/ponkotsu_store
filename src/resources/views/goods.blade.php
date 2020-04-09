@extends('layouts.vue_app')

@section('title', '詳細ページ')
@section('content')
<div id="app">
    <detail-component good-id="{{ $id }}" ></detail-component>
</div>
@endsection