<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Http\Requests\Contacts\AddContactRequest;
use App\Http\Requests\Contacts\EditContactRequest;
use App\Services\ContactService;
use Exception;

class ContactController extends Controller
{
    protected $contactService;

    public function __construct(ContactService $contactService)
    {
        $this->contactService = $contactService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            return $this->contactService->getContacts($request);
        }
        return view('pages.contacts.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddContactRequest $request)
    {
        try {
            $this->contactService->createContact($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Contact added successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $contact = $this->contactService->getContact((int) $id);

            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'gender' => $contact->gender,
                    'profile_image' => $contact->profile_image_url,
                    'additional_file' => $contact->additional_file_name,
                    'additional_file_url' => $contact->additional_file_url,
                    'created_at' => $contact->created_at->format('M d, Y H:i:s'),
                    'updated_at' => $contact->updated_at->format('M d, Y H:i:s'),
                    'custom_fields' => $contact->customFieldValues->map(function($value) {
                        return [
                            'label' => $value->customField->label,
                            'value' => $value->formatted_value,
                            'type' => $value->customField->type
                        ];
                    })
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching contact details',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $contact = $this->contactService->getContact((int) $id);


            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact not found',
                ], 404);
            }

            // Prepare custom field values
            $customFieldValues = [];
            foreach ($contact->customFieldValues as $value) {
                $customFieldValues[$value->custom_field_id] = $value->value;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'email' => $contact->email,
                    'phone' => $contact->phone,
                    'gender' => $contact->gender,
                    'profile_image' => $contact->profile_image_url,
                    'additional_file' => $contact->additional_file_name,
                    'custom_fields' => $customFieldValues
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching contact details',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditContactRequest $request, string $id)
    {
        try {
            $this->contactService->updateContact($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->contactService->deleteContact($id);

            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the contact',
            ], 500);
        }
    }

    /**
     * Check if email is unique.
     */
    public function checkUniqueEmail(Request $request)
    {
        $isUnique = $this->contactService->checkUniqueEmail($request->input('email'));
        return response()->json($isUnique);
    }

    /**
     * Check if email is unique for editing.
     */
    public function checkUniqueEmailForEdit(Request $request)
    {
        $isUnique = $this->contactService->checkUniqueEmail(
            $request->input('email'),
            $request->input('id')
        );
        return response()->json($isUnique);
    }

    /**
     * Get active custom fields.
     */
    public function getActiveCustomFields()
    {
        $customFields = $this->contactService->getActiveCustomFields();
        return response()->json([
            'success' => true,
            'custom_fields' => $customFields
        ]);
    }

}
