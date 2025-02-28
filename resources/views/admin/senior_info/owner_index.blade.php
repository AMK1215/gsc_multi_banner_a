@extends('layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item active">Owner Hierarchy Information</li>
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
                            <h3>Owner Hierarchy Information</h3>
                        </div>

                        <div class="card-body">

                            <table id="seniorTable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>OwnerID</th>
                                        <th>OwnerName</th>
                                        <th>Phone</th>
                                        <th>TotalBalance</th>
                                        <th>Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($owners as $index => $owner)
                                        <tr>
                                            <td>{{ $owners->firstItem() + $index }}</td> <!-- Adjust for pagination -->
                                            <td>{{ $owner['owner_id'] }}</td>
                                            <td>{{ $owner['owner_name'] }}</td>
                                            <td>{{ $owner['owner_phone'] }}</td>
                                            <td>{{ number_format($owner['total_balance'], 2) }}</td>
                                            <td>
                                                <a href="{{ route('admin.OwnerAgentDetail', $owner['id']) }}"
                                                    class="btn btn-primary">Detail</a>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                            <!-- Pagination Links -->
                            <div class="d-flex justify-content-center">
                                {{ $owners->links() }}
                                {{-- {{ $groupedData->links('pagination::bootstrap-5') }} --}}
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
