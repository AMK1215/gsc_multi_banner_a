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
                                class="fas fa-plus text-white  mr-2"></i>Back</a>
                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>W/L Report Detail</h3>
                        </div>
                        <div class="card-body">
                            <table id="mytable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>PlayerName</th>
                                        <th>ProviderName</th>
                                        <th>Game Name</th>
                                        <th>Total Bet</th>
                                        <th>Win Amount</th>
                                        <th>Net Win</th>
                                        <th>His-1</th>
                                        <th>His-2</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($details as $detail)
                                        <tr>
                                            <td>{{ $detail->player_name }}</td>
                                            <td>{{ $detail->game_provide_name }}</td>
                                            <td>{{ $detail->game_name }}</td>
                                            <td>{{ number_format($detail->total_bet_amount, 2) }}</td>
                                            <td>{{ number_format($detail->win_amount, 2) }}</td>
                                            <td>{{ number_format($detail->net_win, 2) }}</td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="getTransactionDetails('{{ $detail->round_id }}')"
                                                    style="color: blueviolet; text-decoration: underline;">
                                                    History1
                                                </a>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0);"
                                                    onclick="getTransactionDetails('{{ $detail->result_id }}')"
                                                    style="color: blueviolet; text-decoration: underline;">
                                                    History2
                                                </a>
                                            </td>
                                            <td>{{ $detail->tran_date_time }}</td>
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
                            {{-- {{ $details->links() }} --}}
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
