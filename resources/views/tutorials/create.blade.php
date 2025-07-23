@extends('Layouts.app')
@section('title', 'Edit Articles')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('tutorials.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="link" class="form-label">Link Video</label>
                                    <input type="text" class="form-control" id="link" name="link" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Thumbnail</label>
                                        <input type="file" name="thumbnail" accept="image/*" class="form-control"
                                            onchange="previewImage(event,'thumbnailPreview')">
                                        <div class="mt-3">
                                            <img id="thumbnailPreview" class="img-fluid"
                                                style="max-height:200px;display:none;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 variant-row" data-variant-index="0">
                                        <label class="form-label">Category</label>
                                        <select name="category" class="form-select variant-category" id="category">
                                            <option disabled selected>Choose</option>
                                            @foreach ($categories as $c)
                                                <option value="{{ $c->id }}" data-name="{{ $c->name }}">
                                                    {{ $c->name }}</option>
                                            @endforeach
                                        </select>
                                        <ul class="category-list mt-2 list-unstyled mb-0"></ul>
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
            hidden.name = `categories[]`;
            hidden.value = opt.value;

            badge.appendChild(hidden);
            list.appendChild(badge);
            sel.selectedIndex = 0;
        });

        // Remove category badge on click
        document.body.addEventListener('click', e => {
            const badge = e.target.closest('li.badge');
            if (badge) badge.remove();
        });
    </script>
@endpush
