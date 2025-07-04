<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomFields;
use App\Http\Requests\CustomFields\AddCustomFieldsRequest;
use App\Http\Requests\CustomFields\EditCustomFieldsRequest;
use Exception;
use App\Services\CustomFieldsService;

class CustomFieldController extends Controller
{
    protected $customFieldsService;

    public function __construct(CustomFieldsService $customFieldsService)
    {
        $this->customFieldsService = $customFieldsService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            return $this->customFieldsService->getCustomFields($request);
        }
        return view('pages.custom-fields.index');
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
    public function store(AddCustomFieldsRequest $request)
    {
        try {
            $this->customFieldsService->createCustomField($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Custom field added successfully!',
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
            $customField = CustomFields::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $customField->id,
                    'label' => $customField->label,
                    'type' => $customField->type,
                    'options' => $customField->options ? json_decode($customField->options) : null,
                    'active' => $customField->active,
                    'created_at' => $customField->created_at->format('M d, Y H:i:s'),
                    'updated_at' => $customField->updated_at->format('M d, Y H:i:s'),
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Custom field not found',
            ], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $customField = $this->customFieldsService->getCustomField($id);

            if (!$customField) {
                return response()->json([
                    'success' => false,
                    'message' => 'Custom field not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $customField->id,
                    'label' => $customField->label,
                    'type' => $customField->type,
                    'options' => $customField->options ? implode("\n", json_decode($customField->options)) : '',
                    'active' => $customField->active,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching custom field details',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditCustomFieldsRequest $request, string $id)
    {
        try {
            $this->customFieldsService->updateCustomField($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Custom field updated successfully!',
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
            $customField = CustomFields::findOrFail($id);
            $customField->delete();

            return response()->json([
                'success' => true,
                'message' => 'Custom field deleted successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the custom field',
            ], 500);
        }
    }

    public function checkUniqueLabel(Request $request)
    {
        $isUnique = $this->customFieldsService->checkUniqueLabel($request->input('label'));
        return response()->json($isUnique);
    }

    public function checkUniqueLabelForEdit(Request $request)
    {
        $isUnique = $this->customFieldsService->checkUniqueLabelForEdit(
            $request->input('label'),
            $request->input('id')
        );
        return response()->json($isUnique);
    }
}
