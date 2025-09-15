<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getMarks(Request $request, string $classNumber)
    {
        $students = Student::with(['user', 'teacher', 'tasks'])
            ->where('class_number', $classNumber)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => StudentResource::collection($students)
        ]);
    }
}
