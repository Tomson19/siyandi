@extends('admin.layouts.admin')

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-info text-white">
    <h5>Dashboard Keuangan</h5>
  </div>
  <div class="card-body">
    <p>Halo {{ auth()->user()->name }}, Anda login sebagai <b>Admin Keuangan</b>.</p>
  </div>
</div>
@endsection
