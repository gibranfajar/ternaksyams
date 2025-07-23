@extends('Layouts.app')
@section('title', 'Articles')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3>Articles</h3>
                                <a href="{{ route('articles.create') }}" class="btn btn-primary btn-sm">Add Data</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="dt-responsive">
                                <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>Thumbnail</th>
                                            <th>Name</th>
                                            <th>Categories</th>
                                            <th style="width: 10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($articles as $item)
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('storage/' . $item->thumbnail) }}"
                                                        alt="{{ $item->name }}" style="max-height: 80px">
                                                </td>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    @foreach ($item->categories as $cat)
                                                        <span class="badge bg-secondary">{{ $cat->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td class="text-center align-middle">
                                                    <div class="d-flex justify-content-center gap-2">
                                                        <a href="{{ route('articles.edit', $item->id) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                        <form action="{{ route('articles.destroy', $item->id) }}"
                                                            method="POST" class="form-delete"
                                                            data-id="{{ $item->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
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
