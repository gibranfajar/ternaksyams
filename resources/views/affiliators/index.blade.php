@extends('Layouts.app')
@section('title', 'Data Affiliator')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <h3>Data Affiliator</h3>
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
                                        @foreach ($affiliates as $affiliate)
                                            <tr>
                                                <td>{{ $affiliate->name }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#affiliateModal{{ $affiliate->id }}">
                                                        Detail
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        @foreach ($affiliates as $affiliate)
                                            <div class="modal fade" id="affiliateModal{{ $affiliate->id }}" tabindex="-1"
                                                aria-labelledby="affiliateModalLabel{{ $affiliate->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="affiliateModalLabel{{ $affiliate->id }}">
                                                                Detail Affiliate - {{ $affiliate->name }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <dl class="row">
                                                                <dt class="col-sm-4">Name</dt>
                                                                <dd class="col-sm-8">{{ $affiliate->name }}</dd>

                                                                <dt class="col-sm-4">Email</dt>
                                                                <dd class="col-sm-8">{{ $affiliate->email }}</dd>

                                                                <dt class="col-sm-4">WhatsApp</dt>
                                                                <dd class="col-sm-8">{{ $affiliate->whatsapp_number }}</dd>

                                                                <dt class="col-sm-4">Address</dt>
                                                                <dd class="col-sm-8">{{ $affiliate->address }}</dd>

                                                                <dt class="col-sm-4">Location</dt>
                                                                <dd class="col-sm-8">
                                                                    {{ $affiliate->subdistrict }}, {{ $affiliate->city }},
                                                                    {{ $affiliate->province }} -
                                                                    {{ $affiliate->postal_code }}
                                                                </dd>

                                                                <hr class="my-2">

                                                                @php
                                                                    $account = \App\Models\PartnerAccount::where(
                                                                        'partner_id',
                                                                        $affiliate->id,
                                                                    )->first();
                                                                @endphp

                                                                @if ($account)
                                                                    <dt class="col-sm-4">Sosmed Account</dt>
                                                                    <dd class="col-sm-8">{{ $account->sosmed_account }}
                                                                    </dd>

                                                                    <dt class="col-sm-4">Shopee</dt>
                                                                    <dd class="col-sm-8">{{ $account->shopee }}</dd>

                                                                    <dt class="col-sm-4">Tokopedia</dt>
                                                                    <dd class="col-sm-8">{{ $account->tokopedia }}</dd>

                                                                    <dt class="col-sm-4">Tiktok</dt>
                                                                    <dd class="col-sm-8">{{ $account->tiktok }}</dd>

                                                                    <dt class="col-sm-4">Lazada</dt>
                                                                    <dd class="col-sm-8">{{ $account->lazada }}</dd>
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
