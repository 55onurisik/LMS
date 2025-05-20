<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index(Request $request)
    {
        $unitId = $request->query('unit_id');
        $topics = Topic::where('unit_id', $unitId)->get();
        return response()->json(['topics' => $topics]);
    }
}
