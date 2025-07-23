@extends('Layouts.app')
@section('title', 'Data Orders')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Data Orders</h3>
                        </div>

                        <div class="card-body">
                            <div class="dt-responsive">
                                <table id="dom-jqry" class="table table-striped table-bordered nowrap">
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Order Date</th>
                                            <th style="width: 10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            <tr>
                                                <td>{{ $order->invoice }}</td>
                                                <td>{{ $order->status }}</td>
                                                <td>Rp{{ number_format($order->total, 0, ',', '.') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y') }}
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#orderModal{{ $order->id }}">
                                                        <i class="ti ti-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- Modals --}}
                                @foreach ($orders as $order)
                                    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1"
                                        aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderModalLabel{{ $order->id }}">
                                                        Detail Order - {{ $order->invoice }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        {{-- Kolom Kiri: Detail Pengiriman --}}
                                                        <div class="col-md-6">
                                                            <h5 class="mb-3">🧾 Informasi Pengiriman</h5>
                                                            <dl class="row">
                                                                <dt class="col-sm-4">Nama</dt>
                                                                <dd class="col-sm-8">{{ $order->shipping->recipient_name }}
                                                                </dd>

                                                                <dt class="col-sm-4">Email</dt>
                                                                <dd class="col-sm-8">{{ $order->shipping->email }}</dd>

                                                                <dt class="col-sm-4">WhatsApp</dt>
                                                                <dd class="col-sm-8">{{ $order->shipping->phone }}</dd>

                                                                <dt class="col-sm-4">Alamat</dt>
                                                                <dd class="col-sm-8">{{ $order->shipping->address }}</dd>

                                                                <dt class="col-sm-4">Lokasi</dt>
                                                                <dd class="col-sm-8">
                                                                    {{ $order->shipping->province }},
                                                                    {{ $order->shipping->city }},
                                                                    {{ $order->shipping->district }},
                                                                    {{ $order->shipping->subdistrict }} -
                                                                    {{ $order->shipping->postal_code }}
                                                                </dd>
                                                            </dl>
                                                        </div>

                                                        {{-- Kolom Kanan: Detail Pembayaran --}}
                                                        <div class="col-md-6">
                                                            <h5 class="mb-3">💳 Informasi Pembayaran</h5>
                                                            <dl class="row">
                                                                <dt class="col-sm-4">Metode</dt>
                                                                <dd class="col-sm-8 d-flex align-items-center gap-2">
                                                                    @if (Str::contains(strtolower($order->payment->method), 'bca'))
                                                                        <img src="{{ asset('assets/images/bank/bca.svg') }}"
                                                                            style="height: 24px;">
                                                                    @elseif (Str::contains(strtolower($order->payment->method), 'bni'))
                                                                        <img src="{{ asset('assets/images/bank/bni.svg') }}"
                                                                            style="height: 24px;">
                                                                    @elseif (Str::contains(strtolower($order->payment->method), 'bri'))
                                                                        <img src="{{ asset('assets/images/bank/bri.svg') }}"
                                                                            style="height: 24px;">
                                                                    @elseif (Str::contains(strtolower($order->payment->method), 'mandiri'))
                                                                        <img src="{{ asset('assets/images/bank/mandiri.svg') }}"
                                                                            style="height: 24px;">
                                                                    @else
                                                                        <i class="ti ti-credit-card"></i>
                                                                    @endif
                                                                </dd>

                                                                <dt class="col-sm-4">Status</dt>
                                                                <dd class="col-sm-8">
                                                                    <span
                                                                        class="badge 
                                                                        @if ($order->payment->status === 'paid') bg-success
                                                                        @elseif($order->payment->status === 'pending') bg-warning
                                                                        @else bg-secondary @endif">
                                                                        {{ ucfirst($order->payment->status ?? 'Belum Diketahui') }}
                                                                    </span>
                                                                </dd>

                                                                <dt class="col-sm-4">Invoice</dt>
                                                                <dd class="col-sm-8">{{ $order->invoice }}</dd>

                                                                <dt class="col-sm-4">Tanggal Order</dt>
                                                                <dd class="col-sm-8">
                                                                    {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                                                                </dd>
                                                            </dl>
                                                        </div>
                                                    </div>

                                                    <hr class="my-2">

                                                    <dt class="col-sm-4">List Order Items</dt>
                                                    <dd class="col-sm-12">
                                                        <table class="table table-bordered">
                                                            <thead class="bg-light">
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <th>Size</th>
                                                                    <th>Quantity</th>
                                                                    <th>Price</th>
                                                                    {{-- <th>Total</th> --}}
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($order->orderItems as $item)
                                                                    <tr>
                                                                        <td>
                                                                            {{ $item->variantSize->variant->product->name }}
                                                                            -
                                                                            {{ $item->variantSize->variant->flavour->name }}
                                                                        </td>
                                                                        <td>{{ $item->variantSize->size->label }}gram
                                                                        </td>
                                                                        <td>{{ $item->quantity }}</td>
                                                                        <td>Rp{{ number_format($item->price, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach

                                                                {{-- Ringkasan total --}}
                                                                <tr class=" fw-bold">
                                                                    <td colspan="3" class="text-end">Subtotal</td>
                                                                    <td>
                                                                        Rp{{ number_format($order->orderItems->sum('total'), 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                                <tr class=" fw-bold">
                                                                    <td colspan="3" class="text-end">
                                                                        Ongkir
                                                                        ({{ $order->shipping->shippingOption->courier }}
                                                                        -
                                                                        {{ $order->shipping->shippingOption->service }})
                                                                    </td>
                                                                    <td>
                                                                        Rp{{ number_format($order->shipping->shippingOption->cost, 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                                @if ($order->voucher)
                                                                    <tr class=" text-success fw-bold">
                                                                        <td colspan="3" class="text-end">
                                                                            Diskon ({{ $order->voucher->code }})
                                                                        </td>
                                                                        <td>-
                                                                            Rp{{ number_format($order->voucher->amount, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                <tr class="fw-bold">
                                                                    <td colspan="3" class="text-end">Total
                                                                        Keseluruhan</td>
                                                                    <td>Rp{{ number_format($order->total, 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <hr>
                                                    </dd>
                                                    </dl>
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
