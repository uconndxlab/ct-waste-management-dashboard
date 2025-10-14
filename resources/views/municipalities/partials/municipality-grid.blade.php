@foreach($municipalities as $municipality)
    <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
        <div class="card h-100 municipality-card shadow-sm border-0" style="transition: all 0.2s ease;">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0 fw-bold text-truncate" style="flex: 1;">
                        {{ $municipality->name }}
                    </h6>
                    <div class="form-check form-switch ms-2">
                        <input class="form-check-input municipality-checkbox" 
                               type="checkbox" 
                               value="{{ $municipality->name }}" 
                               data-name="{{ $municipality->name }}" 
                               id="check-{{ $loop->index }}">
                        <label class="form-check-label visually-hidden" for="check-{{ $loop->index }}">
                            Compare
                        </label>
                    </div>
                </div>
                <div class="mt-auto">
                    <button class="btn btn-primary btn-sm w-100" 
                            hx-get="{{ route('htmx.municipalities.modal', ['name' => $municipality->name]) }}"
                            hx-target="#municipality-modal .modal-body"
                            hx-trigger="click"
                            data-municipality-name="{{ $municipality->name }}"
                            type="button">
                        <i class="bi bi-info-circle me-1"></i> View Details
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
.municipality-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.municipality-card .card-body {
    padding: 1rem;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.form-switch .form-check-input {
    width: 2em;
    height: 1em;
}
</style>