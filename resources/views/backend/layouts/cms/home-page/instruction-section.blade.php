@extends('backend.app')

@section('title', 'Home Page | Instruction Section')

@push('styles')
    <style>
        .dropify-wrapper {
            height: 285px;
        }
    </style>
@endpush

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('home-page.instruction.update') }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')
                                <div class="row gy-4">
                                    <div class="col-md-12">
                                        <div>
                                            <label for="title" class="form-label">Title:</label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                name="title" id="title" placeholder="Please Enter Title"
                                                value="{{ old('title', $instruction->title ?? '') }}">
                                            @error('title')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div>
                                            <label for="image" class="form-label">Image:</label>
                                            <input type="hidden" name="remove_image" value="0">
                                            <input class="form-control dropify @error('image') is-invalid @enderror"
                                                type="file" name="image" id="image"
                                                data-default-file="{{ $instruction->image ? asset($instruction->getRawOriginal('image')) : '' }}">
                                            @error('image')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">All Instruction List</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable"
                                    class="table table-bordered table-striped align-middle dt-responsive nowrap"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="column-id">#</th>
                                            <th class="column-content">Title</th>
                                            <th class="column-content">Description</th>
                                            <th class="column-status">Status</th>
                                            <th class="column-action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Dynamic Data --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Modal Start --}}
    <div class="modal fade" id="editFeatureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="editFeatureForm" class="modal-content" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_feature_id" name="id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Feature</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" id="edit_title" name="title" class="form-control">
                        <span class="text-danger error-text edit_title_error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="4"></textarea>
                        <span class="text-danger error-text edit_description_error"></span>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
    {{-- Edit Modal End --}}

    {{-- Modal for viewing feature details start --}}
    <div class="modal fade" id="viewFeatureModal" tabindex="-1" aria-labelledby="FeatureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="FeatureModalLabel" class="modal-title">Instruction Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Dynamic data filled by JS --}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Modal for viewing feature details end --}}
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.dropify').dropify();

            $('#image').on('dropify.afterClear', function(event, element) {
                $('input[name="remove_image"]').val('1');
            });
        });
    </script>

    <script>
        // ---------------------------------
        // 1. Keep references to CKEditor
        // ---------------------------------
        let createEditor, editEditor;

        ClassicEditor
            .create(document.querySelector('#create_description')) // for create modal
            .then(editor => {
                createEditor = editor;
            })
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('#edit_description')) // for edit modal
            .then(editor => {
                editEditor = editor;
            })
            .catch(error => {
                console.error(error);
            });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
            });

            // Add a hidden "image_url" column in your controller if you want direct file links
            // so you can set .dropify({ defaultFile: row.image_url })

            if (!$.fn.DataTable.isDataTable('#datatable')) {
                let table = $('#datatable').DataTable({
                    responsive: true,
                    order: [],
                    lengthMenu: [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"],
                    ],
                    processing: true,
                    serverSide: true,
                    pagingType: "full_numbers",
                    ajax: {
                        url: "{{ route('home-page.instruction.index') }}",
                        type: "GET",
                    },
                    dom: "<'row table-topbar'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>>" +
                        "<'row'<'col-12'tr>>" +
                        "<'row table-bottom'<'col-md-5 dataTables_left'i><'col-md-7'p>>",
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search records...",
                        lengthMenu: "Show _MENU_ entries",
                        processing: `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>`,
                    },
                    autoWidth: false,
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '5%'
                        },
                        {
                            data: 'title',
                            name: 'title',
                            orderable: true,
                            searchable: true,
                            width: '25%'
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            searchable: false,
                            width: '60%',
                            render: function(data) {
                                // Keep HTML
                                return '<div style="white-space:normal;word-break:break-word;">' +
                                    data + '</div>';
                            }
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '5%'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '5%'
                        },
                    ],
                });

                // ---------------------------------
                // 4. Show "Edit Feature" modal
                // ---------------------------------
                $(document).on('click', '.edit-feature', function() {
                    let row = table.row($(this).closest('tr')).data();

                    // Basic fields
                    $('#edit_feature_id').val(row.id);
                    $('#edit_title').val(row.title);

                    // Set CKEditor content
                    if (editEditor) {
                        editEditor.setData(row.description || '');
                    } else {
                        // fallback if needed
                        $('#edit_description').val(row.description || '');
                    }

                    $('.error-text').text('');
                    $('#editFeatureModal').modal('show');
                });

                // ---------------------------------
                // 5. Submit "Edit Feature" form
                // ---------------------------------
                const updateFeatureUrlTemplate =
                    "{{ route('home-page.instruction.update-instruction', ['id' => ':id']) }}";

                $('#editFeatureForm').submit(e => {
                    e.preventDefault();
                    $('.error-text').text('');

                    const id = $('#edit_feature_id').val();
                    const url = updateFeatureUrlTemplate.replace(':id', id);
                    const formData = new FormData(e.target);

                    axios.post(url, formData)
                        .then(({
                            data
                        }) => {
                            if (data.status) {
                                $('#editFeatureModal').modal('hide');
                                table.ajax.reload();
                                toastr.success(data.message);
                            } else {
                                for (let [field, msgs] of Object.entries(data.errors || {})) {
                                    $(`.edit_${field}_error`).text(msgs[0]);
                                }
                                toastr.error(data.message);
                            }
                        })
                        .catch(() => toastr.error('Something went wrong.'));
                });
            }
        });

        // ---------------------------------
        // 6. Utility functions
        // ---------------------------------
        async function showFeatureDetails(id) {
            let url = '{{ route('home-page.instruction.show', ['id' => ':id']) }}'.replace(':id', id);

            try {
                let response = await axios.get(url);
                if (response.data && response.data.data) {
                    let data = response.data.data;
                    let modalBody = document.querySelector('#viewFeatureModal .modal-body');
                    modalBody.innerHTML = `
                        <p><strong>Title:</strong> ${data.title}</p>
                        <p><strong>Description:</strong> ${data.description}</p>
                    `;
                } else {
                    toastr.error('No data returned from the server.');
                }
            } catch (error) {
                console.error(error);
                toastr.error('Could not fetch feature details.');
            }
        }

        function showStatusChangeAlert(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'You want to update the status?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    statusChange(id);
                }
            });
        }

        function statusChange(id) {
            let url = '{{ route('home-page.instruction.status', ['id' => ':id']) }}'.replace(':id', id);

            axios.get(url)
                .then(function(response) {
                    $('#datatable').DataTable().ajax.reload();
                    if (response.data.status === true) {
                        toastr.success(response.data.message);
                    } else if (response.data.errors) {
                        toastr.error(response.data.errors[0]);
                    } else {
                        toastr.error(response.data.message);
                    }
                })
                .catch(function(error) {
                    toastr.error('An error occurred. Please try again.');
                    console.error(error);
                });
        }

        function showDeleteConfirm(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete this record?',
                text: 'If you delete this, it will be gone forever.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(id);
                }
            });
        }
    </script>
@endpush
