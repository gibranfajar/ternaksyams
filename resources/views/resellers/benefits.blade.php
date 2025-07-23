@extends('Layouts.app')
@section('title', 'Reseller Benefits')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-between align-items-start flex-wrap">
                                <h3 class="mb-1">Reseller Benefits</h3>

                                <button type="button" class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal"
                                    data-bs-target="#addData">Add Data</button>
                            </div>
                        </div>

                        {{-- modal --}}
                        <div id="addData" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addDataTitle"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"
                                role="document">
                                <div class="modal-content">
                                    <form action="{{ route('reseller-benefits.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addDataTitle">Add Benefit</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="benefit" class="form-label">Benefit</label>
                                                <input type="text" class="form-control" id="benefit" name="benefit"
                                                    required>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="thumbnail" class="form-label">Thumbnail</label>
                                                    <input type="file" name="thumbnail" accept="image/*"
                                                        class="form-control"
                                                        onchange="previewImage(event,'thumbnailPreview')">
                                                    <div class="my-3">
                                                        <img id="thumbnailPreview" class="img-fluid"
                                                            style="max-height:200px;display:none;">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="status" class="form-label">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select>
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
                                <table id="dom-jqry" class="table table-striped table-bordered text-center">
                                    <thead class="text-center">
                                        <tr>
                                            <th>Thumbnail</th>
                                            <th>Benefit</th>
                                            <th>Status</th>
                                            <th style="width: 10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($benefits as $benefit)
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('storage/' . $benefit->thumbnail) }}"
                                                        class="img-fluid" style="max-height: 80px">
                                                </td>
                                                <td>{{ $benefit->benefit }}</td>
                                                <td>
                                                    <form
                                                        action="{{ route('reseller-benefits.changeStatus', $benefit->id) }}"
                                                        method="post">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit"
                                                            class="btn {{ $benefit->status == 'active' ? 'btn-success' : 'btn-danger' }} btn-sm">{{ $benefit->status }}</button>
                                                    </form>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editData{{ $benefit->id }}"><i
                                                                class="ti ti-edit"></i></button>
                                                        <form
                                                            action="{{ route('reseller-benefits.destroy', $benefit->id) }}"
                                                            method="POST" class="form-delete"
                                                            data-id="{{ $benefit->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm btn-delete"><i
                                                                    class="ti ti-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            {{-- modal --}}
                                            <div id="editData{{ $benefit->id }}" class="modal fade" tabindex="-1"
                                                role="dialog" aria-labelledby="editData{{ $benefit->id }}Title"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable"
                                                    role="document">
                                                    <div class="modal-content">
                                                        <form
                                                            action="{{ route('reseller-benefits.update', $benefit->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editData{{ $benefit->id }}Title">Edit
                                                                    Benefit</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="benefit"
                                                                        class="form-label">Benefit</label>
                                                                    <input type="text" class="form-control"
                                                                        id="benefit" name="benefit"
                                                                        value="{{ $benefit->benefit }}" required>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <label for="thumbnail"
                                                                            class="form-label">Thumbnail</label>
                                                                        <input type="file" name="thumbnail"
                                                                            accept="image/*" class="form-control"
                                                                            onchange="previewImage(event,'thumbnailPreview{{ $benefit->id }}')">
                                                                        <div class="my-3">
                                                                            <img id="thumbnailPreview{{ $benefit->id }}"
                                                                                src="{{ asset('storage/' . $benefit->thumbnail) }}"
                                                                                class="img-fluid"
                                                                                style="max-height:200px;">
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="status"
                                                                            class="form-label">Status</label>
                                                                        <select name="status" class="form-select"
                                                                            required>
                                                                            <option value="active"
                                                                                {{ $benefit->status == 'active' ? 'selected' : '' }}>
                                                                                Active</option>
                                                                            <option value="inactive"
                                                                                {{ $benefit->status == 'inactive' ? 'selected' : '' }}>
                                                                                Inactive
                                                                            </option>
                                                                        </select>
                                                                    </div>
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

@push('script')
    <script>
        function previewImage(evt, id) {
            const r = new FileReader();
            r.onload = () => {
                const img = document.getElementById(id);
                img.src = r.result;
                img.style.display = 'block';
            };
            r.readAsDataURL(evt.target.files[0]);
        }
    </script>
@endpush
