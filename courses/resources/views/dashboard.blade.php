@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-md-12">
      <h4 class="mb-1">مرحباً بك في لوحة التحكم</h4>
      <p class="mb-6">هنا يمكنك إضافة محتوى لوحة التحكم الخاصة بك</p>
      
      <!-- يمكنك إضافة المحتوى هنا -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">محتوى لوحة التحكم</h5>
          <p class="card-text">هذا المحتوى خاص بصفحة لوحة التحكم الرئيسية.</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection