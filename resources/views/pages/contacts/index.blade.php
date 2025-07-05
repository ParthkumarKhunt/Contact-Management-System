@extends('layouts.app')

{{-- Meta tag section --}}
@section('description', Config::get('metatags.contacts.description'))
@section('keywords', Config::get('metatags.contacts.keywords'))
@section('pagetitle', Config::get('metatags.contacts.pagetitle'))
{{-- End Meta tag section --}}
@php
    $header = array(
        'title' => 'Contacts',
        'icon' => 'address-book',
        'breadcrumb' => array(
            'Dashboard' => route('dashboard'),
            'Contacts' => 'Contacts',
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
<!-- Search and Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form id="searchForm" method="GET" action="{{ route('contacts.index') }}">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           placeholder="Search by name, email, or phone..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="all" {{ request('gender') == 'all' ? 'selected' : '' }}>All Genders</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="custom_field_search" class="form-label">Custom Field Search</label>
                    <input type="text" class="form-control" id="custom_field_search" name="custom_field_search"
                           placeholder="Search custom fields..."
                           value="{{ request('custom_field_search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>
                        Search
                    </button>
                    <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="card-title mb-0">
        Contact List
    </h5>
    <a href="javascript:;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
        <i class="fas fa-plus me-1"></i>Add Contact
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="contacts-table">

            </table>
        </div>
    </div>
</div>

<!-- Add Contact Modal -->
<div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">


        <form id="addContactForm" method="POST" action="{{ route('contacts.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="modal-header">
            <h5 class="modal-title" id="addContactModalLabel">Add Contact</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="row">
                <!-- Standard Fields -->
                <div class="col-12">
                <h6 class="mb-3">Standard Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Enter full name">
                    </div>
                    <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Enter email address">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Enter phone number">
                    </div>
                    <div class="col-md-6 mb-3">
                    <label class="form-label">Gender</label>
                    <div class="d-flex flex-row gap-3">
                        <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="male">
                        <label class="form-check-label" for="male">Male</label>
                        </div>
                        <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                        <label class="form-check-label" for="female">Female</label>
                        </div>
                        <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="other" value="other">
                        <label class="form-check-label" for="other">Other</label>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
                <hr class="my-3">
                <!-- File Uploads -->
                <div class="col-12">
                <h6 class="mb-3">Files</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                    <label for="profile_image" class="form-label">Profile Image</label>
                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                    <div class="form-text">Formats: JPEG, PNG, JPG, GIF</div>
                    </div>
                    <div class="col-md-6 mb-3">
                    <label for="additional_file" class="form-label">Additional File</label>
                    <input type="file" class="form-control" id="additional_file" name="additional_file" accept=".pdf,application/pdf,.doc,application/msword,.docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document,.txt,text/plain">
                    <div class="form-text">Formats: PDF, DOC, DOCX, TXT</div>
                    </div>
                </div>
                </div>
                <hr class="my-3">
            </div>

            <!-- Custom Fields Section -->
            <div id="customFieldsSection">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="m-0">Custom Fields</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="add-custom-field-btn">
                        <i class="fas fa-plus"></i> Add Field
                    </button>
                </div>
                <div id="customFieldsContainer">
                <!-- Custom fields will be dynamically loaded here -->
                </div>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Contact</button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- Edit Contact Modal -->
<div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editContactForm" method="POST" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title" id="editContactModalLabel">Edit Contact</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Standard Fields -->
            <div class="col-12">
              <h6 class="mb-3">Standard Information</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="edit-name" class="form-label">Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="edit-name" name="name" required placeholder="Enter full name">
                </div>
                <div class="col-md-6 mb-3">
                  <label for="edit-email" class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="edit-email" name="email" required placeholder="Enter email address">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="edit-phone" class="form-label">Phone</label>
                  <input type="text" class="form-control" id="edit-phone" name="phone" placeholder="Enter phone number">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Gender</label>
                  <div class="d-flex flex-row gap-3">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="gender" id="edit-male" value="male">
                      <label class="form-check-label" for="edit-male">Male</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="gender" id="edit-female" value="female">
                      <label class="form-check-label" for="edit-female">Female</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="gender" id="edit-other" value="other">
                      <label class="form-check-label" for="edit-other">Other</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <hr class="my-3">
            <!-- File Uploads -->
            <div class="col-12">
              <h6 class="mb-3">Files</h6>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="edit-profile_image" class="form-label">Profile Image</label>
                  <input type="file" class="form-control" id="edit-profile_image" name="profile_image" accept="image/*">
                  <div class="form-text">Formats: JPEG, PNG, JPG, GIF</div>
                  <div id="edit-profile-image-preview" class="mt-2"></div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="edit-additional_file" class="form-label">Additional File</label>
                  <input type="file" class="form-control" id="edit-additional_file" name="additional_file" accept=".pdf,application/pdf,.doc,application/msword,.docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document,.txt,text/plain">
                  <div class="form-text">Formats: PDF, DOC, DOCX, TXT</div>
                  <div id="edit-additional-file-preview" class="mt-2"></div>
                </div>
              </div>
            </div>
            <hr class="my-3">
          </div>
          <!-- Custom Fields Section -->
          <div id="editCustomFieldsSection">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="m-0">Custom Fields</h6>
              <button type="button" class="btn btn-sm btn-primary" id="edit-add-custom-field-btn">
                <i class="fas fa-plus"></i> Add Field
              </button>
            </div>
            <div id="editCustomFieldsContainer">
              <!-- Custom fields will be dynamically loaded here -->
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Contact</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Contact Modal -->
<div class="modal fade" id="viewContactModal" tabindex="-1" aria-labelledby="viewContactModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewContactModalLabel">Contact Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4 text-center mb-3">
            <img id="view-profile-image" src="" alt="Profile Image" class="img-fluid rounded" style="max-width: 200px;">
          </div>
          <div class="col-md-8">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Name:</label>
                <p id="view-name" class="mb-0"></p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Email:</label>
                <p id="view-email" class="mb-0"></p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Phone:</label>
                <p id="view-phone" class="mb-0"></p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Gender:</label>
                <p id="view-gender" class="mb-0"></p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Created:</label>
                <p id="view-created" class="mb-0"></p>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Last Updated:</label>
                <p id="view-updated" class="mb-0"></p>
              </div>
              <div class="col-12 mb-3" id="view-additional-file-container" style="display: none;">
                <label class="form-label fw-bold">Additional File:</label>
                <p id="view-additional-file" class="mb-0"></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Custom Fields Display -->
        <div id="view-custom-fields-container" style="display: none;">
          <h6 class="mb-3 mt-4">Custom Fields</h6>
          <div id="view-custom-fields" class="row">
            <!-- Custom fields will be displayed here -->
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="mergeContactModal" tabindex="-1" aria-labelledby="mergeContactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="mergeContactForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="mergeContactModalLabel">Merge Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="primaryContactId" name="primaryContactId">
                    <div class="mb-3">
                        <label for="secondaryContact" class="form-label">Select contact to merge with:</label>
                        <select class="form-select" id="secondaryContact" name="secondaryContact" required>
                        <!-- Populate with contacts via JS -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Merge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Merge Modal -->
<div class="modal fade" id="confirmMergeModal" tabindex="-1" aria-labelledby="confirmMergeLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="confirmMergeLabel">Confirm Merge</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to merge these contacts?</p>
            <p class="text-muted mb-0">This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="confirmMergeBtn" class="btn btn-danger">Yes, Merge</button>
        </div>
      </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteContactModal" tabindex="-1" aria-labelledby="deleteContactModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteContactModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you sure you want to delete this contact?</p>
        <p class="text-muted mb-0">This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteContactBtn">Yes, Delete</button>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- JS section --}}
@section('js-content')
    <script src="{{ asset('js/customjs/contacts/contacts.js') }}"></script>
    <script src="{{ asset('js/customjs/contacts/add-contacts.js') }}"></script>
    <script src="{{ asset('js/customjs/contacts/edit-contacts.js') }}"></script>
    <script>
        $(document).ready(function() {
            Contacts.init();
            Addcontacts.init();
            Editcontacts.init();
        });
    </script>
@endsection
