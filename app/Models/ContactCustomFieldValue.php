<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactCustomFieldValue extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_custom_field_values';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'contact_id',
        'custom_field_id',
        'value',
    ];

    /**
     * Get the contact that owns the custom field value.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the custom field that owns the value.
     */
    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomFields::class, 'custom_field_id');
    }

    /**
     * Get the formatted value based on field type.
     */
    public function getFormattedValueAttribute()
    {
        if (!$this->customField) {
            return $this->value;
        }

        switch ($this->customField->type) {
            case 'date':
                return $this->value ? date('M d, Y', strtotime($this->value)) : '';
            case 'select':
                return $this->value;
            case 'number':
                return is_numeric($this->value) ? number_format($this->value) : $this->value;
            default:
                return $this->value;
        }
    }
}
