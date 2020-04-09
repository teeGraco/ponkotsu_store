@extends('layouts.vue_app')

@section('title', '商品購入ページ')
@section('content')
<div id="app">
  <buy-component good-id="{{ $goodId }}"></buy-component>
</div>
@endsection