@extends('backend.app')

@section('title', 'Home Page | Service Section')

@push('styles')
    <style>
        .dropify-wrapper {
            height: 220px;
        }

        .service-card {
            border: 1px solid #e3e6ef;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            margin-bottom: 24px;
            background: #fff;
            transition: box-shadow 0.2s;
        }

        .service-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
        }

        .service-card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e3e6ef;
            padding: 12px 20px;
            border-radius: 10px 10px 0 0;
            font-weight: 600;
            font-size: 1.1rem;
            color: #495057;
        }

        .service-card-body {
            padding: 20px;
        }

        .service-image-preview {
            max-width: 100%;
            max-height: 120px;
            margin-bottom: 10px;
            border-radius: 6px;
            border: 1px solid #e3e6ef;
            background: #f8f9fa;
        }

        .form-label {
            font-weight: 500;
        }

        .save-btn {
            min-width: 180px;
            font-size: 1rem;
            padding: 10px 0;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <form method="POST" action="{{ route('home-page.service.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="row">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="col-md-4">
                            <div class="service-card">
                                <div class="service-card-header">
                                    Service #{{ $i + 1 }}
                                </div>
                                <div class="service-card-body">
                                    <div class="mb-3">
                                        <label for="title_{{ $i }}" class="form-label">Title</label>
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
                                    <div class="mb-3">
                                        <label for="content_{{ $i }}" class="form-label">Description</label>
                                        <textarea name="contents[]" id="content_{{ $i }}"
                                            class="form-control ckeditor @error("contents.$i") is-invalid @enderror" rows="4"
                                            placeholder="Enter service description">{{ old("contents.$i", $items[$i]['description']) }}</textarea>
                                        @error("contents.$i")
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="image_{{ $i }}" class="form-label">Image</label>
                                        <input type="file" name="images[]" id="image_{{ $i }}"
                                            class="form-control dropify @error("images.$i") is-invalid @enderror"
                                            data-allowed-file-extensions="jpg jpeg png gif svg"
                                            @php
                                                $imagePath = !empty($items[$i]['image']) ? public_path($items[$i]['image']) : null;
                                            @endphp
                                            @if (!empty($items[$i]['image']) && file_exists($imagePath) && is_file($imagePath))
                                                data-default-file="{{ asset($items[$i]['image']) }}"
                                            @endif
                                            >
                                        @error("images.$i")
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary save-btn">Save Changes</button>
                </div>
            </form>
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
