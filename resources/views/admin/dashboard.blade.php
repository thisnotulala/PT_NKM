@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">

    <div class="col-md-4">
        <div class="card p-3">
            <div class="stat-card">
                <div>
                    <small>Total Proyek</small>
                    <h3>5</h3>
                </div>
                <i class="fas fa-project-diagram"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <div class="stat-card">
                <div>
                    <small>Proyek Aktif</small>
                    <h3>3</h3>
                </div>
                <i class="fas fa-tasks"></i>
            </div>
        </div>
    </div>

</div>
@endsection
