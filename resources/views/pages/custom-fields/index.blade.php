@extends('layouts.app')

{{-- Meta tag section --}}
@section('description', Config::get('metatags.contacts.description'))
@section('keywords', Config::get('metatags.contacts.keywords'))
@section('pagetitle', Config::get('metatags.contacts.pagetitle'))
{{-- End Meta tag section --}}
@php
    $header = array(
        'title' => 'Custom Fields',
        'icon' => 'cogs',
        'breadcrumb' => array(
            'Dashboard' => route('dashboard'),
            'Custom Fields' => 'Custom Fields',
        )
    );
@endphp
{{-- CSS section --}}
@section('css-content')
<link rel="stylesheet" href="{{ asset('plugins/dataTables/1.11.5/css/dataTables.bootstrap5.min.css') }}" />
<!--datatable responsive css-->
<link rel="stylesheet" href="{{ asset('plugins/dataTables/responsive/2.2.9/css/responsive.bootstrap.min.css') }}" />
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="card-title mb-0">

    </h5>
    <a href="javascript:;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomFieldModal">
        <i class="fas fa-plus me-1"></i>Add Custom Field
    </a>
</div>
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="custom-fields-table">

            </table>
        </div>
    </div>
</div>
<!-- Add Custom Field Modal -->
<div class="modal fade" id="addCustomFieldModal" tabindex="-1" aria-labelledby="addCustomFieldModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addCustomFieldForm" method="POST" action="{{ route('custom-fields.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="addCustomFieldModalLabel">Add Custom Field</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="cf-label" class="form-label">Label <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="cf-label" name="label"
                   placeholder="e.g., Phone Number, Birth Date"
                   maxlength="100">
            <div class="form-text">Display name for the field (max 100 characters)</div>
          </div>
          <div class="mb-3">
            <label for="cf-type" class="form-label">Type <span class="text-danger">*</span></label>
            <select class="form-select" id="cf-type" name="type">
                <option value="">Select Field Type</option>
                <option value="text">Text</option>
                <option value="email">Email</option>
                <option value="phone">Phone</option>
                <option value="date">Date</option>
                <option value="number">Number</option>
                <option value="textarea">Text Area</option>
                <option value="select">Select Dropdown</option>
            </select>
            <div class="form-text">Choose the input type for this field</div>
          </div>
          <!-- Options for Select Fields -->
          <div id="optionsSection" class="mb-3" style="display: none;">
            <label for="options" class="form-label">Options <span class="text-danger">*</span></label>
            <textarea class="form-control"
                      id="options"
                      name="options"
                      rows="5"
                      placeholder="Enter each option on a new line&#10;e.g.,&#10;Option 1&#10;Option 2&#10;Option 3"></textarea>
            <div class="form-text">Enter each option on a separate line. At least one option is required.</div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="cf-active" name="active" checked>
              <label class="form-check-label" for="cf-active">
                Active
              </label>
            </div>
            <div class="form-text">Inactive fields won't appear in contact forms</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Add Field</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Custom Field Modal -->
<div class="modal fade" id="viewCustomFieldModal" tabindex="-1" aria-labelledby="viewCustomFieldModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewCustomFieldModalLabel">Custom Field Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Label:</label>
            <p id="view-label" class="mb-0"></p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Type:</label>
            <p id="view-type" class="mb-0"></p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Status:</label>
            <p id="view-status" class="mb-0"></p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Created:</label>
            <p id="view-created" class="mb-0"></p>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Last Updated:</label>
            <p id="view-updated" class="mb-0"></p>
          </div>
          <div class="col-12 mb-3" id="view-options-container" style="display: none;">
            <label class="form-label fw-bold">Options:</label>
            <ul id="view-options" class="list-unstyled mb-0"></ul>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Custom Field Modal -->
<div class="modal fade" id="editCustomFieldModal" tabindex="-1" aria-labelledby="editCustomFieldModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editCustomFieldForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" id="edit-field-id" name="field_id">
        <div class="modal-header">
          <h5 class="modal-title" id="editCustomFieldModalLabel">Edit Custom Field</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit-cf-label" class="form-label">Label <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit-cf-label" name="label"
                   placeholder="e.g., Phone Number, Birth Date"
                   maxlength="100">
            <div class="form-text">Display name for the field (max 100 characters)</div>
          </div>
          <div class="mb-3">
            <label for="edit-cf-type" class="form-label">Type <span class="text-danger">*</span></label>
            <select class="form-select" id="edit-cf-type" name="type">
                <option value="">Select Field Type</option>
                <option value="text">Text</option>
                <option value="email">Email</option>
                <option value="phone">Phone</option>
                <option value="date">Date</option>
                <option value="number">Number</option>
                <option value="textarea">Text Area</option>
                <option value="select">Select Dropdown</option>
            </select>
            <div class="form-text">Choose the input type for this field</div>
          </div>
          <!-- Options for Select Fields -->
          <div id="editOptionsSection" class="mb-3" style="display: none;">
            <label for="edit-options" class="form-label">Options <span class="text-danger">*</span></label>
            <textarea class="form-control"
                      id="edit-options"
                      name="options"
                      rows="5"
                      placeholder="Enter each option on a new line&#10;e.g.,&#10;Option 1&#10;Option 2&#10;Option 3"></textarea>
            <div class="form-text">Enter each option on a separate line. At least one option is required.</div>
          </div>
          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" value="1" id="edit-cf-active" name="active">
              <label class="form-check-label" for="edit-cf-active">
                Active
              </label>
            </div>
            <div class="form-text">Inactive fields won't appear in contact forms</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Field</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCustomFieldModal" tabindex="-1" aria-labelledby="deleteCustomFieldModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteCustomFieldModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this custom field?</p>
        <p class="text-muted mb-0">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- JS section --}}
@section('js-content')
    <script src="{{ asset('js/customjs/custom-fields.js') }}"></script>
    <script>
        $(document).ready(function() {
            Customfields.init();
        });
    </script>
@endsection
