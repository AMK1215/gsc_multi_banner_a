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
                            <h3>Agent's Balance Detail</h3>
                        </div>

                        <div class="card-body">
                            <p><strong>Agent ID:</strong> {{ $agentData['id'] }}</p>
                            <p><strong>Name:</strong> {{ $agentData['agent_name'] }}</p>
                            <p><strong>Username:</strong> {{ $agentData['agent_username'] }}</p>
                            <p><strong>Phone:</strong> {{ $agentData['agent_phone'] }}</p>
                            <p><strong>Wallet Balance:</strong> ${{ number_format($agentData['wallet_balance'], 2) }}</p>
                        </div>
                    </div>
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
