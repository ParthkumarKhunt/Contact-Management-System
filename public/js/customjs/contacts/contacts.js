var Contacts = function() {
    var listContacts = function() {
        $("#contacts-table").dataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lrtip',
            "ajax": {
                url: baseUrl + "/contacts",
                type: 'GET',
                data: function(d) {
                    d.search = $('#search').val();
                    d.gender = $('#gender').val();
                    d.custom_field_search = $('#custom_field_search').val();
                }
            },
            "columns": [
                { 'title': 'S.No', "data": "sr_no", orderable: false, searchable: false },
                { 'title': 'Name', "data": "name", orderable: true, searchable: true},
                {
                    'title': 'Email',
                    "data": "email",
                    orderable: true,
                    searchable: true,
                    'render': function(data, type, row) {
                        if (type === 'display' && data) {
                            return data.replace(/\n/g, '<br>');
                        }
                        return data;
                    }
                },
                {
                    'title': 'Phone',
                    "data": "phone",
                    orderable: true,
                    searchable: true,
                    'render': function(data, type, row) {
                        if (type === 'display' && data) {
                            return data.replace(/\n/g, '<br>');
                        }
                        return data;
                    }
                },
                { 'title': 'Gender', "data": "gender", orderable: true, searchable: false},
                { 'title': 'Profile Image', "data": "profile_image", orderable: false, searchable: false,
                  'render': function(data, type, row) {
                      if (data && data !== '') {
                          return '<img src="' + data + '" alt="Profile" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">';
                      }
                      return '<img src="' + baseUrl + '/images/default-profile.png" alt="Default Profile" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">';
                  }
                },
                { 'title': 'Additional File', "data": "additional_file_url", orderable: false, searchable: false,
                  'render': function(data, type, row) {
                      if (data && row.additional_file_url) {
                          // Show a download icon linking to the file, with file name as tooltip
                          return '<a href="' + data + '" download class="btn btn-sm btn-outline-primary" title="' + row.additional_file + '"><i class="fa fa-download"></i></a>';
                      }
                      return '-';
                  }
                },
                { 'title': 'Action', "data": "actions", orderable: false, searchable: false},
            ],
            "order": [[1, 'asc']],
            "responsive": true,
            "autoWidth": false,
            "lengthMenu": [10, 25, 50, 100],
        });

        $('#add-custom-field-btn').on('click', function() {
            addCustomFieldRepeater($('#customFieldsContainer'));
        });

        function addCustomFieldRepeater(container, fieldData = null) {

            fetchCustomFields(function(customFields) {
                var selectedFieldId = fieldData ? fieldData.custom_field_id : null;
                var options = '<option value="">Select a field</option>';
                customFields.forEach(function(field) {
                    var isSelected = selectedFieldId == field.id ? 'selected' : '';
                    options += `<option value="${field.id}" ${isSelected}>${field.label}</option>`;
                });
                var repeaterRow = $(`
                    <div class="row mb-3 custom-field-repeater-row">
                        <div class="col-md-4">
                            <select class="form-select custom-field-select" name="custom_fields[]">${options}</select>
                            <p class="text-danger custom-field-error"></p>
                        </div>
                        <div class="col-md-6 custom-field-value-container">
                            <!-- Input will be generated here -->
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-custom-field-btn">Remove</button>
                        </div>
                    </div>
                `);
                container.append(repeaterRow);
            });
        }

        var availableCustomFields = [];
        var fetchedCustomFields = false;

        function fetchCustomFields(callback) {
            if (fetchedCustomFields) {
                callback(availableCustomFields);
                return;
            }
            $.ajax({
                url: baseUrl + '/contacts/get-active-custom-fields',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        availableCustomFields = response.custom_fields;
                        fetchedCustomFields = true;
                        callback(availableCustomFields);
                    } else {
                        toastr.error('Could not load custom fields.');
                    }
                },
                error: function() {
                    toastr.error('Could not load custom fields.');
                }
            });
        }

        function getCustomFieldInput(field, value = '') {
            var fieldHtml = '';
            var label = field.label || '';
            var placeholder = '';
            switch (field.type) {
                case 'email':
                    placeholder = 'Please enter your email address';
                    break;
                case 'phone':
                    placeholder = 'Please enter your phone number';
                    break;
                case 'number':
                    placeholder = 'Please enter your ' + label.toLowerCase();
                    break;
                case 'text':
                case 'textarea':
                    placeholder = 'Please enter your ' + label.toLowerCase();
                    break;
                case 'date':
                    placeholder = 'Please select your ' + label.toLowerCase();
                    break;
                case 'select':
                    placeholder = '';
                    break;
                default:
                    placeholder = label;
            }
            placeholder = placeholder ? ` placeholder="${placeholder}"` : '';
            switch (field.type) {
                case 'text':
                case 'email':
                case 'phone':
                case 'number':
                    fieldHtml = `<input type="${field.type}" class="form-control custom-field-value" name="custom_fields_value[]" value="${value}"${placeholder}>` +
                        '<p class="text-danger custom-field-value-error"></p>';
                    break;
                case 'textarea':
                    fieldHtml = `<textarea class="form-control custom-field-value" name="custom_fields_value[]" rows="3"${placeholder}>${value}</textarea>` +
                        '<p class="text-danger custom-field-value-error"></p>';
                    break;
                case 'date':
                    fieldHtml = `<input type="date" class="form-control custom-field-value" name="custom_fields_value[]" value="${value}"${placeholder}>` +
                        '<p class="text-danger custom-field-value-error"></p>';
                    break;
                case 'select':
                    var options = Array.isArray(field.options) ? field.options : [];
                    if (!Array.isArray(options) && typeof field.options === 'string') {
                        try {
                            options = JSON.parse(field.options);
                        } catch (e) {
                            options = [];
                        }
                    }
                    var optionsHtml = `<option value="">${label ? 'Select ' + label : 'Select'}</option>`;
                    options.forEach(function(option) {
                        var selected = value === option ? 'selected' : '';
                        optionsHtml += `<option value="${option}" ${selected}>${option}</option>`;
                    });
                    fieldHtml = `<select class="form-select custom-field-value" name="custom_fields_value[]">${optionsHtml}</select>` +
                        '<p class="text-danger custom-field-value-error"></p>';
                    break;
            }
            return fieldHtml;
        }

        $(document).on('change', '.custom-field-select', function() {
            var selectedFieldId = $(this).val();
            var valueContainer = $(this).closest('.custom-field-repeater-row').find('.custom-field-value-container');
            if (!selectedFieldId) {
                valueContainer.html('');
                return;
            }
            fetchCustomFields(function(customFields) {
                var selectedField = customFields.find(f => f.id == selectedFieldId);
                if (selectedField) {
                    var inputHtml = getCustomFieldInput(selectedField);
                    valueContainer.html(inputHtml);
                }
            });
        });

        $(document).on('click', '.remove-custom-field-btn', function() {
            var row = $(this).closest('.custom-field-repeater-row');
            row.remove();
        });

        var selectedContactId = null;
        // Handle View Contact
        $(document).on('click', '.view-contact', function() {
            var contactId = $(this).data('id');
            $.ajax({
                url: baseUrl + '/contacts/' + contactId,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $('#view-profile-image').attr('src', data.profile_image || (baseUrl + '/images/default-profile.png'));
                        $('#view-name').text(data.name || '-');
                        $('#view-email').text(data.email || '-');
                        $('#view-phone').text(data.phone || '-');
                        $('#view-gender').text(data.gender ? data.gender.charAt(0).toUpperCase() + data.gender.slice(1) : '-');
                        $('#view-created').text(data.created_at || '-');
                        $('#view-updated').text(data.updated_at || '-');

                        // Additional file
                        if (data.additional_file && data.additional_file_url) {
                            $('#view-additional-file').html('<a href="' + data.additional_file_url + '" target="_blank" download title="Download file"><i class="fa fa-download"></i></a>');
                            $('#view-additional-file-container').show();
                        } else {
                            $('#view-additional-file').html('');
                            $('#view-additional-file-container').hide();
                        }

                        // Custom fields
                        if (data.custom_fields && data.custom_fields.length > 0) {
                            var customFieldsHtml = '';
                            data.custom_fields.forEach(function(field) {
                                customFieldsHtml += '<div class="col-md-6 mb-2"><strong>' + field.label + ':</strong> ' + (field.value || '-') + '</div>';
                            });
                            $('#view-custom-fields').html(customFieldsHtml);
                            $('#view-custom-fields-container').show();
                        } else {
                            $('#view-custom-fields').html('');
                            $('#view-custom-fields-container').hide();
                        }

                        $('#viewContactModal').modal('show');
                    } else {
                        toastr.error(response.message || 'Could not fetch contact details.');
                    }
                },
                error: function() {
                    toastr.error('Could not fetch contact details.');
                }
            });
        });

        // Handle Delete Contact
        $(document).on('click', '.delete-contact', function() {
            selectedContactId = $(this).data('id');
            $('#deleteContactModal').modal('show');
        });

        // Confirm Delete
        $('#confirmDeleteContactBtn').on('click', function() {
            if (!selectedContactId) return;
            $.ajax({
                url: baseUrl + '/contacts/' + selectedContactId,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message || 'Contact deleted successfully!');
                        $('#contacts-table').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message || 'Could not delete contact.');
                    }
                    $('#deleteContactModal').modal('hide');
                    selectedContactId = null;
                },
                error: function() {
                    toastr.error('Could not delete contact.');
                    $('#deleteContactModal').modal('hide');
                    selectedContactId = null;
                }
            });
        });

        // Add custom field in edit modal
        $('#edit-add-custom-field-btn').on('click', function() {
            addCustomFieldRepeater($('#editCustomFieldsContainer'));
        });

        // Handle Edit Contact
        var editingContactId = null;
        $(document).on('click', '.edit-contact', function() {
            editingContactId = $(this).data('id');
            // Clear previous form and previews
            $('#editContactForm').removeAttr('data-contact-id');
            $('#editContactForm')[0].reset();
            $('#editContactForm').find('.is-valid, .is-invalid').removeClass('is-valid is-invalid');
            $('#editContactForm').find('.text-danger').remove();
            $('#edit-profile-image-preview').html('');
            $('#edit-additional-file-preview').html('');
            $('#editCustomFieldsContainer').empty();

            $.ajax({
                url: baseUrl + '/contacts/' + editingContactId + '/edit',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $('#editContactForm').attr('data-contact-id', data.id);
                        $('#edit-name').val(data.name);
                        $('#edit-email').val(data.email);
                        $('#edit-phone').val(data.phone);
                        if (data.gender === 'male') $('#edit-male').prop('checked', true);
                        else if (data.gender === 'female') $('#edit-female').prop('checked', true);
                        else if (data.gender === 'other') $('#edit-other').prop('checked', true);
                        else $('input[name="gender"]').prop('checked', false);

                        // Profile image preview
                        if (data.profile_image) {
                            $('#edit-profile-image-preview').html('<img src="' + data.profile_image + '" alt="Profile Image" class="img-fluid rounded" style="max-width: 100px;">');
                        }
                        // Additional file preview
                        if (data.additional_file) {
                            $('#edit-additional-file-preview').html('<span>' + data.additional_file + '</span>');
                        }

                        // Custom fields
                        if (data.custom_fields) {
                            fetchCustomFields(function(customFields) {
                                Object.keys(data.custom_fields).forEach(function(fieldId) {
                                    var value = data.custom_fields[fieldId];
                                    var field = customFields.find(f => f.id == fieldId);
                                    if (field) {
                                        var fieldHtml = '<div class="row mb-3 custom-field-repeater-row">';
                                        fieldHtml += '<div class="col-md-4">';
                                        fieldHtml += '<select class="form-select custom-field-select" name="custom_fields[]">';
                                        customFields.forEach(function(opt) {
                                            var selected = (opt.id == field.id) ? 'selected' : '';
                                            fieldHtml += '<option value="' + opt.id + '" ' + selected + '>' + opt.label + '</option>';
                                        });
                                        fieldHtml += '</select></div>';
                                        fieldHtml += '<div class="col-md-6 custom-field-value-container">' + getCustomFieldInput(field, value) + '</div>';
                                        fieldHtml += '<div class="col-md-2"><button type="button" class="btn btn-danger remove-custom-field-btn">Remove</button></div>';
                                        fieldHtml += '</div>';
                                        $('#editCustomFieldsContainer').append(fieldHtml);
                                    }
                                });
                            });
                        }

                        $('#editContactModal').modal('show');
                    } else {
                        toastr.error(response.message || 'Could not fetch contact details for editing.');
                    }
                },
                error: function() {
                    toastr.error('Could not fetch contact details for editing.');
                }
            });
        });

        // Add this handler to enable filter form to reload DataTable
        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            $('#contacts-table').DataTable().ajax.reload();
        });


        // Open modal and populate contacts
        $(document).on('click', '.merge-contact', function() {
            var primaryId = $(this).data('id');
            $('#primaryContactId').val(primaryId);

            // Populate secondaryContact select (excluding primary)
            $.get('/contacts', function(data) {
                var options = '';
                data.data.forEach(function(contact) {
                    if (contact.id != primaryId) {
                        options += `<option value="${contact.id}">${contact.name} (${contact.email})</option>`;
                    }
                });
                $('#secondaryContact').html(options);
                $('#mergeContactModal').modal('show');
            });
        });

        let mergePayload = {}; // Store form data temporarily

        $('#mergeContactForm').on('submit', function (e) {
            e.preventDefault();
            // Prepare data
            var primaryId = $('#primaryContactId').val();
            var secondaryId = $('#secondaryContact').val();
            mergePayload = {
                master_id: primaryId,
                secondary_id: secondaryId,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            // Show confirm modal
            $('#confirmMergeModal').modal('show');
        });

        $('#confirmMergeBtn').on('click', function () {
            // Disable button temporarily
            $(this).prop('disabled', true).text('Merging...');

            $.ajax({
                url: '/contacts/merge',
                type: 'POST',
                data: mergePayload,
                success: function (response) {
                    $('#confirmMergeModal').modal('hide');
                    $('#mergeContactModal').modal('hide');
                    $('#confirmMergeBtn').prop('disabled', false).text('Yes, Merge');

                    if (response.success) {
                        toastr.success(response.message);
                        $('#contacts-table').DataTable().ajax.reload();
                    } else {
                        toastr.error(response.message || 'Merge failed.');
                    }
                },
                error: function () {
                    $('#confirmMergeModal').modal('hide');
                    $('#confirmMergeBtn').prop('disabled', false).text('Yes, Merge');
                    toastr.error('Merge failed.');
                }
            });
        });

    }
    return {
        init: function() {
            listContacts();
        }
    }
}();
