@extends('Layouts.app')
@section('title', 'Arsip Tutorials')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex">
                                <h3>Arsip Tutorials</h3>
                                <a href="{{ route('tutorials.create') }}" class="btn btn-primary ms-auto">Add Data</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="dom-jqry" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Thumbnail</th>
                                            <th>Title</th>
                                            <th>Categories</th>
                                            <th>Link</th>
                                            <th style="width: 10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tutorials as $item)
                                            <tr>
                                                <td><img src="{{ asset('storage/' . $item->thumbnail) }}" class="img-fluid"
                                                        style="max-height: 80px"></td>
                                                <td>{{ $item->title }}</td>
                                                <td>
                                                    @foreach ($item->categories as $cat)
                                                        <span class="badge bg-secondary">{{ $cat->name }}</span>
                                                    @endforeach
                                                </td>
                                                <td class="text-center"><a href="{{ $item->link }}" target="_blank"
                                                        class="badge bg-info">Watch</a></td>
                                                <td class="text-center align-middle" style="vertical-align: middle;">
                                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                                        <a href="{{ route('tutorials.edit', $item->id) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                        <form action="{{ route('tutorials.destroy', $item->id) }}"
                                                            method="POST" class="form-delete"
                                                            data-id="{{ $item->id }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="btn btn-danger btn-sm btn-delete">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
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
