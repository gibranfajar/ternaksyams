@extends('Layouts.app')
@section('title', 'Data Reseller')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <h3>Data Reseller</h3>
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
                                        @foreach ($resellers as $reseller)
                                            <tr>
                                                <td>{{ $reseller->name }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#resellerModal{{ $reseller->id }}">
                                                        Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach ($resellers as $reseller)
                                            <div class="modal fade" id="resellerModal{{ $reseller->id }}" tabindex="-1"
                                                aria-labelledby="resellerModalLabel{{ $reseller->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="resellerModalLabel{{ $reseller->id }}">
                                                                Detail Reseller - {{ $reseller->name }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <dl class="row">
                                                                <dt class="col-sm-4">Name</dt>
                                                                <dd class="col-sm-8">{{ $reseller->name }}</dd>

                                                                <dt class="col-sm-4">Email</dt>
                                                                <dd class="col-sm-8">{{ $reseller->email }}</dd>

                                                                <dt class="col-sm-4">WhatsApp</dt>
                                                                <dd class="col-sm-8">{{ $reseller->whatsapp_number }}</dd>

                                                                <dt class="col-sm-4">Address</dt>
                                                                <dd class="col-sm-8">{{ $reseller->address }}</dd>

                                                                <dt class="col-sm-4">Location</dt>
                                                                <dd class="col-sm-8">
                                                                    {{ $reseller->subdistrict }}, {{ $reseller->city }},
                                                                    {{ $reseller->province }} -
                                                                    {{ $reseller->postal_code }}
                                                                </dd>

                                                                <hr class="my-2">

                                                                @php
                                                                    $account = \App\Models\PartnerAccount::where(
                                                                        'partner_id',
                                                                        $reseller->id,
                                                                    )->first();
                                                                @endphp

                                                                @if ($account)
                                                                    <dt class="col-sm-4">Bank Name</dt>
                                                                    <dd class="col-sm-8">{{ $account->card }}</dd>

                                                                    <dt class="col-sm-4">Account Number</dt>
                                                                    <dd class="col-sm-8">{{ $account->card_number }}</dd>

                                                                    <dt class="col-sm-4">Account Holder</dt>
                                                                    <dd class="col-sm-8">{{ $account->card_name }}</dd>
                                                                @else
                                                                    <dt class="col-sm-4">Rekening</dt>
                                                                    <dd class="col-sm-8"><em>Tidak ditemukan</em></dd>
                                                                @endif

                                                            </dl>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Tutup</button>
                                                        </div>
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
