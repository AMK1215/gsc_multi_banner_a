@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">W/L Report V3</li>
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
                            <h3>Report V3</h3>
                        </div>

                        <div class="card-body">
                            <table id="" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Player Name</th>
                                        <th>Game Provider</th>
                                        <th>Game Name</th>
                                        {{-- <th>Operator ID</th> --}}
                                        {{-- <th>Request Date Time</th> --}}
                                        <th>Player ID</th>
                                        {{-- <th>Currency</th> --}}
                                        {{-- <th>Round ID</th> --}}
                                        <th>ResultID</th>
                                        <th>GameCode</th>
                                        <th>TotalBet</th>
                                        <th>WinAmount</th>
                                        <th>NetWin</th>
                                        <th>DateTime</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($results as $result)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $result->player_name }}</td>
                                            <td>{{ $result->game_provide_name }}</td>
                                            <td>{{ $result->game_name }}</td>
                                            {{-- <td>{{ $result->operator_id }}</td> --}}
                                            {{-- <td>{{ $result->request_date_time }}</td> --}}
                                            <td>{{ $result->player_id }}</td>
                                            {{-- <td>{{ $result->currency }}</td> --}}
                                            {{-- <td>{{ $result->round_id }}</td> --}}
                                            <td>{{ $result->result_id }}</td>
                                            <td>{{ $result->game_code }}</td>
                                            <td>{{ number_format($result->total_bet_amount, 2) }}</td>
                                            <td>{{ number_format($result->win_amount, 2) }}</td>
                                            <td>{{ number_format($result->net_win, 2) }}</td>
                                            <td>{{ $result->tran_date_time }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="15" class="text-center">No results found for this user.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@endsection
