@extends('Layouts.app')
@section('title', 'Vouchers')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3>Vouchers</h3>
                                <a href="{{ route('vouchers.create') }}" class="btn btn-primary ml-auto">Add Data</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="dt-responsive">
                                <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Target Audience</th>
                                            <th>Detail</th>
                                            <th style="width: 10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vouchers as $item)
                                            <tr>
                                                <td>{{ $item->code }}</td>
                                                <td>{{ $item->target_audience }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#voucherModal{{ $item->id }}">
                                                        Show
                                                    </button>

                                                    <!-- Modal Detail Voucher -->
                                                    <div class="modal fade" id="voucherModal{{ $item->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="voucherModalLabel{{ $item->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="voucherModalLabel{{ $item->id }}">
                                                                        Detail Voucher - {{ $item->code }}
                                                                    </h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    {{-- === DETAIL UTAMA === --}}
                                                                    <h5 class="mb-3 border-bottom pb-2">Informasi Utama</h5>
                                                                    <dl class="row">
                                                                        <dt class="col-sm-4">Kode</dt>
                                                                        <dd class="col-sm-8">{{ $item->code }}</dd>

                                                                        <dt class="col-sm-4">Tipe</dt>
                                                                        <dd class="col-sm-8">{{ ucfirst($item->type) }}</dd>

                                                                        <dt class="col-sm-4">Amount</dt>
                                                                        <dd class="col-sm-8">
                                                                            {{ $item->amount_type === 'percent'
                                                                                ? number_format($item->amount, 0, ',', '.') . '%'
                                                                                : 'Rp ' . number_format($item->amount, 0, ',', '.') }}
                                                                        </dd>

                                                                        @if ($item->amount_type === 'percent')
                                                                            <dt class="col-sm-4">Max Discount Transaksi</dt>
                                                                            <dd class="col-sm-8">Rp
                                                                                {{ number_format($item->max_discount, 0, ',', '.') }}
                                                                            </dd>
                                                                        @endif

                                                                        <dt class="col-sm-4">Minimal Transaksi</dt>
                                                                        <dd class="col-sm-8">Rp
                                                                            {{ number_format($item->min_transaction, 0, ',', '.') }}
                                                                        </dd>

                                                                        <dt class="col-sm-4">Target Audience</dt>
                                                                        <dd class="col-sm-8">
                                                                            {{ ucfirst($item->target_audience) }}</dd>

                                                                        <dt class="col-sm-4">Quota / Limit</dt>
                                                                        <dd class="col-sm-8">{{ $item->quota }} /
                                                                            {{ $item->limit }}</dd>

                                                                        <dt class="col-sm-4">Used</dt>
                                                                        <dd class="col-sm-8">{{ $item->used }} kali</dd>

                                                                        <dt class="col-sm-4">Periode Aktif</dt>
                                                                        <dd class="col-sm-8">
                                                                            {{ \Carbon\Carbon::parse($item->start_date)->translatedFormat('d F Y') }}
                                                                            →
                                                                            {{ \Carbon\Carbon::parse($item->end_date)->translatedFormat('d F Y') }}
                                                                        </dd>

                                                                        <dt class="col-sm-4">Status</dt>
                                                                        <dd class="col-sm-8">
                                                                            <span
                                                                                class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                                                                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                                                                            </span>
                                                                        </dd>
                                                                    </dl>

                                                                    {{-- === CONTENT UNTUK USER === --}}
                                                                    @if ($item->content)
                                                                        <h5 class="mt-4 mb-3 border-bottom pb-2">Konten
                                                                            Voucher</h5>
                                                                        <dl class="row">
                                                                            <dt class="col-sm-4">Judul</dt>
                                                                            <dd class="col-sm-8">
                                                                                {{ $item->content->title }}</dd>

                                                                            <dt class="col-sm-4">Deskripsi</dt>
                                                                            <dd class="col-sm-8 text-wrap text-break">
                                                                                {!! $item->content->description !!}
                                                                            </dd>

                                                                            <dt class="col-sm-4">Used At</dt>
                                                                            <dd class="col-sm-8">
                                                                                {{ $item->content->used_at }}</dd>

                                                                            <dt class="col-sm-4">Syarat & Ketentuan</dt>
                                                                            <dd class="col-sm-8 text-wrap text-break">
                                                                                {!! $item->content->tnc !!}
                                                                            </dd>

                                                                            @if ($item->content->banner)
                                                                                <dt class="col-sm-4">Banner</dt>
                                                                                <dd class="col-sm-8">
                                                                                    <img src="{{ asset('storage/' . $item->content->banner) }}"
                                                                                        class="img-fluid rounded"
                                                                                        style="max-height: 200px;">
                                                                                </dd>
                                                                            @endif
                                                                        </dl>
                                                                    @endif

                                                                    {{-- === PRODUK TERKAIT === --}}
                                                                    @if ($item->type === 'product')
                                                                        <h5 class="mt-4 mb-3 border-bottom pb-2">Produk
                                                                            Terkait</h5>
                                                                        <ul class="list-group list-group-flush">
                                                                            @foreach ($item->products as $vp)
                                                                                @php
                                                                                    $variant = $vp->productVariant;
                                                                                @endphp

                                                                                <li class="list-group-item">
                                                                                    {{ $variant?->product?->name ?? 'Produk tidak ditemukan' }}
                                                                                    -
                                                                                    {{ $variant?->flavour?->name ?? 'Varian tidak ditemukan' }}
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif


                                                                    {{-- === USER TERPILIH === --}}
                                                                    @if ($item->target_audience === 'user')
                                                                        <h5 class="mt-4 mb-3 border-bottom pb-2">User
                                                                            Terpilih</h5>
                                                                        <ul class="list-group list-group-flush">
                                                                            @foreach ($item->users as $u)
                                                                                <li class="list-group-item">
                                                                                    {{ $u->name }}
                                                                                    ({{ $u->email }})
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @endif
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Tutup</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                                <td class="text-center">
                                                    <form action="{{ route('vouchers.changeStatus', $item->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="status"
                                                            value="{{ $item->is_active === 1 ? 0 : 1 }}">
                                                        <button type="submit"
                                                            class="btn btn-sm btn-{{ $item->is_active === 1 ? 'success' : 'danger' }}">
                                                            {{ $item->is_active === 1 ? 'Active' : 'Inactive' }}
                                                        </button>
                                                    </form>
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
