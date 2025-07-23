@extends('Layouts.app')

@section('title', 'Create Flash Sale Product')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="card p-4">
                    <div class="col-sm-12">
                        <form id="flashSaleForm" method="POST" action="{{ route('flashsales.store') }}">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Start Date</label>
                                    <input type="datetime-local" class="form-control" name="start_date" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">End Date</label>
                                    <input type="datetime-local" class="form-control" name="end_date" required>
                                </div>
                            </div>

                            <table id="variantTable" class="display">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($variants as $index => $variant)
                                        <tr data-index="{{ $index }}">
                                            <td>{{ $variant->product->name }} - {{ $variant->flavour->name }}</td>
                                            <td>
                                                <input type="checkbox" class="form-check-input variant-checkbox"
                                                    name="variants[{{ $index }}][checked]" value="1"
                                                    data-variant-id="{{ $variant->id }}">
                                                <input type="hidden" name="variants[{{ $index }}][id]"
                                                    value="{{ $variant->id }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success">Tambahkan ke Flash Sale</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const variantData = @json($variants);

        function formatSizes(index) {
            const sizes = variantData[index].sizes;
            if (!sizes || sizes.length === 0) {
                return `<div class="text-danger">Size tidak ditemukan.</div>`;
            }

            let html = `<table class="table table-sm mb-0"><thead>
                    <tr>
                        <th>Size</th>
                        <th>Original Price</th>
                        <th>Qty Flash</th>
                        <th>Discount (%)</th>
                        <th>Price Flash</th>
                    </tr>
                </thead><tbody>`;

            sizes.forEach((s, i) => {
                const label = `${s.size.label} ${s.size.unit}`;
                html += `<tr class="size-row" data-original="${s.price}">
            <td>${label}</td>
            <td>Rp ${s.price.toLocaleString()}</td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                    name="variants[${index}][sizes][${i}][qty]">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm flash-discount"
                    name="variants[${index}][sizes][${i}][discount]">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm flash-price"
                    name="variants[${index}][sizes][${i}][price]">
            </td>
            <input type="hidden" name="variants[${index}][sizes][${i}][size_id]" value="${s.id}">
        </tr>`;
            });

            html += `</tbody></table>`;
            return `<div class="p-2 bg-light border-top">${html}</div>`;
        }


        $(document).ready(function() {
            const table = $('#variantTable').DataTable({
                paging: false,
                info: false
            });

            $('#variantTable').on('change', '.variant-checkbox', function() {
                const tr = $(this).closest('tr');
                const row = table.row(tr);
                const index = tr.data('index');

                if (this.checked) {
                    row.child(formatSizes(index)).show();
                    tr.addClass('shown');
                } else {
                    row.child.hide();
                    tr.removeClass('shown');
                }
            });

            // Hapus data yang tidak dicentang sebelum submit
            $('#flashSaleForm').on('submit', function() {
                $('.variant-checkbox').each(function() {
                    const index = $(this).closest('tr').data('index');
                    if (!$(this).is(':checked')) {
                        $(`[name^="variants[${index}]"]`).remove();
                    }
                });
            });
        });

        // Hitung harga flash dan diskon dua arah
        $(document).on('input', '.flash-price, .flash-discount', function() {
            const row = $(this).closest('.size-row');
            const originalPrice = parseFloat(row.data('original') || 0);
            const discountInput = row.find('.flash-discount');
            const priceInput = row.find('.flash-price');

            const flashPrice = parseFloat(priceInput.val());
            const discount = parseFloat(discountInput.val());

            if ($(this).hasClass('flash-discount')) {
                // Jika user isi diskon, hitung harga flash
                if (!isNaN(discount) && originalPrice > 0) {
                    const newPrice = originalPrice - (originalPrice * discount / 100);
                    priceInput.val(Math.floor(newPrice));
                }
            } else if ($(this).hasClass('flash-price')) {
                // Jika user isi harga flash, hitung diskon
                if (!isNaN(flashPrice) && originalPrice > 0) {
                    const newDiscount = ((originalPrice - flashPrice) / originalPrice) * 100;
                    discountInput.val(Math.round(newDiscount));
                }
            }
        });
    </script>
@endpush
