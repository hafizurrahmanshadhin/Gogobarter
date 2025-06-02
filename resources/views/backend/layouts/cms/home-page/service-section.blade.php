@extends('backend.app')

@section('title', 'Home Page | Service Section')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- Breadcrumbs / Page Title Start --}}
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Service Section</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('home-page.service.index') }}">CMS</a>
                            </li>
                            <li class="breadcrumb-item">Home Page</li>
                            <li class="breadcrumb-item active">Service Section</li>
                        </ol>
                    </div>
                </div>
            </div>
            {{-- Breadcrumbs / Page Title End --}}

            {{-- Accordion Wrapper --}}
            <div id="serviceAccordion" class="accordion">
                <form method="POST" action="{{ route('home-page.service.update') }}" enctype="multipart/form-data"
                    class="row gy-4">
                    @csrf
                    @method('PATCH')

                    @for ($i = 0; $i < 3; $i++)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingService{{ $i }}">
                                <button class="accordion-button @if ($i !== 0) collapsed @endif"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseService{{ $i }}"
                                    aria-expanded="{{ $i === 0 ? 'true' : 'false' }}"
                                    aria-controls="collapseService{{ $i }}">
                                    Service Item #{{ $i + 1 }}
                                </button>
                            </h2>

                            <div id="collapseService{{ $i }}"
                                class="accordion-collapse collapse @if ($i === 0) show @endif"
                                aria-labelledby="headingService{{ $i }}" data-bs-parent="#serviceAccordion">
                                <div class="accordion-body">
                                    <div class="row gy-3">
                                        <div class="col-md-12">
                                            <label for="title_{{ $i }}" class="form-label">
                                                <strong>Title:</strong>
                                            </label>
                                            <input type="text" name="titles[]" id="title_{{ $i }}"
                                                class="form-control @error("titles.$i") is-invalid @enderror"
                                                placeholder="Enter service title"
                                                value="{{ old("titles.$i", $items[$i]['title']) }}">
                                            @error("titles.$i")
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="content_{{ $i }}" class="form-label">
                                                <strong>Content:</strong>
                                            </label>
                                            <textarea name="contents[]" id="content_{{ $i }}"
                                                class="form-control ckeditor @error("contents.$i") is-invalid @enderror" rows="4"
                                                placeholder="Enter service description">{{ old("contents.$i", $items[$i]['description']) }}</textarea>
                                            @error("contents.$i")
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="image_{{ $i }}" class="form-label">
                                                <strong>Image:</strong>
                                            </label>
                                            @php
                                                $defaultImage = $items[$i]['image']
                                                    ? asset('storage/' . $items[$i]['image'])
                                                    : null;
                                            @endphp
                                            <input type="file" name="images[]" id="image_{{ $i }}"
                                                class="form-control dropify @error("images.$i") is-invalid @enderror"
                                                data-allowed-file-extensions="jpg jpeg png gif svg" data-max-file-size="2M"
                                                @if ($defaultImage) data-default-file="{{ $defaultImage }}" @endif>
                                            @error("images.$i")
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                    <div class="col-12 text-end mt-3">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line me-1"></i> Save Service Section
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('.dropify').dropify({
                messages: {
                    'default': 'Drag and drop a file here or click',
                    'replace': 'Drag and drop or click to replace',
                    'remove': 'Remove',
                    'error': 'Oops, something went wrong.'
                },
                error: {
                    'fileSize': 'The file size is too big (Max 2MB).'
                }
            });

            document.querySelectorAll('.ckeditor').forEach((textarea) => {
                ClassicEditor
                    .create(textarea)
                    .catch(error => {
                        console.error('CKEditor initialization error:', error);
                    });
            });
        });
    </script>
@endpush
