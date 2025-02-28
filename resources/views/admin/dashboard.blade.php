@extends('layouts.master')
@section('content')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard v1</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-4 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            @if (Auth::user()->hasRole('Sub Agent'))
                                <h3>{{ number_format($user->parent->wallet->balanceFloat, 2) }}</h3>
                            @else
                                <h3>{{ number_format($user->wallet->balanceFloat, 2) }}</h3>
                            @endif
                            <p>Balance</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="#" class="small-box-footer"> <i class="fas "></i></a>
                    </div>
                </div>
                @if ($role['0'] == 'Senior')
                    <!-- ./col -->
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($totalBalance->balance / 100, 2) }}</h3>

                                <p>Owner Total Balance</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="{{ route('admin.agent.index') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>

                        </div>
                    </div>
                @endif
                @if ($role['0'] == 'Owner')
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($totalBalance->balance / 100, 2) }}</h3>
                                <p>Agent Total Balance</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="{{ route('admin.agent.index') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                @endif
                <!-- ./col -->
                <!-- ./col -->
                @can('senior_access')
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-danger">
                            <div class="inner">
                                @if ($playerBalance)
                                    <h3>{{ number_format($playerBalance->balance / 100, 2) }}</h3>
                                @else
                                    <h3>0.00</h3>
                                @endif
                                <p>Player Balance</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="{{ route('admin.playerList') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                @endcan

                <!-- ./col -->

                {{-- senior balance update start  --}}

                @can('senior_access')
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <form action="{{ route('admin.balanceUp') }}" method="post">
                                @csrf
                                <div class="card-header p-3 pb-0">
                                    <h6 class="mb-1">Update Balance</h6>
                                    <p class="text-sm mb-0">
                                        Seninor can update balance.
                                    </p>
                                </div>
                                <div class="card-body p-3">
                                    <div class="input-group input-group-static my-4">
                                        <label>Amount</label>
                                        <input type="integer" class="form-control" name="balance">
                                    </div>

                                    <button class="btn bg-gradient-dark mb-0 float-end">SeninorUpdate </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endcan
                @can('owner_access')
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $totalAgent }}</h3>
                                <p>Total Agent</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="{{ route('admin.agent.index') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                @endcan
                @can('owner_access')
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ $totalPlayer }}</h3>
                                <p>Total Player</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="{{ route('admin.GetOwnerPlayerList') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                @endcan
                <!-- ./col -->

                {{-- senior balance update end --}}
            </div>
        </div>
    </section>
@endsection
