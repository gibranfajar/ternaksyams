@extends('Layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="card p-4">
                    <div class="col-sm-12">
                        <form action="{{ route('products.update', $product->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            {{-- ================== PRODUCT INFO ================== --}}
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="product_name"
                                    value="{{ old('product_name', $product->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input id="description" type="hidden" name="description"
                                    value="{{ old('description', $product->description) }}">
                                <trix-editor input="description"></trix-editor>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Benefit</label>
                                <input id="benefit" type="hidden" name="benefit"
                                    value="{{ old('benefit', $product->benefit) }}">
                                <trix-editor input="benefit"></trix-editor>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Nutrition Image</label>
                                    <input type="file" name="product_nutrition" accept="image/*" class="form-control"
                                        onchange="previewImage(event,'nutritionPreview')">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="active" {{ $product->status == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive" {{ $product->status == 'inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <img id="nutritionPreview" src="{{ asset('storage/' . $product->nutrition) }}"
                                    class="img-fluid mt-2"
                                    style="max-height:200px;{{ $product->nutrition ? '' : 'display:none;' }}">
                            </div>

                            {{-- ================== VARIANTS ================== --}}
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <h6>Variants</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantRow">+ Add
                                    Variant</button>
                            </div>

                            <div id="variantContainer">
                                @foreach ($product->variants as $vi => $variant)
                                    <div class="variant-row border rounded p-3 mt-3"
                                        data-variant-index="{{ $vi }}">
                                        <div class="col-md-12 text-end align-self-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-variant">Remove
                                                Variant</button>
                                        </div>
                                        <input type="hidden" name="variants[{{ $vi }}][id]"
                                            value="{{ $variant->id }}">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Flavor</label>
                                                <select name="variants[{{ $vi }}][flavor]" class="form-select"
                                                    required>
                                                    @foreach ($flavours as $f)
                                                        <option value="{{ $f->id }}"
                                                            {{ $f->id == $variant->flavour_id ? 'selected' : '' }}>
                                                            {{ $f->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Categories</label>
                                                <select class="form-select variant-category">
                                                    <option disabled selected>Choose</option>
                                                    @foreach ($categories as $c)
                                                        <option value="{{ $c->id }}"
                                                            data-name="{{ $c->name }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                                <ul class="category-list mt-2 list-unstyled mb-0">
                                                    @foreach ($variant->categories as $cat)
                                                        <li data-id="{{ $cat->id }}"
                                                            class="badge bg-secondary text-white px-2 py-1 me-1 mb-1"
                                                            style="cursor:pointer;">
                                                            {{ $cat->name }} <span class="ms-1">&times;</span>
                                                            <input type="hidden"
                                                                name="variants[{{ $vi }}][categories][]"
                                                                value="{{ $cat->id }}">
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label">Images</label>
                                                <input type="file" name="variants[{{ $vi }}][images][]"
                                                    class="form-control variant-images" multiple>
                                                <div class="image-preview-container d-flex flex-wrap gap-2 mt-2">
                                                    @foreach ($variant->images as $img)
                                                        <img src="{{ asset('storage/' . $img->image) }}"
                                                            class="img-thumbnail"
                                                            style="width:80px;height:80px;object-fit:cover;margin-right:5px;">
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="size-group-container">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6>Sizes</h6>
                                                <button type="button"
                                                    class="btn btn-sm btn-outline-success mt-2 add-size-row">+ Add
                                                    Size</button>
                                            </div>
                                            @foreach ($variant->sizes as $si => $size)
                                                <div class="size-row row g-3 align-items-end mt-2"
                                                    data-size-index="{{ $si }}">
                                                    {{-- Hidden ID --}}
                                                    <input type="hidden"
                                                        name="variants[{{ $vi }}][sizes][{{ $si }}][id]"
                                                        value="{{ $size->id }}">

                                                    {{-- Size --}}
                                                    <div class="col-md-2">
                                                        <label class="form-label">Size</label>
                                                        <select
                                                            name="variants[{{ $vi }}][sizes][{{ $si }}][size]"
                                                            class="form-select">
                                                            @foreach ($sizes as $s)
                                                                <option value="{{ $s->id }}"
                                                                    {{ $s->id == $size->size_id ? 'selected' : '' }}>
                                                                    {{ $s->label }} gr
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    {{-- Price --}}
                                                    <div class="col-md-2">
                                                        <label class="form-label">Price</label>
                                                        <input type="number"
                                                            name="variants[{{ $vi }}][sizes][{{ $si }}][price]"
                                                            class="form-control size-price" value="{{ $size->price }}">
                                                    </div>

                                                    {{-- Discount (%) --}}
                                                    <div class="col-md-2">
                                                        <label class="form-label">Discount (%)</label>
                                                        <input type="number"
                                                            name="variants[{{ $vi }}][sizes][{{ $si }}][discount_percent]"
                                                            class="form-control size-discount"
                                                            value="{{ $size->discount }}">
                                                    </div>

                                                    {{-- Final Price --}}
                                                    <div class="col-md-2">
                                                        <label class="form-label">Final Price</label>
                                                        <input type="number"
                                                            name="variants[{{ $vi }}][sizes][{{ $si }}][price_discount]"
                                                            class="form-control size-price-discount"
                                                            value="{{ $size->discount_price }}">
                                                    </div>

                                                    {{-- Stock --}}
                                                    <div class="col-md-2">
                                                        <label class="form-label">Stock</label>
                                                        <input type="number"
                                                            name="variants[{{ $vi }}][sizes][{{ $si }}][stock]"
                                                            class="form-control" value="{{ $size->quantity }}">
                                                    </div>

                                                    {{-- Remove Button --}}
                                                    <div class="col-md-2 text-end">
                                                        <button type="button"
                                                            class="btn btn-outline-danger btn-sm remove-size-row">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="text-end">
                                <button class="btn btn-primary mt-3">Update Product</button>
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
        // utilise same previewImage etc but enhanced with deletion marks
        let variantIndex = {{ $product->variants->count() }};

        document.addEventListener('DOMContentLoaded', () => {
            const variantContainer = document.getElementById('variantContainer');

            document.getElementById('addVariantRow').addEventListener('click', () => {
                const tmpl = document.querySelector('.variant-row');
                const clone = tmpl.cloneNode(true);
                clone.dataset.variantIndex = variantIndex;

                // Update semua input name
                clone.querySelectorAll('input,select,textarea').forEach(el => {
                    if (!el.name) return;

                    // Ganti variant index dan reset size index
                    el.name = el.name.replace(/variants\[\d+]/, `variants[${variantIndex}]`);
                    el.name = el.name.replace(/\[sizes\]\[\d+]/g, '[sizes][0]');

                    // Hapus field ID supaya dianggap data baru
                    if (/\[id]$/.test(el.name)) {
                        el.remove();
                        return;
                    }

                    if (el.type === 'select-one') el.selectedIndex = 0;
                    else el.value = '';
                });

                // Kosongin kategori & image preview
                clone.querySelector('.category-list').innerHTML = '';
                clone.querySelector('.image-preview-container').innerHTML = '';

                // Hapus semua size-row kecuali 1
                const sizeContainer = clone.querySelector('.size-group-container');
                const sizeRows = sizeContainer.querySelectorAll('.size-row');
                sizeRows.forEach((row, i) => {
                    if (i > 0) row.remove();
                });

                // Kosongin input di size-row yang tersisa
                const firstSize = sizeContainer.querySelector('.size-row');
                if (firstSize) {
                    firstSize.dataset.sizeIndex = 0;
                    firstSize.querySelectorAll('input,select').forEach(el => {
                        if (!el.name) return;
                        el.name = el.name.replace(/\[sizes\]\[\d+]/, '[sizes][0]');
                        if (/\[id]$/.test(el.name)) {
                            el.remove();
                            return;
                        }
                        if (el.type === 'select-one') el.selectedIndex = 0;
                        else el.value = '';
                    });
                }

                variantContainer.appendChild(clone);
                variantIndex++;
            });



            document.body.addEventListener('click', e => {
                // remove variant (mark _destroy if already persisted)
                if (e.target.closest('.remove-variant')) {
                    const row = e.target.closest('.variant-row');
                    const idInput = row.querySelector('input[name$="[id]"]');
                    if (idInput) {
                        // mark for deletion
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = idInput.name.replace('[id]', '[_destroy]');
                        input.value = '1';
                        row.appendChild(input);
                        row.style.display = 'none';
                    } else {
                        row.remove();
                    }
                }

                // add size
                if (e.target.closest('.add-size-row')) {
                    const variantRow = e.target.closest('.variant-row');
                    const sizeContainer = variantRow.querySelector('.size-group-container');
                    const sizeRows = sizeContainer.querySelectorAll('.size-row');
                    const lastRow = sizeRows[sizeRows.length - 1];
                    const newIndex = sizeRows.length;
                    const clone = lastRow.cloneNode(true);
                    clone.querySelectorAll('input,select').forEach(el => {
                        if (!el.name) return;
                        el.name = el.name.replace(/\[sizes\]\[\d+]/, `[sizes][${newIndex}]`)
                            .replace(/\[id]/, '');
                        if (el.type === 'select-one') el.selectedIndex = 0;
                        else el.value = '';
                    });
                    sizeContainer.appendChild(clone);
                }

                // remove size row
                if (e.target.classList.contains('remove-size-row')) {
                    const row = e.target.closest('.size-row');
                    const idInput = row.querySelector('input[name$="[id]"]');
                    const container = row.closest('.size-group-container');
                    const rows = container.querySelectorAll('.size-row');
                    if (rows.length > 1) {
                        if (idInput) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = idInput.name.replace('[id]', '[_destroy]');
                            input.value = '1';
                            row.appendChild(input);
                            row.style.display = 'none';
                        } else {
                            row.remove();
                        }
                    } else {
                        alert('At least one size is required.');
                    }
                }

                // remove category badge
                if (e.target.closest('.category-list li')) {
                    const badge = e.target.closest('.category-list li');
                    badge.remove();
                }
            });

            // price discount calc
            let lastEditedField = null;

            document.body.addEventListener('input', e => {
                const input = e.target;
                const row = input.closest('.size-row');
                if (!row) return;

                const priceInput = row.querySelector('.size-price');
                const discountInput = row.querySelector('.size-discount');
                const finalPriceInput = row.querySelector('.size-price-discount');

                if ([priceInput, discountInput, finalPriceInput].includes(input)) {
                    lastEditedField = input;

                    const price = parseFloat(priceInput.value || 0);
                    const discount = parseFloat(discountInput.value || 0);
                    const finalPrice = parseFloat(finalPriceInput.value || 0);

                    if (lastEditedField === discountInput || lastEditedField === priceInput) {
                        if (!isNaN(price) && !isNaN(discount)) {
                            finalPriceInput.value = Math.floor(price - (price * discount / 100));
                        }
                    } else if (lastEditedField === finalPriceInput && !isNaN(price) && price > 0) {
                        discountInput.value = Math.round(((price - finalPrice) / price) * 100);
                    }
                }
            });
            // document.body.addEventListener('input', e => {
            //     const row = e.target.closest('.size-row');
            //     if (!row) return;
            //     const price = parseFloat(row.querySelector('.size-price')?.value || 0);
            //     const disc = parseFloat(row.querySelector('.size-discount')?.value || 0);
            //     const final = row.querySelector('.size-price-discount');
            //     if (final) {
            //         const result = price - (price * disc / 100);
            //         final.value = isNaN(result) ? '' : Math.floor(result);
            //     }
            // });

            // preview images new uploads
            document.body.addEventListener('change', e => {
                if (!e.target.classList.contains('variant-images')) return;
                const input = e.target;
                const row = input.closest('.variant-row');
                const holder = row.querySelector('.image-preview-container');
                holder.innerHTML = '';

                const files = Array.from(input.files);

                files.forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = ev => {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'preview-item position-relative me-2 mb-2';
                        wrapper.style = 'width:80px;height:80px;';

                        const img = document.createElement('img');
                        Object.assign(img, {
                            src: ev.target.result,
                            className: 'img-thumbnail w-100 h-100',
                            style: 'object-fit:cover;cursor:move;'
                        });

                        // Optional: inject index as data
                        wrapper.dataset.index = index;
                        wrapper.appendChild(img);
                        holder.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });

                // Make preview sortable
                if (!holder.dataset.sortableAttached) {
                    Sortable.create(holder, {
                        animation: 150,
                        ghostClass: 'bg-light',
                        draggable: '.preview-item',
                        onEnd: function( /**Event*/ evt) {
                            console.log('Image order changed');
                        }
                    });
                    holder.dataset.sortableAttached = 'true';
                }
            });
        });

        document.body.addEventListener('change', e => {
            if (!e.target.classList.contains('variant-category')) return;

            const sel = e.target;
            const opt = sel.options[sel.selectedIndex];
            if (!opt || opt.disabled) return;

            const vRow = sel.closest('.variant-row');
            const vIdx = vRow.dataset.variantIndex || 0;
            const list = vRow.querySelector('.category-list');

            // ❗ Cek apakah kategori sudah dipilih
            if ([...list.children].some(li => li.dataset.id === opt.value)) {
                sel.selectedIndex = 0; // Reset dropdown
                return;
            }

            // 🔽 Tambahkan badge
            const badge = document.createElement('li');
            badge.dataset.id = opt.value;
            badge.className = 'badge bg-secondary text-white px-2 py-1 me-1 mb-1';
            badge.style.cursor = 'pointer';
            badge.innerHTML = `${opt.dataset.name} <span class='ms-1'>&times;</span>`;

            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = `variants[${vIdx}][categories][]`;
            hidden.value = opt.value;
            badge.appendChild(hidden);
            list.appendChild(badge);

            sel.selectedIndex = 0;
        });


        function previewImage(evt, id) {
            const r = new FileReader();
            r.onload = () => {
                const img = document.getElementById(id);
                img.src = r.result;
                img.style.display = 'block';
            };
            r.readAsDataURL(evt.target.files[0]);
        }
    </script>
@endpush
