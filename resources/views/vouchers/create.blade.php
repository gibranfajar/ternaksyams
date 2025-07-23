@extends('Layouts.app')
@section('title', 'Create Voucher')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('vouchers.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="code" class="form-label">Code Voucher</label>
                                        <input type="text" class="form-control" id="code" name="code" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="type" class="form-label">Type</label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="product">Products</option>
                                            <option value="shipping">Shipping</option>
                                            <option value="transaction">Transaction</option>
                                        </select>
                                        <div id="product-selector" class="mt-3 d-none">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label class="form-label d-block">Pilih Produk</label>
                                                    <button type="button" class="btn btn-outline-primary"
                                                        data-bs-toggle="modal" data-bs-target="#productModal">
                                                        Tambah Produk
                                                    </button>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mt-2" id="selected-products-container">
                                                        <strong>Produk Terpilih:</strong>
                                                        <ul id="selected-products-list" class="list-unstyled mb-0"></ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="products">
                                            <div id="hidden-product-inputs"></div>
                                        </div>
                                        <div class="modal fade" id="productModal" tabindex="-1">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Pilih Produk</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-bordered" id="product-table">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" id="select-all-products" />
                                                                    </th>
                                                                    <th>Nama Produk</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($products as $product)
                                                                    <tr>
                                                                        <td>
                                                                            <input type="checkbox" class="product-checkbox"
                                                                                value="{{ $product->id }}"
                                                                                data-name="{{ $product->product->name }} - {{ $product->flavour->name }}">
                                                                        </td>
                                                                        <td>{{ $product->product->name }} -
                                                                            {{ $product->flavour->name }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary"
                                                            data-bs-dismiss="modal">Selesai Pilih</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="amount_type" class="form-label">Amount Type</label>
                                        <select class="form-select" id="amount_type" name="amount_type" required>
                                            <option value="fixed">Fixed</option>
                                            <option value="percent">Percentage</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="amount" class="form-label">Amount</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="amount" name="amount"
                                                required>
                                            <span class="input-group-text" id="amount-suffix"
                                                style="display: none;">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="max_discount" class="form-label">Max Discount Transaction</label>
                                        <input type="number" class="form-control" id="max_discount" name="max_discount">
                                        <small class="form-text fst-italic text-danger">Wajib diisi jika diskon berupa
                                            persen. Digunakan
                                            untuk membatasi jumlah
                                            maksimal potongan harga.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="min_transaction" class="form-label">Min Transaction</label>
                                        <input type="number" class="form-control" id="min_transaction"
                                            name="min_transaction" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="target_audience" class="form-label">Target Audience</label>
                                        <select name="target_audience" class="form-select" id="target_audience">
                                            <option value="all">All</option>
                                            <option value="user">User</option>
                                            <option value="guest">Guest</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="quota" class="form-label">Quota</label>
                                        <input type="number" class="form-control" id="quota" name="quota"
                                            required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="limit" class="form-label">Limit</label>
                                        <input type="number" class="form-control" id="limit" name="limit"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="end_date" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date"
                                            required>
                                    </div>
                                </div>

                                {{-- Hidden by default --}}
                                <div id="user-extra-fields" class="mt-4 d-none">
                                    <hr>
                                    <h5>Voucher Detail for Users</h5>

                                    <div id="user-selector" class="mt-3 d-none">
                                        <label class="form-label d-block">Pilih User</label>
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#userModal">
                                            Tambah User
                                        </button>

                                        <div class="mt-2" id="selected-users-container">
                                            <strong>User Terpilih:</strong>
                                            <div id="hidden-user-inputs"></div>
                                            <ul id="selected-users-list" class="list-unstyled mb-0"></ul>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="userModal" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Pilih User</h5>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <select id="filter-birthday" class="form-select">
                                                                <option value="">-- Semua Ulang Tahun --</option>
                                                                <option value="this_month">Ulang Tahun Bulan Ini</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select id="filter-top-user" class="form-select">
                                                                <option value="">-- Semua User --</option>
                                                                <option value="top_buyers">Paling Banyak Beli</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <table class="table table-bordered" id="user-table">
                                                        <thead>
                                                            <tr>
                                                                <th><input type="checkbox" id="select-all-users" />
                                                                </th>
                                                                <th>Nama</th>
                                                                <th>Email</th>
                                                                <th>Keterangan</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($users as $user)
                                                                @php
                                                                    $birthLabel = '';
                                                                    $highlight = false;

                                                                    $birthDateRaw = $user->profiles->birth_date ?? null;
                                                                    $birthDate = $birthDateRaw
                                                                        ? \Carbon\Carbon::parse($birthDateRaw)
                                                                        : null;

                                                                    if ($birthDate) {
                                                                        $today = now()->startOfDay();
                                                                        $birthdayThisYear = $birthDate
                                                                            ->copy()
                                                                            ->year($today->year);

                                                                        if ($birthdayThisYear->isPast()) {
                                                                            $birthdayThisYear = $birthdayThisYear->addYear();
                                                                        }

                                                                        $diffDays = $today->diffInDays(
                                                                            $birthdayThisYear,
                                                                            false,
                                                                        );

                                                                        if ($diffDays >= 0 && $diffDays <= 7) {
                                                                            $highlight = true;
                                                                            $birthLabel =
                                                                                $diffDays === 0
                                                                                    ? 'Today is birthday!'
                                                                                    : "$diffDays day" .
                                                                                        ($diffDays > 1 ? 's' : '') .
                                                                                        ' to birthday';
                                                                        }
                                                                    }
                                                                @endphp


                                                                <tr class="{{ $highlight ? 'table-warning' : '' }}">
                                                                    <td>
                                                                        <input type="checkbox" class="user-checkbox"
                                                                            value="{{ $user->id }}"
                                                                            data-name="{{ $user->name }}">
                                                                    </td>
                                                                    <td>{{ $user->name }}</td>
                                                                    <td>{{ $user->email }}</td>
                                                                    <td>{{ $birthLabel }}</td>
                                                                </tr>
                                                            @endforeach

                                                        </tbody>

                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary"
                                                        data-bs-dismiss="modal">Selesai Pilih</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" id="title">
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <input type="hidden" class="form-control" name="description" id="description">
                                        <trix-editor input="description"></trix-editor>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="banner" class="form-label">Banner Image</label>
                                            <input type="file" name="banner" accept="image/*" class="form-control"
                                                onchange="previewImage(event,'thumbnailPreview')">
                                            <div class="mt-3">
                                                <img id="thumbnailPreview" class="img-fluid"
                                                    style="max-height:200px;display:none;">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="used_at" class="form-label">Used At (Web, Shopee, Tokopedia,
                                                etc)</label>
                                            <input type="text" class="form-control" name="used_at" id="used_at"
                                                placeholder="e.g. Web, Shopee">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tnc" class="form-label">Term and Condition</label>
                                        <input type="hidden" class="form-control" name="tnc" id="tnc">
                                        <trix-editor input="tnc"></trix-editor>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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

        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', function() {
                // Hapus semua karakter kecuali angka dan titik (.)
                this.value = this.value.replace(/[^0-9.]/g, '');

                // Cegah lebih dari satu titik desimal
                const parts = this.value.split('.');
                if (parts.length > 2) {
                    this.value = parts[0] + '.' + parts[1];
                }
            });
        });

        // Menampilkan atau menyembunyikan elemen span
        document.addEventListener('DOMContentLoaded', function() {
            const amountTypeSelect = document.getElementById('amount_type');
            const maxDiscountInput = document.getElementById('max_discount');
            const suffix = document.getElementById('amount-suffix');

            function handleAmountTypeChange() {
                if (amountTypeSelect.value === 'percent') {
                    maxDiscountInput.disabled = false;
                    suffix.style.display = 'inline';
                } else {
                    maxDiscountInput.disabled = true;
                    maxDiscountInput.value = ''; // kosongkan juga kalau di-disable
                    suffix.style.display = 'none';
                }
            }

            amountTypeSelect.addEventListener('change', handleAmountTypeChange);
            handleAmountTypeChange(); // run saat load awal
        });


        // Menampilkan atau menyembunyikan content berdasarkan target audience
        document.addEventListener('DOMContentLoaded', function() {
            const audienceSelect = document.getElementById('target_audience');
            const extraFields = document.getElementById('user-extra-fields');

            function toggleExtraFields() {
                if (audienceSelect.value === 'user') {
                    extraFields.classList.remove('d-none');
                } else {
                    extraFields.classList.add('d-none');
                }
            }

            // Trigger on page load & on change
            audienceSelect.addEventListener('change', toggleExtraFields);
            toggleExtraFields(); // for preselected value
        });

        // handle products selection
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const productSelector = document.getElementById('product-selector');
            const selectedList = document.getElementById('selected-products-list');
            const hiddenInput = document.getElementById('products');

            function toggleProductSelector() {
                if (typeSelect.value === 'product') {
                    productSelector.classList.remove('d-none');
                } else {
                    productSelector.classList.add('d-none');
                    hiddenInput.value = '';
                    selectedList.innerHTML = '';
                    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = false);
                }
            }

            // Checkbox select all
            const selectAll = document.getElementById('select-all-products');
            selectAll?.addEventListener('change', function() {
                document.querySelectorAll('.product-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                    cb.dispatchEvent(new Event('change'));
                });
            });

            // Update selected product list & hidden input
            function updateSelectedProducts() {
                const selectedCheckboxes = document.querySelectorAll('.product-checkbox:checked');
                const selectedList = document.getElementById('selected-products-list');
                const hiddenInputContainer = document.getElementById('hidden-product-inputs');

                const selectedNames = [];

                // Kosongin semua input hidden yang lama
                hiddenInputContainer.innerHTML = "";

                selectedCheckboxes.forEach(cb => {
                    selectedNames.push(cb.dataset.name);

                    // Bikin hidden input baru untuk setiap produk
                    const input = document.createElement("input");
                    input.type = "hidden";
                    input.name = "products[]";
                    input.value = cb.value;
                    hiddenInputContainer.appendChild(input);
                });

                selectedList.innerHTML = selectedNames.length ?
                    selectedNames.map(name => `<li>• ${name}</li>`).join('') :
                    '<li><em>Tidak ada produk dipilih</em></li>';
            }


            // Register change event
            document.querySelectorAll('.product-checkbox').forEach(cb => {
                cb.addEventListener('change', updateSelectedProducts);
            });

            typeSelect.addEventListener('change', toggleProductSelector);
            toggleProductSelector(); // initial check
        });

        // handle users selection
        document.addEventListener('DOMContentLoaded', function() {
            const targetAudience = document.getElementById('target_audience');
            const userSelector = document.getElementById('user-selector');
            const selectedUsersList = document.getElementById('selected-users-list');
            const hiddenUserInputs = document.getElementById('hidden-user-inputs');

            function toggleUserSelector() {
                if (targetAudience.value === 'user') {
                    userSelector.classList.remove('d-none');
                } else {
                    userSelector.classList.add('d-none');
                    selectedUsersList.innerHTML = '';
                    hiddenUserInputs.innerHTML = '';
                    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
                }
            }

            document.getElementById('select-all-users')?.addEventListener('change', function() {
                document.querySelectorAll('.user-checkbox').forEach(cb => {
                    cb.checked = this.checked;
                    cb.dispatchEvent(new Event('change'));
                });
            });

            function updateSelectedUsers() {
                const selected = document.querySelectorAll('.user-checkbox:checked');
                let names = [],
                    hiddenInputs = '';

                selected.forEach(cb => {
                    names.push(`<li>• ${cb.dataset.name}</li>`);
                    hiddenInputs += `<input type="hidden" name="users[]" value="${cb.value}">`;
                });

                selectedUsersList.innerHTML = names.length ? names.join('') :
                    '<li><em>Tidak ada user dipilih</em></li>';
                hiddenUserInputs.innerHTML = hiddenInputs;
            }

            document.querySelectorAll('.user-checkbox').forEach(cb => {
                cb.addEventListener('change', updateSelectedUsers);
            });

            targetAudience.addEventListener('change', toggleUserSelector);
            toggleUserSelector();
        });

        $(document).ready(function() {
            $('#user-table').DataTable({
                responsive: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    paginate: {
                        previous: "Prev",
                        next: "Next"
                    }
                }
            });

            // Select All Users
            $('#select-all-users').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.user-checkbox').prop('checked', isChecked).trigger('change');
            });
        });
    </script>
@endpush
