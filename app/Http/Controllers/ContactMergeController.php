<?php

namespace App\Http\Controllers;

use App\Http\Requests\MergeContacts\MergeContactsRequest;
use App\Services\ContactMergeService;

class ContactMergeController extends Controller
{
    protected $contactMergeService;

    public function __construct(ContactMergeService $contactMergeService)
    {
        $this->contactMergeService = $contactMergeService;
    }

    public function merge(MergeContactsRequest $request)
    {
        try {
            $this->contactMergeService->merge(
                $request->input('master_id'),
                $request->input('secondary_id')
            );

            return response()->json([
                'success' => true,
                'message' => 'Contacts merged successfully.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Merge failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}

