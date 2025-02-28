@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Player List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Player</li>
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
                    <div class="card">
                        <div class="card-body">
                            @foreach ($agents as $agent)
                                <h4>Agent: {{ $agent->name }}</h4>
                                <table id="mytable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Player Name</th>
                                            <th>PlayerId</th>
                                            <th>AgentName</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th>Balance</th>
                                            <th>CreatedAt</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($agent->createdAgents as $createdAgent)
                                            @foreach ($createdAgent->players as $player)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $player->name }}</td>
                                                    <td>{{ $player->user_name }}</td>
                                                    <td>{{$player->parent->name}}</td>
                                                    <td>{{ $player->phone }}</td>
                                                    <td>
                                                        @if ($player->status == 1)
                                                            <p>Active</p>
                                                        @else
                                                            <p>Inactive</p>
                                                        @endif
                                                    </td>
                                                    <td>{{ number_format($player->balanceFloat) }}</td>
                                                    <td>{{ $player->created_at->setTimezone('Asia/Yangon')->format('d-m-Y H:i:s') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                    {{-- <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name</th>
                                        <th>User Phone</th>
                                        <th>Creator</th>
                                        <th>Balance</th>
                                        <th>CreatedAt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->agent ? $user->agent->name : 'N/A' }}</td>
                                            <td>{{ number_format($user->balanceFloat) }}</td>
                                            <td>{{ $user->created_at->setTimezone('Asia/Yangon')->format('d-m-Y H:i:s') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody> --}}

                                </table>
                            @endforeach

                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
@endsection
