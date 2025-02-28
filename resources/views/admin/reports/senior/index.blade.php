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
                    <a href="{{ route('home') }}" class="btn btn-primary " style="width: 100px;"><i
                            class="fas fa-arrow-left text-white  mr-2"></i>Back</a>
                </div>
                <div class="card " style="border-radius: 20px;">
                    <div class="card-header">
                        <h3>Senior Winlose Total Report</h3>
                    </div>
                    <form role="form" class="text-start" action="{{ route('admin.reports.senior') }}" method="GET">
                        <div class="row ml-5">
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-bold" for="inputEmail1">From Date</label>
                                    <input type="date" class="form-control border border-1 border-secondary px-2"
                                        name="start_date" value="{{request()->start_date }}">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-bold" for="inputEmail1">To Date</label>
                                    <input type="date" class="form-control border border-1 border-secondary px-2"
                                        id="" name="end_date" value="{{request()->end_date }}">
                                </div>
                            </div>
                            <div class="col-log-3">
                                <button type="submit" class="btn btn-primary" style="margin-top: 32px;">Search</button>
                                <a href="{{ route('admin.reports.senior') }}" class="btn btn-warning" style="margin-top: 32px;">Refresh</a>
                            </div>
                        </div>
                    </form>
                    <div class="card-body">
                        <table id="mytable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Owner</th>
                                    <th>Total Bets</th>
                                    <th>Total Wins</th>
                                    <th>Total Net</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                <tr>
                                    <td>{{ $row['admin_name'] }}</td>
                                    <td>{{ $row['total_bets'] }}</td>
                                    <td>{{ $row['total_wins'] }}</td>
                                    <td>{{ $row['total_net'] }}</td>
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