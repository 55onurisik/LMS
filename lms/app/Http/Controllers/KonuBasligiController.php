<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\KonuBasligi;
use Illuminate\Http\Request;

class KonuBasligiController extends Controller
{
    public function getTopicsByUnit($unitId)
    {
        $topics = Topic::where('unit_id', $unitId)->get();
        return response()->json(['topics' => $topics]);
    }
}
