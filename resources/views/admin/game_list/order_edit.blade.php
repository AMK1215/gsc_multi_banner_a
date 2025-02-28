@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="container my-auto mt-5">
            <div class="row">
                <div class="col-lg-10 col-md-2 col-12 mx-auto mt-5">
                    <div class="card z-index-0 fadeIn3 fadeInBottom">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                                <h4 class="text-white font-weight-bolder text-center mb-2">Edit Game List Order</h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.GameListOrderUpdate', $gameList->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="custom-form-group">
                                    <label for="image">Game Order NO</label>
                                    <input type="number" class="form-control" name="order" required>
                                </div>

                                <div class="custom-form-group mt-3">
                                    <button class="btn btn-success" type="submit">Update</button>
                                    <a href="{{ route('admin.gameLists.index') }}" class="btn btn-primary">Cancel</a>
                                </div>
                            </form>

                            <div class="custom-form-group mt-3">
                                <p>
                                    Order Number :: {{ $gameList->order }}
                                </p>
                                <p>
                                    {{ $gameList->game_name }}
                                </p>

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection