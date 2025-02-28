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
                            <h3>Senior Result Report</h3>
                        </div>
                        <div class="card-body">
                            <div class="mt-2">
                                <form action="{{ route('admin.senior.deleteResults') }}" method="POST">
                                    @csrf
                                    <label for="start_date">Start Date:</label>
                                    <input type="date" id="start_date" name="start_date" required>

                                    <label for="end_date">End Date:</label>
                                    <input type="date" id="end_date" name="end_date" required>

                                    <button type="submit">Delete Results</button>
                                </form>

                            </div>
                            <table id="seniorTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Player Name</th>
                                        <th>PlayerID</th>
                                        <th>Game Provide Name</th>
                                        <th>Game Name</th>
                                        <th>Total Bet Amount</th>
                                        <th>Win Amount</th>
                                        <th>Net Win</th>
                                        <th>Date</th>
                                        <th>Actioin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($results as $index => $result)
                                        <tr>
                                            <td>{{ $results->firstItem() + $index }}</td> <!-- Adjust for pagination -->
                                            <td>{{ $result->player_name }}</td>
                                            <td>{{ $result->player_id }}</td>
                                            <td>{{ $result->game_provide_name }}</td>
                                            <td>{{ $result->game_name }}</td>
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

@section('script')
    <!-- jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#seniorTable').DataTable();
        });
    </script>

    <script>
        if (document.getElementById('seniorTable')) {
            const dataTableSearch = new simpleDatatables.DataTable("#seniorTable", {
                searchable: true,
                fixedHeight: false,
                perPage: 200
            });

            document.querySelectorAll(".export").forEach(function(el) {
                el.addEventListener("click", function(e) {
                    var type = el.dataset.type;

                    var data = {
                        type: type,
                        filename: "material-" + type,
                    };

                    if (type === "csv") {
                        data.columnDelimiter = "|";
                    }

                    dataTableSearch.export(data);
                });
            });
        };
    </script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endsection
