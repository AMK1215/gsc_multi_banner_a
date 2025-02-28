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




                            <div class="mt-4">

                                <form action="{{ route('admin.gamelistnew.store') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <!-- Game ID -->
                                    <div class="form-group">
                                        <label for="game_id">Game ID</label>
                                        <input type="number" name="game_id" id="game_id" class="form-control"
                                            value="{{ old('game_id') }}" required>
                                        @error('game_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- Game Type -->
                                    <div class="form-group">
                                        <label for="game_type_id">Game Type ID</label>
                                        <input type="text" name="game_type_id" id="game_type_id" class="form-control"
                                            {{-- <select name="game_type_id" id="game_type_id" class="form-control" required>
                                            <option value="">Select Game Type</option>
                                            @foreach ($gameTypes as $gameType)
                                                <option value="{{ $gameType->id }}"
                                                    {{ old('game_type_id') == $gameType->id ? 'selected' : '' }}>
                                                    {{ $gameType->name }}
                                                </option>
                                            @endforeach
                                        </select> --}}
                                            @error('game_type_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                            </div>

                                        <!-- Product -->
                                        <div class="form-group">
                                            <label for="product_id">Product</label>
                                            <input type="text" name="product_id" id="product_id" class="form-control"
                                                value="{{ old('product_id') }}" required>
                                            {{-- <select name="product_id" id="product_id" class="form-control" required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select> --}}
                                            @error('product_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <!-- Status -->
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                            @error('status')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <!-- Game Code -->
                                        <div class="form-group">
                                            <label for="game_code">Game Code</label>
                                            <input type="text" name="game_code" id="game_code" class="form-control"
                                                value="{{ old('game_code') }}" required>
                                            @error('game_code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <!-- Game Name -->
                                        <div class="form-group">
                                            <label for="game_name">Game Name</label>
                                            <input type="text" name="game_name" id="game_name" class="form-control"
                                                value="{{ old('game_name') }}" required>
                                            @error('game_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="game_type">Game Type</label>
                                            <input type="number" name="game_type" id="game_type" class="form-control"
                                                value="{{ old('game_type', 0) }}" required>
                                            @error('game_type')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="game_type">Game Order</label>
                                            <input type="number" name="order" id="order" class="form-control"
                                                value="{{ old('order', 0) }}" required>
                                            @error('order')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>



                                        <!-- Method -->
                                        <div class="form-group">
                                            <label for="method">Method</label>
                                            <input type="text" name="method" id="method" class="form-control"
                                                value="{{ old('method') }}" required>
                                            @error('method')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="is_h5_support">IsH5Support</label>
                                            <input type="text" name="is_h5_support" id="is_h5_support"
                                                class="form-control" value="1" required>
                                            @error('method')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="has_demo">HasDemo</label>
                                            <input type="text" name="has_demo" id="has_demo" class="form-control"
                                                value="0" required>
                                            @error('method')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>


                                        <!-- Game Provide Code -->
                                        <div class="form-group">
                                            <label for="game_provide_code">Game Provide Code</label>
                                            <input type="text" name="game_provide_code" id="game_provide_code"
                                                class="form-control" value="{{ old('game_provide_code') }}" required>
                                            @error('game_provide_code')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <!-- Game Provide Name -->
                                        <div class="form-group">
                                            <label for="game_provide_name">Game Provide Name</label>
                                            <input type="text" name="game_provide_name" id="game_provide_name"
                                                class="form-control" value="{{ old('game_provide_name') }}" required>
                                            @error('game_provide_name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <!-- Sequence -->
                                        <div class="form-group">
                                            <label for="sequence">Sequence</label>
                                            <input type="number" name="sequence" id="sequence" class="form-control"
                                                value="{{ old('sequence') }}" required>
                                            @error('sequence')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <!-- Image Upload -->
                                        <div class="form-group">
                                            <label for="image_url">Game Icon</label>
                                            <input type="text" name="image_url" id="image" class="form-control">
                                            @error('image_url')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success">Create Game</button>
                                        </div>
                                </form>



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
