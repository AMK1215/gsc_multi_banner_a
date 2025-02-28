@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Detail Owner Hierarchy Information</li>
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
                        <a href="{{ route('admin.GetAllOwners') }}" class="btn btn-primary " style="width: 100px;"><i
                                class="fas fa-arrow-left text-white  mr-2"></i>Back</a>
                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Owner's Detail Hierarchy Information</h3>
                        </div>

                        <div class="card-body">

                            <table id="seniorTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>TableID</th>
                                        <th>Agent Name</th>
                                        <th>AgentID</th>
                                        <th>AgentPhone</th>
                                        <th>Wallet Balance</th>
                                        <th>BalanceDetail</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($owner->agents as $index => $agent)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $agent->id }}</td>


                                            <td>
                                                <a
                                                    href="{{ route('admin.AgentPlayerDetail', $agent->id) }}">{{ $agent->name }}</a>
                                            </td>


                                            <td>
                                                <a
                                                    href="{{ route('admin.AgentPlayerDetail', $agent->id) }}">{{ $agent->user_name }}</a>
                                            </td>
                                            <td>{{ $agent->phone }}</td>
                                            <td>{{ number_format($agent->wallet->balanceFloat ?? 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.AgentBalanceDetail', ['owner_id' => $owner->id, 'agent_id' => $agent->id]) }}"
                                                    class="btn btn-primary">AgentBalanceDetail</a>
                                            </td>

                                            <td>
                                                <a href="{{ route('admin.AgentPlayerDetail', $agent->id) }}"
                                                    class="btn btn-primary">View Players</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>


                        </div>

                    </div>

                    {{-- <div class="card" style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Owner's Detail Hierarchy Information</h3>
                        </div>
                        <div class="card-body">
                            <h4>Owner Information</h4>
                            <p><strong>Name:</strong> {{ $owner->name }}</p>
                            <p><strong>Phone:</strong> {{ $owner->phone }}</p>

                            <h4>Agents Summary</h4>
                            <p><strong>Total Agents:</strong> {{ $totalAgents }}</p>
                            <p><strong>Total Balance (All Agents):</strong> {{ number_format($totalBalance, 2) }}</p>

                            <!-- Display Specific Agent Balance if $agentId is provided -->
                            @if ($agentId)
                                <h4>Specific Agent Information</h4>
                                @if ($specificAgent)
                                    <p><strong>Agent Name:</strong> {{ $specificAgent->name }}</p>
                                    <p><strong>Agent ID:</strong> {{ $specificAgent->user_name }}</p>
                                    <p><strong>Agent Phone:</strong> {{ $specificAgent->phone }}</p>
                                    <p><strong>Wallet Balance:</strong> {{ number_format($specificAgentBalance, 2) }}</p>
                                @else
                                    <p>No agent found with ID {{ $agentId }}.</p>
                                @endif
                            @endif

                            <h4>Agents List</h4>
                            <table id="seniorTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>TableID</th>
                                        <th>Agent Name</th>
                                        <th>AgentID</th>
                                        <th>Agent Phone</th>
                                        <th>Wallet Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($owner->agents as $index => $agent)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $agent->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.AgentPlayerDetail', ['id' => $agent->id]) }}">
                                                    {{ $agent->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.AgentPlayerDetail', ['id' => $agent->id]) }}">
                                                    {{ $agent->user_name }}
                                                </a>
                                            </td>
                                            <td>{{ $agent->phone }}</td>
                                            <td>{{ number_format($agent->wallet->balance ?? 0, 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.OwnerAgentDetail', ['id' => $owner->id, 'agentId' => $agent->id]) }}"
                                                    class="btn btn-primary">
                                                    View Balance
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> --}}

                    {{-- <div class="card" style="border-radius: 20px;">
                        <div class="card-header">
                            <h3>Owner's Detail Hierarchy Information</h3>
                        </div>
                        <div class="card-body">
                            <h4>Owner Information</h4>
                            <p><strong>Name:</strong> {{ $owner->name }}</p>
                            <p><strong>Phone:</strong> {{ $owner->phone }}</p>

                            <h4>Agents Summary</h4>
                            <p><strong>Total Agents:</strong> {{ $totalAgents }}</p>
                            <p><strong>Total Balance (All Agents):</strong> {{ number_format($totalBalance, 2) }}</p>

                            <!-- Display Specific Agent Balance if $agentId is provided -->
                            @if ($agentId)
                                <h4>Specific Agent Information</h4>
                                @if ($specificAgent)
                                    <p><strong>Agent Name:</strong> {{ $specificAgent->name }}</p>
                                    <p><strong>Agent ID:</strong> {{ $specificAgent->user_name }}</p>
                                    <p><strong>Agent Phone:</strong> {{ $specificAgent->phone }}</p>
                                    <p><strong>Wallet Balance:</strong> {{ number_format($specificAgentBalance, 2) }}</p>
                                @else
                                    <p>No agent found with ID {{ $agentId }}.</p>
                                @endif
                            @endif

                            <h4>Agents List</h4>
                            <table id="seniorTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>TableID</th>
                                        <th>Agent Name</th>
                                        <th>AgentID</th>
                                        <th>Agent Phone</th>
                                        <th>Wallet Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($owner->agents as $index => $agent)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $agent->id }}</td>
                                            <td>
                                                <a href="{{ route('admin.AgentPlayerDetail', ['id' => $agent->id]) }}">
                                                    {{ $agent->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.AgentPlayerDetail', ['id' => $agent->id]) }}">
                                                    {{ $agent->user_name }}
                                                </a>
                                            </td>
                                            <td>{{ $agent->phone }}</td>
                                            <td>{{ number_format($agent->wallet->balance ?? 0, 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.OwnerAgentDetail', ['ownerId' => $owner->id]) }}?agentId={{ $agent->id }}"
                                                    class="btn btn-primary">
                                                    View Balance
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div> --}}

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
