@extends('Layouts.app')
@section('title', 'Flash Sales')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                <div class="pe-3">
                                    <h3 class="mb-1">Flash Sale</h3>
                                    <small class="text-danger fst-italic fw-bold">
                                        Saat ini hanya satu flash sale yang bisa dibuat.
                                        Setelah tanggal berakhir, flash sale akan otomatis dihapus dan Anda bisa membuat
                                        yang baru.
                                    </small>
                                </div>

                                @if ($flashSales->isEmpty())
                                    <div class="mt-2 mt-sm-0">
                                        <a href="{{ route('flashsales.create') }}" class="btn btn-primary">Add Data</a>
                                    </div>
                                @endif

                            </div>
                        </div>

                        <div class="card-body">
                            <div class="dt-responsive">
                                <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Duration</th>
                                            <th style="width: 10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($flashSales as $flashsale)
                                            <tr>
                                                <td>{{ Carbon\Carbon::parse($flashsale->start_date)->format('d M Y H:i') }}
                                                </td>
                                                <td>{{ Carbon\Carbon::parse($flashsale->end_date)->format('d M Y H:i') }}
                                                </td>
                                                <td>{{ Carbon\Carbon::parse($flashsale->start_date)->diffForHumans($flashsale->end_date, true) }}
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#modalFlashSale{{ $flashsale->id }}">
                                                        Show Product
                                                    </button>
                                                    <!-- Modal -->
                                                    <div class="modal fade" id="modalFlashSale{{ $flashsale->id }}"
                                                        tabindex="-1" aria-labelledby="modalLabel{{ $flashsale->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Produk Flash Sale</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal" aria-label="Tutup"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <table class="table table-bordered table-sm">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Product Name</th>
                                                                                <th>Size</th>
                                                                                <th>Original Price</th>
                                                                                <th>Discount</th>
                                                                                <th>Flash Price</th>
                                                                                <th>Stok</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach ($flashsale->productFlashSales as $item)
                                                                                <tr>
                                                                                    <td>
                                                                                        {{ $item->variantSize->variant->product->name }}
                                                                                        -
                                                                                        {{ $item->variantSize->variant->flavour->name }}
                                                                                    </td>

                                                                                    <td>{{ $item->variantSize->size->label }}
                                                                                        {{ $item->variantSize->size->unit }}
                                                                                    </td>
                                                                                    <td>Rp
                                                                                        {{ number_format($item->variantSize->price, 0, ',', '.') }}
                                                                                    </td>
                                                                                    <td> {{ $item->discount }}%</td>
                                                                                    <td>Rp
                                                                                        {{ number_format($item->price, 0, ',', '.') }}
                                                                                    </td>
                                                                                    <td>{{ $item->quantity }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
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
