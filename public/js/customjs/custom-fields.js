var Customfields = function() {
    var listCustomFields = function() {
        $("#custom-fields-table").dataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: baseUrl + "/custom-fields",
                type: 'GET',
            },
            "columns": [
                { 'title': 'S.No', "data": "sr_no", orderable: false, searchable: false },
                { 'title': 'Label', "data": "label", orderable: true, searchable: true},
                { 'title': 'Type', "data": "type", orderable: true, searchable: true},
                { 'title': 'Status', "data": "active", orderable: true, searchable: false,
                  'render': function(data, type, row) {
                      return data ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                  }
                },
                { 'title': 'Action', "data": "actions", orderable: false, searchable: false},
            ],
            "order": [[1, 'asc']],
            "responsive": true,
            "autoWidth": false,
            "lengthMenu": [10, 25, 50, 100],
        });
    }

    var initFormValidation = function() {
        // Show/hide options section based on field type
        $('#cf-type').on('change', function() {
            const selectedType = $(this).val();
            if (selectedType === 'select') {
                $('#optionsSection').show();
                $('#options').prop('required', true);
            } else {
                $('#optionsSection').hide();
                $('#options').prop('required', false);
            }
        });

        // Initialize form validation
        $("#addCustomFieldForm").validate({
            rules: {
                label: {
                    required: true,
                    minlength: 2,
                    maxlength: 100,
                    remote: {
                        url: checkUniqueLabelUrl,
                        type: "post",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            label: function() {
                                return $("#cf-label").val();
                            }
                        }
                    }
                },
                type: {
                    required: true
                },
                options: {
                    required: function() {
                        return $('#cf-type').val() === 'select';
                    },
                    minlength: function() {
                        return $('#cf-type').val() === 'select' ? 1 : 0;
                    }
                }
            },
            messages: {
                label: {
                    required: "Field label is required.",
                    minlength: "Field label must be at least 2 characters long.",
                    maxlength: "Field label cannot exceed 100 characters.",
                    remote: "This label has already been taken."
                },
                type: {
                    required: "Please select a field type."
                },
                options: {
                    required: "Options are required for select fields.",
                    minlength: "At least one option is required for select fields."
                }
            },

            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).addClass('is-valid').removeClass('is-invalid');
            },
            errorPlacement: function(error, element) {
                if (element.attr("type") === "checkbox") {
                    error.insertAfter(element.closest('.form-check'));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                // Show loading state
                var submitBtn = $(form).find('button[type="submit"]');
                var originalText = submitBtn.text();
                submitBtn.prop('disabled', true).text('Adding...');

                // Submit form via AJAX
                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            toastr.success(response.message || 'Custom field added successfully!');

                            // Close modal
                            $('#addCustomFieldModal').modal('hide');

                            // Reset form
                            form.reset();
                            $('#addCustomFieldForm').find('.is-valid').removeClass('is-valid');

                            // Reload DataTable
                            $('#custom-fields-table').DataTable().ajax.reload();
                        } else {
                            toastr.error(response.message || 'Error adding custom field');
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'An error occurred while adding the custom field';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Handle validation errors from server
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                var element = $('[name="' + field + '"]');
                                element.addClass('is-invalid');
                                element.after('<span class="text-danger">' + messages[0] + '</span>');
                            });
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        // Reset button state
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });

                return false; // Prevent default form submission
            }
        });

        // Reset validation on modal close
        $('#addCustomFieldModal').on('hidden.bs.modal', function() {
            $('#addCustomFieldForm')[0].reset();
            $('#addCustomFieldForm').find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
            $('#addCustomFieldForm').find('.text-danger').remove();
            $('#optionsSection').hide();
            $('#options').prop('required', false);
        });
    }

    var initEditFormValidation = function() {
        // Show/hide options section based on field type for edit form
        $('#edit-cf-type').on('change', function() {
            const selectedType = $(this).val();
            if (selectedType === 'select') {
                $('#editOptionsSection').show();
                $('#edit-options').prop('required', true);
            } else {
                $('#editOptionsSection').hide();
                $('#edit-options').prop('required', false);
            }
        });

        // Initialize edit form validation
        $("#editCustomFieldForm").validate({
            rules: {
                label: {
                    required: true,
                    minlength: 2,
                    maxlength: 100,
                    remote: {
                        url: checkUniqueLabelForEditUrl,
                        type: "post",
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            label: function() {
                                return $("#edit-cf-label").val();
                            },
                            id: function() {
                                return $("#edit-field-id").val();
                            }
                        }
                    }
                },
                type: {
                    required: true
                },
                options: {
                    required: function() {
                        return $('#edit-cf-type').val() === 'select';
                    },
                    minlength: function() {
                        return $('#edit-cf-type').val() === 'select' ? 1 : 0;
                    }
                }
            },
            messages: {
                label: {
                    required: "Field label is required.",
                    minlength: "Field label must be at least 2 characters long.",
                    maxlength: "Field label cannot exceed 100 characters.",
                    remote: "This label has already been taken."
                },
                type: {
                    required: "Please select a field type."
                },
                options: {
                    required: "Options are required for select fields.",
                    minlength: "At least one option is required for select fields."
                }
            },

            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).addClass('is-valid').removeClass('is-invalid');
            },
            errorPlacement: function(error, element) {
                if (element.attr("type") === "checkbox") {
                    error.insertAfter(element.closest('.form-check'));
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                // Show loading state
                var submitBtn = $(form).find('button[type="submit"]');
                var originalText = submitBtn.text();
                submitBtn.prop('disabled', true).text('Updating...');

                // Submit form via AJAX
                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: $(form).serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            toastr.success(response.message || 'Custom field updated successfully!');

                            // Close modal
                            $('#editCustomFieldModal').modal('hide');

                            // Reset form
                            form.reset();
                            $('#editCustomFieldForm').find('.is-valid').removeClass('is-valid');

                            // Reload DataTable
                            $('#custom-fields-table').DataTable().ajax.reload();
                        } else {
                            toastr.error(response.message || 'Error updating custom field');
                        }
                    },
                    error: function(xhr) {
                        var errorMessage = 'An error occurred while updating the custom field';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            // Handle validation errors from server
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                var element = $('[name="' + field + '"]');
                                element.addClass('is-invalid');
                                element.after('<span class="text-danger">' + messages[0] + '</span>');
                            });
                        }
                        toastr.error(errorMessage);
                    },
                    complete: function() {
                        // Reset button state
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });

                return false; // Prevent default form submission
            }
        });

        // Reset validation on modal close
        $('#editCustomFieldModal').on('hidden.bs.modal', function() {
            $('#editCustomFieldForm')[0].reset();
            $('#editCustomFieldForm').find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
            $('#editCustomFieldForm').find('.text-danger').remove();
            $('#editOptionsSection').hide();
            $('#edit-options').prop('required', false);
        });
    }

    var initViewAndDeleteHandlers = function() {
        // View custom field handler
        $(document).on('click', '.view-custom-field', function() {
            var fieldId = $(this).data('id');

            // Show loading state
            $('#viewCustomFieldModal').modal('show');
            $('#viewCustomFieldModal .modal-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            // Fetch custom field details
            $.ajax({
                url: baseUrl + '/custom-fields/' + fieldId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;

                        // Populate modal with data
                        $('#view-label').text(data.label);
                        $('#view-type').text(data.type.charAt(0).toUpperCase() + data.type.slice(1));
                        $('#view-status').html(data.active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>');
                        $('#view-created').text(data.created_at);
                        $('#view-updated').text(data.updated_at);

                        // Handle options for select fields
                        if (data.type === 'select' && data.options && data.options.length > 0) {
                            $('#view-options-container').show();
                            var optionsHtml = '';
                            data.options.forEach(function(option) {
                                optionsHtml += '<li><i class="fas fa-check text-success me-2"></i>' + option + '</li>';
                            });
                            $('#view-options').html(optionsHtml);
                        } else {
                            $('#view-options-container').hide();
                        }

                        // Restore modal content
                        $('#viewCustomFieldModal .modal-body').html(`
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Label:</label>
                                    <p id="view-label" class="mb-0">${data.label}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Type:</label>
                                    <p id="view-type" class="mb-0">${data.type.charAt(0).toUpperCase() + data.type.slice(1)}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Status:</label>
                                    <p id="view-status" class="mb-0">${data.active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Created:</label>
                                    <p id="view-created" class="mb-0">${data.created_at}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Last Updated:</label>
                                    <p id="view-updated" class="mb-0">${data.updated_at}</p>
                                </div>
                                <div class="col-12 mb-3" id="view-options-container" style="${data.type === 'select' && data.options && data.options.length > 0 ? '' : 'display: none;'}">
                                    <label class="form-label fw-bold">Options:</label>
                                    <ul id="view-options" class="list-unstyled mb-0">
                                        ${data.type === 'select' && data.options ? data.options.map(option => '<li><i class="fas fa-check text-success me-2"></i>' + option + '</li>').join('') : ''}
                                    </ul>
                                </div>
                            </div>
                        `);
                    } else {
                        toastr.error('Error loading custom field details');
                        $('#viewCustomFieldModal').modal('hide');
                    }
                },
                error: function() {
                    toastr.error('Error loading custom field details');
                    $('#viewCustomFieldModal').modal('hide');
                }
            });
        });

        // Edit custom field handler
        $(document).on('click', '.edit-custom-field', function() {
            var fieldId = $(this).data('id');

            // Show loading state
            $('#editCustomFieldModal').modal('show');
            $('#editCustomFieldModal .modal-body').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            // Fetch custom field details for editing
            $.ajax({
                url: baseUrl + '/custom-fields/' + fieldId + '/edit',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;

                        // Set form action URL
                        $('#editCustomFieldForm').attr('action', baseUrl + '/custom-fields/' + fieldId);

                        // Populate form fields
                        $('#edit-field-id').val(data.id);
                        $('#edit-cf-label').val(data.label);
                        $('#edit-cf-type').val(data.type);
                        $('#edit-cf-active').prop('checked', data.active);

                        // Handle options for select fields
                        if (data.type === 'select' && data.options) {
                            $('#editOptionsSection').show();
                            $('#edit-options').prop('required', true);
                            $('#edit-options').val(data.options);
                        } else {
                            $('#editOptionsSection').hide();
                            $('#edit-options').prop('required', false);
                            $('#edit-options').val('');
                        }

                        // Restore modal content
                        $('#editCustomFieldModal .modal-body').html(`
                            <div class="mb-3">
                                <label for="edit-cf-label" class="form-label">Label <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit-cf-label" name="label" value="${data.label}" placeholder="e.g., Phone Number, Birth Date" maxlength="100">
                                <div class="form-text">Display name for the field (max 100 characters)</div>
                            </div>
                            <div class="mb-3">
                                <label for="edit-cf-type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit-cf-type" name="type">
                                    <option value="">Select Field Type</option>
                                    <option value="text" ${data.type === 'text' ? 'selected' : ''}>Text</option>
                                    <option value="email" ${data.type === 'email' ? 'selected' : ''}>Email</option>
                                    <option value="phone" ${data.type === 'phone' ? 'selected' : ''}>Phone</option>
                                    <option value="date" ${data.type === 'date' ? 'selected' : ''}>Date</option>
                                    <option value="number" ${data.type === 'number' ? 'selected' : ''}>Number</option>
                                    <option value="textarea" ${data.type === 'textarea' ? 'selected' : ''}>Text Area</option>
                                    <option value="select" ${data.type === 'select' ? 'selected' : ''}>Select Dropdown</option>
                                </select>
                                <div class="form-text">Choose the input type for this field</div>
                            </div>
                            <div id="editOptionsSection" class="mb-3" style="${data.type === 'select' ? '' : 'display: none;'}">
                                <label for="edit-options" class="form-label">Options <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="edit-options" name="options" rows="5" placeholder="Enter each option on a new line&#10;e.g.,&#10;Option 1&#10;Option 2&#10;Option 3">${data.type === 'select' ? data.options : ''}</textarea>
                                <div class="form-text">Enter each option on a separate line. At least one option is required.</div>
                            </div>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="edit-cf-active" name="active" ${data.active ? 'checked' : ''}>
                                    <label class="form-check-label" for="edit-cf-active">Active</label>
                                </div>
                                <div class="form-text">Inactive fields won't appear in contact forms</div>
                            </div>
                        `);

                        // Reinitialize validation for the restored form
                        initEditFormValidation();
                    } else {
                        toastr.error('Error loading custom field details');
                        $('#editCustomFieldModal').modal('hide');
                    }
                },
                error: function() {
                    toastr.error('Error loading custom field details');
                    $('#editCustomFieldModal').modal('hide');
                }
            });
        });

        // Delete custom field handler
        $(document).on('click', '.delete-custom-field', function() {
            var fieldId = $(this).data('id');

            // Store the field ID for deletion
            $('#deleteCustomFieldModal').data('field-id', fieldId);
            $('#deleteCustomFieldModal').modal('show');
        });

        // Confirm delete handler
        $('#confirmDeleteBtn').on('click', function() {
            var fieldId = $('#deleteCustomFieldModal').data('field-id');
            var btn = $(this);
            var originalText = btn.text();

            // Show loading state
            btn.prop('disabled', true).text('Deleting...');

            // Send delete request
            $.ajax({
                url: baseUrl + '/custom-fields/' + fieldId,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toastr.success(response.message || 'Custom field deleted successfully!');

                        // Close modal
                        $('#deleteCustomFieldModal').modal('hide');

                        // Reload DataTable
                        $('#custom-fields-table').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message || 'Error deleting custom field');
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'An error occurred while deleting the custom field';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    // Reset button state
                    btn.prop('disabled', false).text(originalText);
                }
            });
        });
    }

    return {
        init: function() {
            listCustomFields();
            initFormValidation();
            initEditFormValidation();
            initViewAndDeleteHandlers();
        }
    }
}();
