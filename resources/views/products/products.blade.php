@extends('Layouts.app')
@section('title', 'products')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-between align-items-center">
                                <h3>Products</h3>
                                <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm ms-auto">Add Data</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="dt-responsive">
                                <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Variants</th>
                                            <th style="width: 10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($products as $item)
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal" data-bs-target="#modal-{{ $item->id }}">
                                                        Show Variants
                                                    </button>
                                                </td>

                                                <td class="text-center">
                                                    <a href="{{ route('products.edit', $item->id) }}"
                                                        class="btn btn-sm btn-warning"><i class="ti ti-edit"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Modal for each product -->
                                @foreach ($products as $item)
                                    <div class="modal fade" id="modal-{{ $item->id }}" tabindex="-1"
                                        aria-labelledby="modalLabel-{{ $item->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel-{{ $item->id }}">Variants of
                                                        {{ $item->name }}</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">

                                                    @forelse ($item->variants as $vi => $variant)
                                                        <div class="mb-4 border rounded p-3">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="">
                                                                    <h6 class="fw-bold">Variant #{{ $vi + 1 }}
                                                                        - {{ $variant->flavour->name ?? '-' }}</h6>
                                                                    <p><strong>Categories:</strong>
                                                                        {{ $variant->categories->pluck('name')->implode(', ') ?: '-' }}
                                                                    </p>
                                                                </div>
                                                                <div class="">
                                                                    <form
                                                                        action="{{ route('products.status', $variant->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input type="hidden" name="status"
                                                                            value="{{ $variant->status == 'active' ? 'inactive' : 'active' }}">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-{{ $variant->status == 'active' ? 'warning' : 'success' }}">
                                                                            {{ $variant->status == 'active' ? 'Set Inactive' : 'Set Active' }}
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th>Size</th>
                                                                            <th>Price</th>
                                                                            <th>Discount (%)</th>
                                                                            <th>Final Price</th>
                                                                            <th>Stock</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($variant->sizes as $size)
                                                                            <tr>
                                                                                <td>{{ $size->size->label ?? '-' }}
                                                                                    gr</td>
                                                                                <td>Rp {{ number_format($size->price) }}
                                                                                </td>
                                                                                <td>{{ $size->discount }}%
                                                                                </td>
                                                                                <td>Rp
                                                                                    {{ number_format($size->discount_price ?? $size->price - ($size->price * $size->discount) / 100) }}
                                                                                </td>
                                                                                <td>{{ $size->quantity }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <p class="text-muted">No variants found for this
                                                            product.</p>
                                                    @endforelse

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
