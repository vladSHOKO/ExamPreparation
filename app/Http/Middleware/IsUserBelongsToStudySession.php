<?php

namespace App\Http\Middleware;

use App\Models\Student;
use App\Models\TaskSession;
use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUserBelongsToStudySession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $role = $user->role;
        $taskSessionId = (int)substr($request->url(), strpos($request->url(), 'get/') + 4); //Вырезаем искомый id из url
        $taskSession = TaskSession::find($taskSessionId);
        $student = Student::find($taskSession->student_id);
        $teacher = Teacher::find($student->teacher_id);
        if ($role === 'student') {
            $isBelongs = $user->id === $student->user_id;
        } elseif ($role === 'teacher') {
            $isBelongs = $user->id === $teacher->user_id;
        } else {
            $isBelongs = false;
        }

        if (!$isBelongs) {
            return abort(403, 'У вас нет доступа к данной сессии');
        }

        return $next($request);
    }
}
