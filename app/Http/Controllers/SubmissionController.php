<?php

namespace App\Http\Controllers;

use App\Models\LetterType;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function create()
    {
        $letter_types = LetterType::where('bitActive', 1)->get();
        return view('pages.submissions.create.index', compact([
            'letter_types'
        ]));
    }
}
