@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">W/L Report</li>
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
                    {{-- <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin.player.create') }}" class="btn btn-success " style="width: 100px;"><i
                                class="fas fa-plus text-white  mr-2"></i>Create</a>
                    </div> --}}
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Daily W/L Report</h3>
                        </div>
                        <div class="card-body">
                            <table id="mytable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Currency Code</th>
                                        <th>Turnover</th>
                                        <th>Valid Turnover</th>
                                        <th>Payout</th>
                                        <th>Win/Lose</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($summaries as $summary)
                                        <tr>
                                            <td>{{ $summary->summary_date }}</td>
                                            <td>{{ $summary->currency_code }}</td>
                                            <td>{{ number_format($summary->turnover, 2) }}</td>
                                            <td>{{ number_format($summary->valid_turnover, 2) }}</td>
                                            <td>{{ number_format($summary->payout, 2) }}</td>
                                            <td>{{ number_format($summary->win_lose, 2) }}</td>
                                        </tr>
                                    @endforeach
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

    {{-- <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('home') }}" class="btn btn-primary" style="width: 100px;">
                            <i class="fas fa-plus text-white mr-2"></i>Back
                        </a>
                    </div>
                    <div class="card" style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Get Day Summary</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('daily_summary') }}" method="POST">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <label for="OperatorId">OperatorId:</label>
                                        <input type="text" class="form-control" id="OperatorId" name="OperatorId"
                                            value="delightMMK" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="RequestDateTime">RequestDateTime:</label>
                                        <input type="text" class="form-control" id="RequestDateTime"
                                            name="RequestDateTime" value="2024-11-11 12:12:12" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="Signature">Signature:</label>
                                        <input type="text" class="form-control" id="Signature" name="Signature"
                                            value="b7acad4a4fb0de124cc64106ab6d0eea" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="Date">Date:</label>
                                        <input type="text" class="form-control" id="Date" name="Date"
                                            value="2024-11-14T00:00:00Z" required>
                                    </div>
                                </div>

                                <div class="form-row mt-4">
                                    <div class="col text-center">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>

                            @if (session('response'))
                                <div class="mt-4">
                                    <h4>Response Data</h4>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Currency Code</th>
                                                <th>Turnover</th>
                                                <th>Valid Turnover</th>
                                                <th>Payout</th>
                                                <th>Win/Lose</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (session('response')['Trans'] as $transaction)
                                                <tr>
                                                    <td>{{ $transaction['CurrencyCode'] }}</td>
                                                    <td>{{ $transaction['Turnover'] }}</td>
                                                    <td>{{ $transaction['ValidTurnover'] }}</td>
                                                    <td>{{ $transaction['Payout'] }}</td>
                                                    <td>{{ $transaction['WinLose'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    {{-- <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('home') }}" class="btn btn-primary" style="width: 100px;">
                            <i class="fas fa-plus text-white mr-2"></i>Back
                        </a>
                    </div>
                    <div class="card" style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Get Day Summary</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('daily_summary') }}" method="POST">
                                @csrf
                                <div class="form-row">
                                    <div class="col-md-3">
                                        <label for="OperatorId">OperatorId:</label>
                                        <input type="text" class="form-control" id="OperatorId" name="OperatorId"
                                            value="delightMMK" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="RequestDateTime">RequestDateTime:</label>
                                        <input type="text" class="form-control" id="RequestDateTime"
                                            name="RequestDateTime" value="2024-11-11 12:12:12" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="Signature">Signature:</label>
                                        <input type="text" class="form-control" id="Signature" name="Signature"
                                            value="b7acad4a4fb0de124cc64106ab6d0eea" required>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="Date">Date:</label>
                                        <input type="text" class="form-control" id="Date" name="Date"
                                            value="2024-11-14T00:00:00Z" required>
                                    </div>
                                </div>

                                <div class="form-row mt-4">
                                    <div class="col text-center">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>

                            @if (session('response'))
                                <div class="mt-4">
                                    <h2>Response</h2>
                                    <pre>{{ json_encode(session('response'), JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
@endsection
