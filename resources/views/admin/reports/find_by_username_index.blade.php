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

                        <form role="form" class="text-start" action="{{ route('admin.results.search') }}" method="POST">
                            @csrf
                            <div class="row ml-5">
                                <div class="col-lg-3">
                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-bold" for="inputEmail1">PlayerID</label>
                                        <input type="text" name="user_name" id="user_name"
                                            class="form-control border border-1 border-secondary px-2"
                                            placeholder="Enter PlayerID (e.g - Player0001)">
                                    </div>
                                </div>

                                <div class="col-log-3">
                                    <button type="submit" class="btn btn-primary" style="margin-top: 32px;">Search</button>
                                </div>
                            </div>
                        </form>

                        <div class="card-body">
                            @if (!empty($results))
                                <table id="" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Player Name</th>
                                            <th>Game Provider</th>
                                            <th>Game Name</th>

                                            <th>Player ID</th>

                                            <th>Result ID</th>
                                            <th>Game Code</th>
                                            <th>Total Bet</th>
                                            <th>Win Amount</th>
                                            <th>Net Win</th>
                                            <th>TransactionDateTime</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($results as $result)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $result->player_name }}</td>
                                                <td>{{ $result->game_provide_name }}</td>
                                                <td>{{ $result->game_name }}</td>

                                                <td>{{ $result->player_id }}</td>

                                                <td>{{ $result->result_id }}</td>
                                                <td>{{ $result->game_code }}</td>
                                                <td>{{ number_format($result->total_bet_amount, 2) }}</td>
                                                <td>{{ number_format($result->win_amount, 2) }}</td>
                                                <td>{{ number_format($result->net_win, 2) }}</td>
                                                <td>{{ $result->tran_date_time }}</td>
                                                <td>
                                                    <form action="{{ route('admin.results.delete', $result->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this result?')">Delete</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- <form id="deleteMultipleForm" action="{{ route('admin.results.deleteMultiple') }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th>#</th>
                                                <th>Player Name</th>
                                                <th>Game Provider</th>
                                                <th>Game Name</th>
                                                <th>Player ID</th>
                                                <th>Result ID</th>
                                                <th>Game Code</th>
                                                <th>Total Bet</th>
                                                <th>Win Amount</th>
                                                <th>Net Win</th>
                                                <th>Transaction Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($results as $result)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="ids[]" value="{{ $result->id }}"
                                                            class="checkbox">
                                                    </td>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $result->player_name }}</td>
                                                    <td>{{ $result->game_provide_name }}</td>
                                                    <td>{{ $result->game_name }}</td>
                                                    <td>{{ $result->player_id }}</td>
                                                    <td>{{ $result->result_id }}</td>
                                                    <td>{{ $result->game_code }}</td>
                                                    <td>{{ number_format($result->total_bet_amount, 2) }}</td>
                                                    <td>{{ number_format($result->win_amount, 2) }}</td>
                                                    <td>{{ number_format($result->net_win, 2) }}</td>
                                                    <td>{{ $result->tran_date_time }}</td>
                                                    <td>
                                                        <form action="{{ route('admin.results.delete', $result->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger"
                                                                onclick="return confirm('Are you sure you want to delete this result?')">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>


                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete selected results?')">
                                        Delete Selected
                                    </button>
                                </form> --}}
                            @endif
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        document.getElementById("selectAll").addEventListener("click", function() {
            let checkboxes = document.querySelectorAll(".checkbox");
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });
    </script>
@endsection
