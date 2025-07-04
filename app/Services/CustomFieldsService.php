<?php
namespace App\Services;

use App\Models\CustomFields;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomFieldsService extends CommonFunction
{

    /**
     * Get all indices.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCustomFields($request)
    {
        extract($this->DTFilters($request->all()));
        $records = [];
        $query = CustomFields::query();

        if ($search != '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%")
                  ->orWhere('type', 'like', "%{$search}%");
            });
        }
        $count = $query->count();

        $records['recordsTotal'] = $count;
        $records['recordsFiltered'] = $count;
        $records['data'] = [];

        $customFields = $query->offset($offset)->limit($limit)->orderBy($sort_column, $sort_order)->latest()->get();

        $sr_no = $offset + 1;
        foreach ($customFields as $field) {
            $records['data'][] = [
                'sr_no' => $sr_no++,
                'label' => $field->label,
                'type' => $field->type,
                'active' => $field->active,
                'actions' =>
                    '<a href="javascript:;" data-id="' . $field->id . '" class="text-primary view-custom-field me-2"><i class="fa fa-eye"></i></a>' .
                    '<a href="javascript:;" data-id="' . $field->id . '" class="text-info edit-custom-field me-2"><i class="fa fa-edit"></i></a>' .
                    '<a href="javascript:;" data-id="' . $field->id . '" class="delete-custom-field text-danger"><i class="fa fa-trash"></i></a>',
            ];
        }
        return $records;
    }

    /**
     * Create a new custom field.
     *
     * @param array $data
     * @return \App\Models\CustomFields
     */
    public function createCustomField(array $data): CustomFields
    {
        $name = Str::slug($data['label']);
        $originalName = $name;
        $counter = 1;

        while (CustomFields::where('name', $name)->exists()) {
            $name = $originalName . '-' . $counter++;
        }

        return CustomFields::create([
            'name' => $name,
            'label' => $data['label'],
            'type' => $data['type'],
            'options' => ($data['type'] === 'select' && isset($data['options'])) ? $this->formatOptions($data['options']) : null,
            'active' => isset($data['active']) ? true : false,
        ]);
    }

    /**
     * Format options from a textarea string to a JSON array.
     *
     * @param string $optionsString
     * @return string|null
     */
    protected function formatOptions(string $optionsString): ?string
    {
        $options = array_map('trim', explode("\n", $optionsString));
        $options = array_filter($options);

        if (empty($options)) {
            return null;
        }

        return json_encode($options);
    }

    /**
     * Check if the provided label is unique.
     *
     * @param string $label
     * @return bool
     */
    public function checkUniqueLabel(string $label): bool
    {
        return !CustomFields::where('label', $label)->exists();
    }

    /**
     * Get a single custom field by ID.
     *
     * @param int $id
     * @return \App\Models\CustomFields|null
     */
    public function getCustomField(int $id): ?CustomFields
    {
        return CustomFields::find($id);
    }

    /**
     * Update an existing custom field.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\CustomFields
     */
    public function updateCustomField(int $id, array $data): CustomFields
    {
        $customField = CustomFields::findOrFail($id);

        // Generate new name if label changed
        if ($data['label'] !== $customField->label) {
            $name = Str::slug($data['label']);
            $originalName = $name;
            $counter = 1;

            while (CustomFields::where('name', $name)->where('id', '!=', $id)->exists()) {
                $name = $originalName . '-' . $counter++;
            }

            $data['name'] = $name;
        }

        $customField->update([
            'name' => $data['name'] ?? $customField->name,
            'label' => $data['label'],
            'type' => $data['type'],
            'options' => ($data['type'] === 'select' && isset($data['options'])) ? $this->formatOptions($data['options']) : null,
            'active' => $data['active'] ?? $customField->active,
        ]);

        return $customField->fresh();
    }

    /**
     * Check if the provided label is unique (excluding current record).
     *
     * @param string $label
     * @param int $excludeId
     * @return bool
     */
    public function checkUniqueLabelForEdit(string $label, int $excludeId): bool
    {
        return !CustomFields::where('label', $label)->where('id', '!=', $excludeId)->exists();
    }
}
?>
