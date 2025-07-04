var Editcontacts = function() {
    var editingContactId = null;
    $(document).on('click', '.edit-contact', function() {
        editingContactId = $(this).data('id');
    });
    // jQuery Validation for Edit Contact Form
    var editContactFormValidation = function() {
        var customValid = true;
        $('#editContactForm').validate({
            ignore: [],
            rules: {
                name: {
                    required: true,
                    minlength: 2,
                    maxlength: 100
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: checkUniqueEmailForEditUrl,
                        type: "post",
                        data: {
                            _token: function() { return $('meta[name="csrf-token"]').attr('content'); },
                            email: function() { return $('#edit-email').val(); },
                            id: function() { return $('#editContactForm').attr('data-contact-id') || ''; }
                        }
                    }
                },
                phone: {
                    required: true,
                    maxlength: 20
                },
                gender: {
                    required: true
                },
                profile_image: {
                    extension: "jpg|jpeg|png|gif"
                },
                additional_file: {
                    extension: "pdf|doc|docx|txt"
                }
            },
            messages: {
                name: {
                    required: "Name is required.",
                    minlength: "Name must be at least 2 characters long.",
                    maxlength: "Name cannot exceed 100 characters."
                },
                email: {
                    required: "Email is required.",
                    email: "Please enter a valid email address.",
                    remote: "This email has already been taken."
                },
                phone: {
                    required: "Phone number is required.",
                    maxlength: "Phone number cannot exceed 20 characters."
                },
                profile_image: {
                    extension: "Please upload an image file (jpg, jpeg, png, gif)."
                },
                additional_file: {
                    extension: "Please upload a valid file (pdf, doc, docx, txt)."
                },
                gender: {
                    required: "Please select a gender."
                }
            },
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            errorElement: 'div',
            errorClass: 'invalid-feedback',
            highlight: function(element, errorClass, validClass) {
                if ($(element).attr('type') === 'radio') return;
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                if ($(element).attr('type') === 'radio') return;
                $(element).addClass('is-valid').removeClass('is-invalid');
            },
            errorPlacement: function(error, element) {
                customValid = customerInfoValid();
                if (element.attr("type") === "radio") {
                    error.insertAfter(element.closest('.form-check').parent());
                } else {
                    error.insertAfter(element);
                }
            },
            invalidHandler: function (event, validator) {
                customValid = customerInfoValid();
            },
            submitHandler: function(form) {
                customValid = customerInfoValid();
                if (customValid) {
                    if (!editingContactId) return false;
                    var submitBtn = $(form).find('button[type="submit"]');
                    var originalText = submitBtn.text();
                    submitBtn.prop('disabled', true).text('Updating...');
                    var formData = new FormData(form);
                    formData.append('_method', 'PUT');
                    $.ajax({
                        url: baseUrl + '/contacts/' + editingContactId,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message || 'Contact updated successfully!');
                                $('#editContactModal').modal('hide');
                                $('#contacts-table').DataTable().ajax.reload();
                            } else {
                                toastr.error(response.message || 'Error updating contact');
                            }
                        },
                        error: function(xhr) {
                            var errorMessage = 'An error occurred while updating the contact';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(field, messages) {
                                    var element = $('#editContactForm').find('[name="' + field + '"]');
                                    element.addClass('is-invalid');
                                    element.after('<span class="text-danger">' + messages[0] + '</span>');
                                });
                            }
                            toastr.error(errorMessage);
                        },
                        complete: function() {
                            submitBtn.prop('disabled', false).text(originalText);
                        }
                    });
                }
                return false;
            }
        });

        function customerInfoValid() {
            var customValid = true;
            $('.custom-field-select').each(function () {
                var elem = $(this);
                if ($(this).is(':visible')) {
                    if ($(this).val() == '' || $(this).val() == null) {
                        $(this).addClass('is-invalid').removeClass('is-valid');
                        $(this).parent().find('.custom-field-error').text('Please select a field');
                        customValid = false;
                    } else {
                        $(this).addClass('is-valid').removeClass('is-invalid');
                        $(this).parent().find('.custom-field-error').text('');
                    }
                }
            });
            $('.custom-field-value').each(function () {
                var elem = $(this);
                if ($(this).is(':visible')) {
                    if ($(this).val() == '' || $(this).val() == null) {
                        $(this).addClass('is-invalid').removeClass('is-valid');
                        $(this).parent().find('.custom-field-value-error').text('Please enter a value');
                        customValid = false;
                    } else {
                        $(this).addClass('is-valid').removeClass('is-invalid');
                        $(this).parent().find('.custom-field-value-error').text('');
                    }
                }
            });
            return customValid;
        }
    };

    return {
        init: function() {
            editContactFormValidation();
        }
    }
}();
