@extends('Layouts.app')

@section('title', 'Create Product')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="card p-4">
                    <div class="col-sm-12">
                        <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            {{-- ================== PRODUCT INFO ================== --}}
                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="product_name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input id="description" type="hidden" name="description">
                                <trix-editor input="description"></trix-editor>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Benefit</label>
                                <input id="benefit" type="hidden" name="benefit">
                                <trix-editor input="benefit"></trix-editor>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nutrition Image</label>
                                    <input type="file" name="product_nutrition" accept="image/*" class="form-control"
                                        onchange="previewImage(event,'nutritionPreview')">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <img id="nutritionPreview" class="img-fluid" style="max-height:200px;display:none;">
                            </div>

                            {{-- ================== VARIANTS ================== --}}
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Variants</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addVariantRow">+ Add
                                    Variant</button>
                            </div>

                            <div id="variantContainer">
                                {{-- TEMPLATE VARIANT (index 0) --}}
                                <template id="variantTemplate">
                                    <div class="variant-row border rounded p-3 mt-3" data-variant-index="__INDEX__">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Flavor</label>
                                                <select name="variants[__INDEX__][flavor]" class="form-select" required>
                                                    <option disabled selected>Choose</option>
                                                    @foreach ($flavours as $f)
                                                        <option value="{{ $f->id }}">{{ $f->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Categories</label>
                                                <select class="form-select variant-category">
                                                    <option disabled selected>Choose</option>
                                                    @foreach ($categories as $c)
                                                        <option value="{{ $c->id }}" data-name="{{ $c->name }}">
                                                            {{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                                <ul class="category-list mt-2 list-unstyled mb-0"></ul>
                                            </div>

                                            <div class="col-md-12">
                                                <label class="form-label">Images</label>
                                                <input type="file" name="variants[__INDEX__][images][]"
                                                    class="form-control variant-images" multiple>
                                                <div class="image-preview-container d-flex flex-wrap gap-2 mt-2"></div>
                                            </div>
                                        </div>

                                        <hr>
                                        <div class="size-group-container">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">Sizes</h6>
                                                <button type="button" class="btn btn-sm btn-outline-success add-size-row">+
                                                    Add Size</button>
                                            </div>

                                            <div class="size-row row g-3 align-items-end mt-2" data-size-index="0">
                                                <div class="col-md-2">
                                                    <label class="form-label">Size</label>
                                                    <select name="variants[__INDEX__][sizes][0][size]" class="form-select">
                                                        <option disabled selected>Choose</option>
                                                        @foreach ($sizes as $s)
                                                            <option value="{{ $s->id }}">{{ $s->label }} gr
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Price</label>
                                                    <input type="number" name="variants[__INDEX__][sizes][0][price]"
                                                        class="form-control size-price">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Discount (%)</label>
                                                    <input type="number"
                                                        name="variants[__INDEX__][sizes][0][discount_percent]"
                                                        class="form-control size-discount">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Final Price</label>
                                                    <input type="number"
                                                        name="variants[__INDEX__][sizes][0][price_discount]"
                                                        class="form-control size-price-discount">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Stock</label>
                                                    <input type="number" name="variants[__INDEX__][sizes][0][stock]"
                                                        class="form-control">
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm remove-size-row"><i
                                                            class="ti ti-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                            </div>

                            <div class="text-end">
                                <button class="btn btn-primary mt-3">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let variantIndex = 1;

        document.getElementById('addVariantRow').addEventListener('click', () => {
            const tmpl = document.getElementById('variantTemplate').innerHTML;
            const html = tmpl.replace(/__INDEX__/g, variantIndex);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            document.getElementById('variantContainer').appendChild(wrapper.firstElementChild);
            variantIndex++;
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

        document.addEventListener('DOMContentLoaded', () => {
            const variantContainer = document.getElementById('variantContainer');

            /* ---------- Delegated Clicks ---------- */
            document.body.addEventListener('click', e => {
                /* Add Size */
                if (e.target.closest('.add-size-row')) {
                    const vRow = e.target.closest('.variant-row');
                    const vIdx = vRow.dataset.variantIndex;
                    const sizeContainer = vRow.querySelector('.size-group-container');
                    const sizeRows = sizeContainer.querySelectorAll('.size-row');
                    const newIdx = sizeRows.length;

                    const firstRow = sizeRows[0];
                    const clone = firstRow.cloneNode(true);
                    clone.dataset.sizeIndex = newIdx;

                    clone.querySelectorAll('input,select').forEach(el => {
                        if (!el.name) return;
                        el.name = el.name.replace(/\[sizes]\[\d+]/, `[sizes][${newIdx}]`);
                        if (el.type === 'select-one') el.selectedIndex = 0;
                        else el.value = '';
                    });

                    sizeContainer.appendChild(clone);
                }


                /* Remove Size */
                if (e.target.classList.contains('remove-size-row') || e.target.closest(
                        '.remove-size-row')) {
                    const btn = e.target.closest('.remove-size-row');
                    const row = btn.closest('.size-row');
                    const container = row.closest('.size-group-container');
                    if (container.querySelectorAll('.size-row').length > 1) {
                        row.remove();
                    } else {
                        alert('At least one size is required.');
                    }
                }

                /* Category Badge Remove */
                if (e.target.closest('.category-list li')) {
                    e.target.closest('.category-list li').remove();
                }
            });

            /* ---------- Price Discount Calc ---------- */
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
            //     const sRow = e.target.closest('.size-row');
            //     if (!sRow) return;
            //     const price = parseFloat(sRow.querySelector('.size-price')?.value || 0);
            //     const disc = parseFloat(sRow.querySelector('.size-discount')?.value || 0);
            //     const final = sRow.querySelector('.size-price-discount');
            //     final.value = isNaN(price) ? '' : Math.floor(price - (price * disc / 100));
            // });

            /* ---------- Image Preview + Sortable ---------- */
            document.body.addEventListener('change', e => {
                if (!e.target.classList.contains('variant-images')) return;
                const input = e.target;
                const vRow = input.closest('.variant-row');
                const holder = vRow.querySelector('.image-preview-container');
                holder.innerHTML = '';

                Array.from(input.files).forEach((file, idx) => {
                    const reader = new FileReader();
                    reader.onload = ev => {
                        const wrap = document.createElement('div');
                        wrap.className = 'preview-item position-relative me-2 mb-2';
                        wrap.style = 'width:80px;height:80px;';
                        wrap.innerHTML =
                            `<img src="${ev.target.result}" class="img-thumbnail w-100 h-100" style="object-fit:cover;cursor:move;">`;
                        holder.appendChild(wrap);
                    };
                    reader.readAsDataURL(file);
                });

                if (!holder.dataset.sortableAttached) {
                    Sortable.create(holder, {
                        animation: 150,
                        ghostClass: 'bg-light',
                        draggable: '.preview-item'
                    });
                    holder.dataset.sortableAttached = 'true';
                }
            });

            /* ---------- Category Select ---------- */
            document.body.addEventListener('change', e => {
                if (!e.target.classList.contains('variant-category')) return;
                const sel = e.target;
                const opt = sel.options[sel.selectedIndex];
                if (!opt || opt.disabled) return;

                const vRow = sel.closest('.variant-row');
                const vIdx = vRow.dataset.variantIndex || 0;
                const list = vRow.querySelector('.category-list');
                if ([...list.children].some(li => li.dataset.id === opt.value)) {
                    sel.selectedIndex = 0;
                    return;
                }

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
        });
    </script>
@endpush
