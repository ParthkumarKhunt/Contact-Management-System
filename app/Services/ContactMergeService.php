<?php
namespace App\Services;

use App\Models\Contact;
use App\Models\ContactMerge;
use Illuminate\Support\Facades\DB;

class ContactMergeService
{
    public function merge(int $masterId, int $secondaryId): void
    {
        DB::transaction(function () use ($masterId, $secondaryId) {
            $master = Contact::with('customFieldValues')->findOrFail($masterId);
            $secondary = Contact::with('customFieldValues')->findOrFail($secondaryId);

            // Merge custom fields
            foreach ($secondary->customFieldValues as $fieldValue) {
                $existing = $master->customFieldValues()
                    ->where('custom_field_id', $fieldValue->custom_field_id)
                    ->first();

                if (!$existing) {
                    $master->customFieldValues()->create([
                        'custom_field_id' => $fieldValue->custom_field_id,
                        'value' => $fieldValue->value,
                    ]);
                } elseif ($existing->value !== $fieldValue->value) {
                    $existing->value = $existing->value . "\n" . $fieldValue->value;
                    $existing->save();
                }
            }

            // Merge email & phone if different
            if ($secondary->phone && $master->phone !== $secondary->phone) {
                $master->phone .= "\n" . $secondary->phone;
            }

            if ($secondary->email && $master->email !== $secondary->email) {
                $master->email .= "\n" . $secondary->email;
            }

            $master->save();

            // Track the merge
            ContactMerge::create([
                'master_contact_id' => $master->id,
                'merged_contact_id' => $secondary->id,
            ]);

            // Soft delete secondary
            $secondary->delete();
        });
    }
}
