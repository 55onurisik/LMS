<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $classId = $request->query('class_id');
        $units = Unit::where('class_level', $classId)->get();
        return response()->json(['units' => $units]);
    }
    public function create(Request $request)
    {
        $classLevels = [9, 10, 11, 12];
        $allUnits = Unit::all(['id', 'class_level', 'unit_name']); // Tüm üniteleri al

        return view('admin.unit_topic.create', [
            'classLevels' => $classLevels,
            'allUnits' => $allUnits, // Tüm üniteleri view'a gönder
            'oldClassLevel' => old('class_level_topic'),
            'oldUnitId' => old('unit_id')
        ]);
    }

    public function storeUnit(Request $request)
    {
        $data = $request->validate([
            'class_level_unit' => 'required|in:9,10,11,12',
            'unit_name'        => 'required|string|max:255',
        ]);

        Unit::create([
            'class_level' => $data['class_level_unit'],
            'unit_name'   => $data['unit_name'],
        ]);

        return back()->with('success_unit', 'Ünite başarıyla eklendi!');
    }

    public function storeTopic(Request $request)
    {
        $data = $request->validate([
            'class_level_topic' => 'required|in:9,10,11,12',
            'unit_id'           => 'required|exists:units,id',
            'topic_name'        => 'required|string|max:255',
        ]);

        Topic::create([
            'unit_id'    => $data['unit_id'],
            'topic_name' => $data['topic_name'],
        ]);

        return back()->with('success_topic', 'Konu başarıyla eklendi!');
    }

    public function getUnits($class_level)
    {
        $units = Unit::where('class_level', $class_level)
            ->get(['id','unit_name']);
        dd($units);
        return response()->json($units);
    }
}
