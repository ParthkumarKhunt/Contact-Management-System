<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomFields;
use App\Models\Contact;

class DashboardController extends Controller
{
    public function index()
    {
        $customFieldsCount = CustomFields::count();
        $totalContactsCount = Contact::count();
        return view('pages.dashboard.index', compact('customFieldsCount', 'totalContactsCount'));
    }
}
