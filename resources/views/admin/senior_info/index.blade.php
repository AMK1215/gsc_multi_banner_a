@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Senior Hierarchy Information</li>
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
                            <h3>Senior Hierarchy Information</h3>
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <h5>Total Balances</h5>
                                <ul>
                                    <li><strong>Total Owner Balance:</strong> {{ number_format($totalOwnerBalance, 2) }}
                                    </li>
                                    <li><strong>Total Agent Balance:</strong> {{ number_format($totalAgentBalance, 2) }}
                                    </li>
                                    <li><strong>Total Player Balance:</strong> {{ number_format($totalPlayerBalance, 2) }}
                                    </li>
                                </ul>
                            </div>
                            <table id="seniorTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Owner Name</th>
                                        <th>OwnerBalance</th>
                                        {{-- <th>AgentTotalBalance</th> --}}
                                        <th>Agent Name</th>
                                        <th>AgentBalance</th>
                                        <th>Player Name</th>
                                        <th>PlayerBalance</th>
                                    </tr>
                                </thead>
                                {{-- <tbody>
                                    @php $count = 1; @endphp
                                    @foreach ($senior->children as $owner)
                                        <!-- Owners -->
                                        @foreach ($owner->children as $agent)
                                            <!-- Agents -->
                                            @foreach ($agent->children as $player)
                                                <!-- Players -->
                                                <tr>
                                                    <td>{{ $count++ }}</td>
                                                    <td>{{ $owner->name }}</td>
                                                    <td>{{ $owner->wallet->balanceFloat ?? '0.00' }}</td>
                                                    <td>{{ $agent->name }}</td>
                                                    <td>{{ $agent->wallet->balanceFloat ?? '0.00' }}</td>
                                                    <td>{{ $player->name }}</td>
                                                    <td>{{ $player->wallet->balanceFloat ?? '0.00' }}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody> --}}
                                {{-- <tbody>
                                    @php $count = 1; @endphp
                                    @foreach ($groupedData as $owner)
                                        <!-- Owners -->
                                        @foreach ($owner['agents'] as $agentsGroup)
                                            @foreach ($agentsGroup as $agent)
                                                @foreach ($agent['players'] as $player)
                                                    <tr>
                                                        <td>{{ $count++ }}</td>
                                                        <td>{{ $owner['owner_name'] }}</td>
                                                        <td>{{ $owner['owner_balance'] }}</td>
                                                        <td>{{ $agent['agent_name'] }}</td>
                                                        <td>{{ $agent['agent_balance'] }}</td>
                                                        <td>{{ $player['player_name'] }}</td>
                                                        <td>{{ $player['player_balance'] }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                </tbody> --}}
                                <tbody>
                                    @php $count = $groupedData->firstItem(); @endphp
                                    @foreach ($groupedData as $row)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $row['owner_name'] }}</td>
                                            <td>{{ $row['owner_balance'] }}</td>
                                            {{-- <td>{{ $row['owner_agent_total_balance'] }}</td> --}}
                                            <td>{{ $row['agent_name'] }}</td>
                                            <td>{{ $row['agent_balance'] }}</td>
                                            <td>{{ $row['player_name'] }}</td>
                                            <td>{{ $row['player_balance'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $groupedData->links('pagination::bootstrap-5') }}
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
@endsection
