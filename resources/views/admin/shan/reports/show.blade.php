@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">ShanWinLose Detail</li>
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
                        {{-- <a href="{{ route('admin.player.create') }}" class="btn btn-success " style="width: 100px;"><i
                                class="fas fa-plus text-white  mr-2"></i>Back</a> --}}
                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Shan Win / Lose Detail</h3>
                        </div>
                        <div class="card-body">
                            <table id="mytable" class="table table-bordered table-hover">
                                {{-- <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Game Type ID</th>
                                        <th>Rate</th>
                                        <th>Transaction Amount</th>
                                        <th>Bet Amount</th>
                                        <th>Valid Amount</th>
                                        <th>Status</th>
                                        <th>Final Turn</th>
                                        <th>Banker</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ $transaction->game_type_id }}</td>
                                            <td>{{ $transaction->rate }}</td>
                                            <td>{{ $transaction->transaction_amount }}</td>
                                            <td>{{ $transaction->bet_amount }}</td>
                                            <td>{{ $transaction->valid_amount }}</td>
                                            <td>{{ $transaction->status }}</td>
                                            <td>{{ $transaction->final_turn }}</td>
                                            <td>{{ $transaction->banker }}</td>
                                            <td>{{ $transaction->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody> --}}

                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>PlayerID</th>
                                        <th>Game Type ID</th>
                                        <th>Bet Amount</th>
                                        <th>Amount Changed</th>
                                        <th>Win/Lose Status</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userTransactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ $playerName }}</td>
                                            <td>{{ $transaction->game_type_id == 1 ? 'Shan' : 'Slot' }}</td>
                                            <td>{{ number_format($transaction->bet_amount, 2) }}</td>
                                            <td>{{ number_format($transaction->transaction_amount, 2) }}</td>
                                            <td>{{ $transaction->win_lose_status == 1 ? 'Win' : 'Lose' }}</td>
                                            <td>{{ $transaction->created_at }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total:</th>
                                        <th>{{ number_format($totalBet, 2) }}</th>
                                        <th>{{ number_format($totalWin, 2) }}</th>
                                        <th>{{ number_format($totalLose, 2) }}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
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
