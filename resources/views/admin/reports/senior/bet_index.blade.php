@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">W/L Result Report</li>
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
                                class="fas fa-plus text-white  mr-2"></i>Back</a>
                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Senior Bet Report</h3>
                        </div>
                        <div class="card-body">
                            <div class="mt-2">
                                <form action="{{ route('admin.senior.deleteBets') }}" method="POST">
                                    @csrf
                                    <label for="start_date">Start Date:</label>
                                    <input type="date" id="start_date" name="start_date" required>

                                    <label for="end_date">End Date:</label>
                                    <input type="date" id="end_date" name="end_date" required>

                                    <button type="submit">Delete Bet</button>
                                </form>

                            </div>
                            <table id="mytable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>PlayerID</th>
                                        <th>Game Provide Name</th>
                                        <th>Game Name</th>
                                        <th>RoundID</th>
                                        <th>Bet Amount</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $index => $result)
                                        <tr>
                                            <td>{{ $results->firstItem() + $index }}</td> <!-- Adjust for pagination -->
                                            <td>{{ $result->player_id }}</td>
                                            <td>{{ $result->game_provide_name }}</td>
                                            <td>{{ $result->game_name }}</td>
                                            <td>{{ $result->round_id }}</td>
                                            <td>{{ number_format($result->bet_amount, 2) }}</td>
                                            <td>{{ $result->tran_date_time }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $results->links() }}
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@endsection
