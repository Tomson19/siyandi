@extends('layouts.bootstrap')

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-success text-white">
    <h5>Dashboard Operator</h5>
  </div>
  <div class="card-body">
    <p>Halo {{ auth()->user()->name }}, Anda login sebagai <b>Admin Operator</b>.</p>
  </div>
</div>
@endsection
