@extends('Layouts.app')
@section('title', 'Faqs')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-sm-12">

                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h3>Faqs</h3>
                            <button type="button" class="btn btn-primary btn-sm ms-auto" data-bs-toggle="modal"
                                data-bs-target="#addData">Add Data</button>
                        </div>

                        <div class="card-body">
                            <div class="dt-responsive table-responsive">
                                {{-- Filter --}}
                                <div class="row my-3 align-items-end">
                                    {{-- Filter Role --}}
                                    <div class="col-md-5">
                                        <label for="filterRole" class="form-label">Filter by Role</label>
                                        <select id="filterRole" class="form-select">
                                            <option value="">-- All Roles --</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Filter Category --}}
                                    <div class="col-md-5">
                                        <label for="filterCategory" class="form-label">Filter by Category</label>
                                        <select id="filterCategory" class="form-select">
                                            <option value="">-- All Categories --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Reset Button --}}
                                    <div class="col-md-2">
                                        <button
                                            class="btn btn-secondary w-100 d-flex justify-content-center align-items-center"
                                            id="resetFilter">
                                            <i class="ti ti-refresh me-1"></i>
                                        </button>
                                    </div>
                                </div>

                                <table id="faq-table" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th>Category</th>
                                            <th>Role</th>
                                            <th>Answer</th>
                                            <th class="text-center" style="width: 120px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($faqs as $index => $faq)
                                            <tr>
                                                <td>{{ $faq->question }}</td>
                                                <td>{{ $faq->category->name }}</td>
                                                <td>{{ $faq->role->name }}</td>
                                                <td>{!! Str::limit(strip_tags($faq->answer), 50) !!}</td>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        {{-- Change Status Form --}}
                                                        <form action="{{ route('faqs.changeStatus', $faq->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <input type="hidden" name="status"
                                                                value="{{ $faq->status == 'active' ? 'inactive' : 'active' }}">
                                                            <button type="submit"
                                                                class="btn text-sm btn-sm btn-{{ $faq->status == 'active' ? 'success' : 'secondary' }}">
                                                                {{ $faq->status == 'active' ? 'Active' : 'Inactive' }}
                                                            </button>
                                                        </form>

                                                        {{-- Edit Button --}}
                                                        <button type="button" class="btn btn-warning btn-sm text-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editData{{ $faq->id }}">
                                                            <i class="ti ti-edit"></i>
                                                        </button>

                                                        {{-- Delete Form --}}
                                                        <form action="{{ route('faqs.destroy', $faq->id) }}" method="POST"
                                                            class="form-delete" data-id="{{ $faq->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm text-sm btn-delete">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>

                                                    </div>
                                                </td>
                                            </tr>

                                            {{-- modal edit --}}
                                            <div id="editData{{ $faq->id }}" class="modal fade" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <form action="{{ route('faqs.update', $faq->id) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Edit Faq</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="question"
                                                                        class="form-label">Question</label>
                                                                    <input type="text" class="form-control"
                                                                        id="question" name="question"
                                                                        value="{{ $faq->question }}" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="answerEdit"
                                                                        class="form-label">Answer</label>
                                                                    <input type="hidden" id="answerEdit" name="answer"
                                                                        value="{{ $faq->answer }}">
                                                                    <trix-editor input="answerEdit"></trix-editor>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <label for="category_id"
                                                                            class="form-label">Category</label>
                                                                        <select name="category_id" id="category_id"
                                                                            class="form-select" required>
                                                                            <option value="" disabled selected>--
                                                                                Select Category --</option>
                                                                            @foreach ($categories as $category)
                                                                                <option value="{{ $category->id }}"
                                                                                    {{ $faq->category_id == $category->id ? 'selected' : '' }}>
                                                                                    {{ $category->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <label for="role_id"
                                                                            class="form-label">Role</label>
                                                                        <select name="role_id" id="role_id"
                                                                            class="form-select" required>
                                                                            <option value="" disabled selected>--
                                                                                Select Role --</option>
                                                                            @foreach ($roles as $role)
                                                                                <option value="{{ $role->id }}"
                                                                                    {{ $faq->role_id == $role->id ? 'selected' : '' }}>
                                                                                    {{ $role->name }}</option>
                                                                            @endforeach
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
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No data found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Add Modal --}}
                    <div id="addData" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                            <div class="modal-content">
                                <form action="{{ route('faqs.store') }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Faq</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="question" class="form-label">Question</label>
                                            <input type="text" class="form-control" id="question" name="question"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="answer" class="form-label">Answer</label>
                                            <input type="hidden" id="answer" name="answer">
                                            <trix-editor input="answer"></trix-editor>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="category_id" class="form-label">Category</label>
                                                <select name="category_id" id="category_id" class="form-select" required>
                                                    <option value="" disabled selected>-- Select Category --</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}">{{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="role_id" class="form-label">Role</label>
                                                <select name="role_id" id="role_id" class="form-select" required>
                                                    <option value="" disabled selected>-- Select Role --</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                                    @endforeach
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
                    {{-- End Modal --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#faq-table').DataTable({
                responsive: true
            });

            // Filter Category (index 1)
            $('#filterCategory').on('change', function() {
                let value = $(this).val();
                table.column(1).search(value).draw();
            });

            // Filter Role (index 2)
            $('#filterRole').on('change', function() {
                let value = $(this).val();
                table.column(2).search(value).draw();
            });

            // Reset Filter 
            $('#resetFilter').on('click', function() {
                $('#filterRole').val('');
                $('#filterCategory').val('');
                table.search('').columns().search('').draw();
            });
        });
    </script>
@endpush
