@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Owner W/L Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('home') }}" class="btn btn-primary " style="width: 100px;"><i
                                class="fas fa-arrow-left text-white  mr-2"></i>Back</a>
                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Report</h3>
                        </div>
                        <form role="form" class="text-start" action="{{ route('admin.shan.reports.index') }}"
                            method="GET">
                            <div class="row ml-5">
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-bold" for="inputEmail1">From Date</label>
                                        <input type="date" class="form-control border border-1 border-secondary px-2"
                                            id="inputEmail1" name="start_date">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-bold" for="inputEmail1">To Date</label>
                                        <input type="date" class="form-control border border-1 border-secondary px-2"
                                            id="inputEmail1" name="end_date">
                                    </div>
                                </div>
                                <div class="col-log-3">
                                    <button type="submit" class="btn btn-primary" style="margin-top: 32px;">Search</button>
                                    <a href="{{ route('admin.shan.reports.index') }}" class="btn btn-warning"
                                        style="margin-top: 32px;">Refresh</a>
                                </div>
                            </div>
                        </form>
                        <div class="card-body">
                            <table id="" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Agent Name</th>
                                        <th>PlayerId</th>
                                        <th>Player Name</th>
                                        <th>TotalStakeCount</th>
                                        <th>Total Bet</th>
                                        <th>Total Win</th>
                                        <th>Total Net Win</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($report as $data)
                                        <tr>
                                            <td>{{ $data->agent_name }}</td>
                                            <td>{{ $data->user_name }}</td>
                                            <td>{{ $data->player_name }}</td>
                                            <td>{{ $data->total_games }}</td>

                                            <td>{{ number_format($data->total_bet_amount, 2) }}</td>
                                            <td>{{ number_format($data->total_win_amount, 2) }}</td>
                                            <td>{{ number_format($data->total_net_win, 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.reports.player.details', $data->user_id) }}"
                                                    class="btn btn-primary">Detail</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total:</th>
                                        <th>{{ number_format($totalBet, 2) }}</th>
                                        <th>{{ number_format($totalWin, 2) }}</th>
                                        <th>{{ number_format($totalNetWin, 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>

                            </table>
                            {{ $report->links() }}
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@endsection
