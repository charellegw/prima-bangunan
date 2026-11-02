<div class="modal fade" id="{{ $modalId }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">

            <div class="modal-body p-4">

                {{-- ICON --}}
                <div class="d-flex justify-content-center mb-4">
                    <div
                        class="rounded-circle bg-danger-subtle d-flex align-items-center justify-content-center"
                        style="width:70px;height:70px;">

                        <i class="bi bi-trash3-fill text-danger fs-3"></i>

                    </div>
                </div>

                {{-- TITLE --}}
                <h4 class="fw-bold text-center mb-2">
                    {{ $title }}
                </h4>

                {{-- SUBTITLE --}}
                <p class="text-muted text-center mb-4">
                    {!! $subtitle !!}
                </p>

                <div class="d-flex gap-2">

                    <button
                        type="button"
                        class="btn btn-secondary-soft border flex-fill rounded-3"
                        data-bs-dismiss="modal">

                        Batal

                    </button>

                    <form
                        action="{{ $action }}"
                        method="POST"
                        class="flex-fill">

                        @csrf
                        @method('DELETE')

                        <button
                            class="btn btn-danger-soft w-100 rounded-3">

                            <i class="bi bi-trash3 me-1"></i>

                            {{ $buttonText ?? 'Hapus' }}

                        </button>

                    </form>

                </div>

            </div>

        </div>
    </div>
</div>