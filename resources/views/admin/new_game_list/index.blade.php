@extends('layouts.master')
@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">
@endsection
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">GSC GameList</li>
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

                    </div>
                    <div class="card " style="border-radius: 20px;">
                        <div class="card-header">
                            <h5 class="mb-0">Game List Dashboards
                                <span>
                                    <a href="{{ route('admin.gamelistnew.create') }}" class="btn btn-primary">Add New
                                        Game</a>
                                </span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.gameLists.search') }}" method="GET" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <input type="text" name="game_name" class="form-control"
                                            placeholder="Search by Game Name" value="{{ request('game_name') }}">
                                    </div>
                                    {{-- <div class="col-md-2">
                                        <input type="text" name="game_code" class="form-control"
                                            placeholder="Search by Game Code" value="{{ request('game_code') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <select name="game_type" class="form-control">
                                            <option value="">All Game Types</option>
                                            <option value="1" {{ request('game_type') == 1 ? 'selected' : '' }}>Type 1
                                            </option>
                                            <option value="2" {{ request('game_type') == 2 ? 'selected' : '' }}>Type 2
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="1" {{ request('status') == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ request('status') == 0 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="hot_status" class="form-control">
                                            <option value="">Hot Status</option>
                                            <option value="1" {{ request('hot_status') == 1 ? 'selected' : '' }}>Hot
                                            </option>
                                            <option value="0" {{ request('hot_status') == 0 ? 'selected' : '' }}>Not
                                                Hot</option>
                                        </select>
                                    </div> --}}
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-primary btn-block">Search</button>
                                    </div>
                                </div>
                            </form>



                            @can('admin_access')
                                <div class="mt-4">

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Game ID</th>
                                                <th>Game Name</th>
                                                <th>Game Code</th>
                                                <th>Game Type</th>
                                                <th>Product</th>
                                                <th>Status</th>
                                                <th>Image</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($gameLists as $game)
                                                <tr>
                                                    <td>{{ $game->game_id }}</td>
                                                    <td>{{ $game->game_name }}</td>
                                                    <td>{{ $game->game_code }}</td>
                                                    <td>{{ $game->gameType->name ?? 'N/A' }}</td>
                                                    <td>{{ $game->product->name ?? 'N/A' }}</td>
                                                    <td>{{ $game->status ? 'Active' : 'Inactive' }}</td>
                                                    <td>
                                                        <img src="{{ asset('storage/' . $game->image_url) }}"
                                                            alt="{{ $game->game_name }}" width="50">
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.gamelistnew.edit', $game) }}"
                                                            class="btn btn-sm btn-warning">Edit</a>
                                                        <form action="{{ route('admin.gamelistnew.destroy', $game) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endcan


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
    {{-- <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script> --}}
@endsection
