<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        return Subject::all();
    }
    public function show($slug)
    {
        $subject = Subject::where('slug', $slug)
                    ->with(['topics' => function($query) {
                        $query->orderBy('order', 'asc');
                    }])
                    ->firstOrFail();

        return $subject;
    }
}