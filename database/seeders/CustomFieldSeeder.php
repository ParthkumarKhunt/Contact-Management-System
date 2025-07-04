<?php

namespace Database\Seeders;

use App\Models\CustomFields;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customFields = [
            [
                'name' => 'birthday',
                'label' => 'Birthday',
                'type' => 'date',
                'active' => true,
            ],
            [
                'name' => 'company_name',
                'label' => 'Company Name',
                'type' => 'text',
                'active' => true,
            ],
            [
                'name' => 'job_title',
                'label' => 'Job Title',
                'type' => 'text',
                'active' => true,
            ],
            [
                'name' => 'address',
                'label' => 'Address',
                'type' => 'textarea',
                'active' => true,
            ],
            [
                'name' => 'website',
                'label' => 'Website',
                'type' => 'text',
                'active' => true,
            ],
            [
                'name' => 'department',
                'label' => 'Department',
                'type' => 'select',
                'options' => json_encode(['Sales', 'Marketing', 'Engineering', 'HR', 'Finance', 'Operations']),
                'active' => true,
            ],
            [
                'name' => 'emergency_contact',
                'label' => 'Emergency Contact',
                'type' => 'phone',
                'active' => true,
            ],
            [
                'name' => 'notes',
                'label' => 'Notes',
                'type' => 'textarea',
                'active' => true,
            ],
        ];

        foreach ($customFields as $field) {
            CustomFields::create($field);
        }
    }
}
