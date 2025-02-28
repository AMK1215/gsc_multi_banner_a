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
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ url('admin/slot/report') }}" class="btn btn-primary " style="width: 100px;"><i
                                class="fas fa-plus text-white  mr-2"></i>Go To W/L Report V1</a>
                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>{{ $player->name }} - W/L Report Detail </h3>
                        </div>
                        <div class="card-body">
                            <table id="mytable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>User Name</th>
                                        <th>Player Name</th>
                                        <th>Game Name</th>
                                        <th>Provider</th>
                                        <th>History1</th>
                                        <th>History2</th>
                                        <th>DateTime</th>
                                        <th>Currency</th>
                                        <th>Bet</th>
                                        <th>Win</th>
                                        <th>NetWin</th>
                                        {{-- <th>RoundID</th> --}}
                                        <th>TransactionDateTime</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($details as $detail)
                                        <tr>
                                            <td>{{ $detail->user_name }}</td>
                                            <td>{{ $detail->player_name }}</td>
                                            <td>{{ $detail->game_name }}</td>
                                            <td>{{ $detail->game_provide_name }}</td>
                                            {{-- <td><a href="https://delightmyanmar99.pro/api/transaction-details/{{ $detail->round_id }}"
                                                    target="_blank"
                                                    style="color: blueviolet; text-decoration: underline;">{{ $detail->round_id }}</a>
                                            </td> --}}

                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="getTransactionDetails('{{ $detail->round_id }}')"
                                                    style="color: blueviolet; text-decoration: underline;">
                                                    {{ $detail->round_id }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="getTransactionDetails('{{ $detail->result_id }}')"
                                                    style="color: blueviolet; text-decoration: underline;">
                                                    {{ $detail->result_id }}
                                                </a>
                                            </td>

                                            {{-- <td>
                                                <a href="javascript:void(0);"
                                                    onclick="getTransactionDetails('{{ $detail->round_id ? $detail->result_id : null }}')"
                                                    style="color: blueviolet; text-decoration: underline;">
                                                    {{ $detail->round_id }}
                                                </a>
                                            </td> --}}

                                            <td>{{ $detail->request_date_time }}</td>
                                            <td>{{ $detail->currency }}</td>
                                            <td>{{ number_format($detail->total_bet_amount, 2) }}</td>
                                            <td>{{ number_format($detail->win_amount, 2) }}</td>
                                            <td>{{ number_format($detail->net_win, 2) }}</td>
                                            {{-- <td>{{ $detail->result_id }}</td> --}}
                                            <td>{{ $detail->tran_date_time }}</td>
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
@endsection

@section('script')
    <script>
        // function getTransactionDetails(tranId) {
        //     fetch(`/api/transaction-details/${tranId}`, {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //                 'X-CSRF-TOKEN': '{{ csrf_token() }}' // Only if CSRF protection is enabled
        //             },
        //             body: JSON.stringify({
        //                 tranId: tranId
        //             })
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             // Handle the response data here, e.g., display in a modal or alert
        //             console.log(data);
        //             alert(JSON.stringify(data));
        //         })
        //         .catch(error => {
        //             console.error('Error:', error);
        //             alert('Failed to get transaction details');
        //         });
        // }

        function getTransactionDetails(tranId) {
            // Make the POST request to fetch transaction details
            fetch(`/api/transaction-details/${tranId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Only if CSRF protection is enabled
                    },
                    body: JSON.stringify({
                        tranId: tranId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    // Assuming the response contains a URL or other relevant data to display
                    if (data.Url) {
                        // Redirect to the provided URL in the response data (open in new tab)
                        window.open(data.Url, '_blank');
                    } else {
                        // If there's no URL, open a new page with data passed as URL parameters
                        const newPageUrl =
                            `/transaction-details-page?tranId=${tranId}&details=${encodeURIComponent(JSON.stringify(data))}`;
                        window.open(newPageUrl, '_blank');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to get transaction details');
                });
        }
    </script>
@endsection
