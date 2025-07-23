@extends('Layouts.app')
@section('title', 'Sizes')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <h3>Sizes</h3>
                                <button type="button" class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal"
                                    data-bs-target="#addData">Add Data</button>
                            </div>
                        </div>

                        {{-- modal --}}
                        <div id="addData" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addDataTitle"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('sizes.store') }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addDataTitle">Add Size</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="label" required>
                                                    <span class="input-group-text">gr</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Add</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="dt-responsive">
                                <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th style="width: 10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($sizes as $item)
                                            <tr>
                                                <td>{{ $item->label }} gr</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-warning btn-sm ms-auto"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editData{{ $item->id }}"><i
                                                            class="ti ti-edit"></i></button>
                                                </td>
                                            </tr>

                                            {{-- modal edit --}}
                                            <div id="editData{{ $item->id }}" class="modal fade" tabindex="-1"
                                                role="dialog" aria-labelledby="editData{{ $item->id }}Title"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('sizes.update', $item->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editData{{ $item->id }}Title">Edit
                                                                    Size</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="size" class="form-label">Size</label>
                                                                    <input type="text" class="form-control"
                                                                        id="size" name="label"
                                                                        value="{{ $item->label }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                                <button type="submit"
                                                                    class="btn btn-primary">Update</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
