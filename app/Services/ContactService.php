<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\ContactCustomFieldValue;
use App\Models\CustomFields;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ContactService extends CommonFunction
{
    /**
     * Get all contacts with pagination and search.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getContacts($request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $query = Contact::with(['customFieldValues.customField']);

        // Apply search filter
        if (isset($request->search) && isset($request->search) != '') {
            $query->search($request->search);
        }

        // Apply gender filter
        if (isset($request->gender) && $request->gender !== 'all') {
            $query->gender($request->gender);
        }

        // Apply custom field search filter
        if (isset($request->custom_field_search) && $request->custom_field_search != '') {
            $query->whereHas('customFieldValues', function ($q) use ($request) {
                $q->where('value', 'like', '%' . $request->custom_field_search . '%');
            });
        }

        $count = $query->count();
        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        DB::enableQueryLog();
        $contacts = $query->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->latest()->get();

        $sr_no = $offset + 1;
        foreach ($contacts as $contact) {
            $records['data'][] = [
                'id' => $contact->id,
                'sr_no' => $sr_no++,
                'name' => $contact->name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'gender' => $contact->gender ? ucfirst($contact->gender) : '-',
                'profile_image' => $contact->profile_image_url,
                'additional_file_url' => $contact->additional_file_url,
                'actions' =>
                    '<a href="javascript:;" data-id="' . $contact->id . '" class="text-primary view-contact me-2"><i class="fa fa-eye"></i></a>' .
                    '<a href="javascript:;" data-id="' . $contact->id . '" class="text-info edit-contact me-2"><i class="fa fa-edit"></i></a>' .
                    '<a href="javascript:;" data-id="' . $contact->id . '" class="delete-contact text-danger me-2"><i class="fa fa-trash"></i></a>'.
                    '<a href="javascript:;" data-id="' . $contact->id . '" class="text-warning merge-contact" ><i class="fa fa-compress"></i></a>',
            ];
        }
        return $records;
    }

    /**
     * Create a new contact.
     *
     * @param array $data
     * @return \App\Models\Contact
     */
    public function createContact(array $data): Contact
    {
        DB::beginTransaction();
        // Track new uploads for cleanup
        $uploadedProfileImage = null;
        $uploadedAdditionalFile = null;
        try {
            // Handle file uploads
            $profileImage = $this->handleFileUpload($data['profile_image'] ?? null, 'profile_images');
            $additionalFile = $this->handleFileUpload($data['additional_file'] ?? null, 'additional_files');
            if ($profileImage) {
                $uploadedProfileImage = $profileImage;
            }
            if ($additionalFile) {
                $uploadedAdditionalFile = $additionalFile;
            }
            $data['profile_image'] = $profileImage;
            $data['additional_file'] = $additionalFile;

            // Create contact
            $contact = Contact::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'profile_image' => $data['profile_image'],
                'additional_file' => $data['additional_file'],
            ]);

            // Handle custom fields
            $this->saveCustomFieldValues($contact, $data['custom_fields'] ?? [], $data['custom_fields_value'] ?? []);

            DB::commit();
            return $contact;
        } catch (\Exception $e) {
            DB::rollBack();
            // Remove uploaded files if transaction fails
            if ($uploadedProfileImage) {
                Storage::disk('public')->delete($uploadedProfileImage);
            }
            if ($uploadedAdditionalFile) {
                Storage::disk('public')->delete($uploadedAdditionalFile);
            }
            throw $e;
        }
    }

    /**
     * Update an existing contact.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Contact
     */
    public function updateContact(int $id, array $data): Contact
    {
        DB::beginTransaction();
        // Track new uploads for cleanup
        $uploadedProfileImage = null;
        $uploadedAdditionalFile = null;

        try {
            $contact = Contact::findOrFail($id);

            // Handle file uploads
            if (isset($data['profile_image']) && $data['profile_image']) {
                $newProfileImage = $this->handleFileUpload($data['profile_image'], 'profile_images', $contact->profile_image);
                if ($newProfileImage !== $contact->profile_image) {
                    $uploadedProfileImage = $newProfileImage;
                }
                $data['profile_image'] = $newProfileImage;
            }
            if (isset($data['additional_file']) && $data['additional_file']) {
                $newAdditionalFile = $this->handleFileUpload($data['additional_file'], 'additional_files', $contact->additional_file);
                if ($newAdditionalFile !== $contact->additional_file) {
                    $uploadedAdditionalFile = $newAdditionalFile;
                }
                $data['additional_file'] = $newAdditionalFile;
            }

            // Update contact
            $contact->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'profile_image' => $data['profile_image'] ?? $contact->profile_image,
                'additional_file' => $data['additional_file'] ?? $contact->additional_file,
            ]);

            // Handle custom fields
            $this->saveCustomFieldValues($contact, $data['custom_fields'] ?? [],  $data['custom_fields_value'] ?? []);

            DB::commit();
            return $contact->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            // Remove uploaded files if transaction fails
            if ($uploadedProfileImage) {
                Storage::disk('public')->delete($uploadedProfileImage);
            }
            if ($uploadedAdditionalFile) {
                Storage::disk('public')->delete($uploadedAdditionalFile);
            }
            throw $e;
        }
    }

    /**
     * Get a single contact by ID.
     *
     * @param int $id
     * @return \App\Models\Contact|null
     */
    public function getContact(int $id): ?Contact
    {
        return Contact::with(['customFieldValues.customField'])->find($id);
    }

    /**
     * Delete a contact.
     *
     * @param int $id
     * @return bool
     */
    public function deleteContact(int $id): bool
    {
        $contact = Contact::findOrFail($id);

        // Delete files
        if ($contact->profile_image) {
            Storage::disk('public')->delete($contact->profile_image);
        }
        if ($contact->additional_file) {
            Storage::disk('public')->delete($contact->additional_file);
        }

        return $contact->delete();
    }

    /**
     * Get active custom fields for forms.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCustomFields()
    {
        $fields = CustomFields::where('active', true)->orderBy('label')->get();
        foreach ($fields as $field) {
            if ($field->type === 'select') {
                if (is_string($field->options)) {
                    $field->options = $field->options ? json_decode($field->options) : [];
                }
                if (!is_array($field->options)) {
                    $field->options = [];
                }
            }
        }
        return $fields;
    }

    /**
     * Handle file upload.
     *
     * @param mixed $file
     * @param string $directory
     * @param string|null $oldFile
     * @return string|null
     */
    protected function handleFileUpload($file, string $directory, ?string $oldFile = null): ?string
    {
        if (!$file) {
            return $oldFile;
        }

        // Delete old file if exists
        if ($oldFile && Storage::disk('public')->exists($oldFile)) {
            Storage::disk('public')->delete($oldFile);
        }

        // Store new file
        if ($file && $file->isValid()) {
            return $file->store($directory, 'public');
        }

        return $oldFile;
    }

    /**
     * Save custom field values for a contact.
     *
     * @param Contact $contact
     * @param array $customFields
     * @return void
     */
    protected function saveCustomFieldValues(Contact $contact, array $customFields, array $customFieldsValue): void
    {
        // Delete existing custom field values
        $contact->customFieldValues()->delete();

        // Save new custom field values
        foreach ($customFields as $fieldId => $value) {
            ContactCustomFieldValue::create([
                'contact_id' => $contact->id,
                'custom_field_id' => $customFields[$fieldId] ?? null,
                'value' => $customFieldsValue[$fieldId] ?? null,
            ]);
        }
    }

    /**
     * Check if email is unique.
     *
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function checkUniqueEmail(string $email, ?int $excludeId = null): bool
    {
        $query = Contact::where('email', $email);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return !$query->exists();
    }

    public function mergeContacts($masterId, $secondaryId)
    {
        $master = Contact::with('customFieldValues')->findOrFail($masterId);
        $secondary = Contact::with('customFieldValues')->findOrFail($secondaryId);

        // Merge emails (comma-separated, unique)
        $masterEmails = array_map('trim', explode(',', $master->email));
        $secondaryEmails = array_map('trim', explode(',', $secondary->email));
        $allEmails = array_unique(array_merge($masterEmails, $secondaryEmails));
        $master->email = implode(', ', $allEmails);

        // Merge phones (comma-separated, unique)
        $masterPhones = array_map('trim', explode(',', $master->phone));
        $secondaryPhones = array_map('trim', explode(',', $secondary->phone));
        $allPhones = array_unique(array_merge($masterPhones, $secondaryPhones));
        $master->phone = implode(', ', $allPhones);

        // Merge custom fields
        $masterFields = $master->customFieldValues->keyBy('custom_field_id');
        foreach ($secondary->customFieldValues as $secField) {
            if (!$masterFields->has($secField->custom_field_id)) {
                // Copy missing field to master
                $secField->contact_id = $master->id;
                $secField->save();
            }
        }

        // Mark secondary as merged
        $secondary->merged_into_contact_id = $master->id;
        $secondary->save();

        $master->save();

        // Optionally, return info about what was merged for UI
        return [
            'master' => $master,
            'secondary' => $secondary,
        ];
    }
}
