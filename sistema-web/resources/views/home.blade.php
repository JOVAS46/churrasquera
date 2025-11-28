@extends('layouts.app')

@section('content')
    <!-- ========== title-wrapper start ========== -->
    <div class="title-wrapper pt-30 text-center">
        <h2 class="page-title">{{ __('Panel de Control') }}</h2>
    </div>
    <!-- ========== title-wrapper end ========== -->
    
    <div class="container-fluid">
        <div class="row">
            <!-- Stats Cards -->
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card stat-widget">
                    <div class="card-body">
                        <h5 class="card-title">Insumos</h5>
                        <h2 class="card-text">{{ $stats['insumos'] ?? 0 }}</h2>
                        <i class="lni lni-package"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card stat-widget">
                    <div class="card-body">
                        <h5 class="card-title">Categorías</h5>
                        <h2 class="card-text">{{ $stats['categorias'] ?? 0 }}</h2>
                        <i class="lni lni-tag"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-lg-6 col-sm-6">
                <div class="card stat-widget">
                    <div class="card-body">
                        <h5 class="card-title">Mesas</h5>
                        <h2 class="card-text">{{ $stats['mesas'] ?? 0 }}</h2>
                        <i class="lni lni-restaurant"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Welcome Message -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4>¡Bienvenido al Sistema de Gestión!</h4>
                        <p>Utiliza el menú lateral para navegar por las diferentes secciones del sistema.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .stat-widget {
            background: linear-gradient(135deg, #4a1c1c 0%, #6d2b2b 100%);
            color: white;
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .stat-widget .card-body {
            padding: 20px;
            position: relative;
        }
        
        .stat-widget h2 {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stat-widget i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 3rem;
            opacity: 0.3;
        }
    </style>
@endsection
