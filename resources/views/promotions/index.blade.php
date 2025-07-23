@extends('Layouts.app')
@section('title', 'Promotions')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-between align-items-start flex-wrap">
                                <div class="pe-3">
                                    <h3 class="mb-1">Promotions</h3>
                                    <small class="text-danger fst-italic fw-bold">
                                        Setelah tanggal berakhir, promo akan otomatis terhapus dan Anda bisa membuat
                                        yang baru.
                                    </small>
                                </div>

                                <button type="button" class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal"
                                    data-bs-target="#addData">Add Data</button>
                            </div>
                        </div>

                        {{-- modal --}}
                        <div id="addData" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addDataTitle"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <form action="{{ route('promotions.store') }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="addDataTitle">Add Promotion</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Name</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Title</label>
                                                <input type="text" class="form-control" id="title" name="title"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Description</label>
                                                <input type="hidden" id="description" name="description">
                                                <trix-editor input="description"></trix-editor>
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
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <label for="start_date" class="form-label">Start Date</label>
                                                    <input type="datetime-local" class="form-control" id="start_date"
                                                        name="start_date" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="end_date" class="form-label">End Date</label>
                                                    <input type="datetime-local" class="form-control" id="end_date"
                                                        name="end_date" required>
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
                                    <thead>
                                        <tr>
                                            <th>Thumbnail</th>
                                            <th>Name</th>
                                            <th>Title</th>
                                            <th>Active Time</th>
                                            <th>Status</th>
                                            <th style="width: 10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($promotions as $promotion)
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('storage/' . $promotion->thumbnail) }}"
                                                        class="img-fluid" style="max-height: 100px">
                                                </td>
                                                <td>{{ $promotion->name }}</td>
                                                <td>{{ $promotion->title }}</td>
                                                <td>{{ Carbon\Carbon::parse($promotion->start_date)->format('d M Y H:i') }}
                                                    - {{ Carbon\Carbon::parse($promotion->end_date)->format('d M Y H:i') }}
                                                </td>
                                                <td>

                                                    <form action="{{ route('promotions.changeStatus', $promotion->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status" value="active">
                                                        <button type="submit"
                                                            class="btn {{ $promotion->status == 'active' ? 'btn-success' : 'btn-danger' }} btn-sm">{{ $promotion->status }}</button>
                                                    </form>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <button type="button" class="btn btn-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editData{{ $promotion->id }}">
                                                            <i class="ti ti-edit"></i>
                                                        </button>

                                                        <form action="{{ route('promotions.destroy', $promotion->id) }}"
                                                            method="POST" class="form-delete"
                                                            data-id="{{ $promotion->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm btn-delete">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- modal edit --}}
                                            <div id="editData{{ $promotion->id }}" class="modal fade" tabindex="-1"
                                                role="dialog" aria-labelledby="editData{{ $promotion->id }}Title"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('promotions.update', $promotion->id) }}"
                                                            method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="editData{{ $promotion->id }}Title">Edit Promotion
                                                                </h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="name" class="form-label">Name</label>
                                                                    <input type="text" class="form-control"
                                                                        id="name" name="name"
                                                                        value="{{ $promotion->name }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="title" class="form-label">Title</label>
                                                                    <input type="text" class="form-control"
                                                                        id="title" name="title"
                                                                        value="{{ $promotion->title }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="descriptionEdit"
                                                                        class="form-label">Description</label>
                                                                    <input type="hidden" id="descriptionEdit"
                                                                        name="description"
                                                                        value="{{ $promotion->description }}">
                                                                    <trix-editor input="descriptionEdit"></trix-editor>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <label for="thumbnail"
                                                                            class="form-label">Thumbnail</label>
                                                                        <input type="file" name="thumbnail"
                                                                            accept="image/*" class="form-control"
                                                                            onchange="previewImage(event,'thumbnailPreview{{ $promotion->id }}')">
                                                                        <div class="my-3">
                                                                            <img id="thumbnailPreview{{ $promotion->id }}"
                                                                                src="{{ asset('storage/' . $promotion->thumbnail) }}"
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
                                                                                {{ $promotion->status == 'active' ? 'selected' : '' }}>
                                                                                Active</option>
                                                                            <option value="inactive"
                                                                                {{ $promotion->status == 'inactive' ? 'selected' : '' }}>
                                                                                Inactive</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <label for="start_date" class="form-label">Start
                                                                            Date</label>
                                                                        <input type="datetime-local" class="form-control"
                                                                            id="start_date" name="start_date"
                                                                            value="{{ $promotion->start_date }}" required>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="end_date" class="form-label">End
                                                                            Date</label>
                                                                        <input type="datetime-local" class="form-control"
                                                                            id="end_date" name="end_date"
                                                                            value="{{ $promotion->end_date }}" required>
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
