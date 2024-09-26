<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Notifications\TaskStatusUpdated;
use Illuminate\Support\Facades\Notification;

use App\Models\User;
use App\Models\Task;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 
class APiMasterController extends Controller
{
    // Retrieve all tasks or user-specific tasks
    public function getTasks(Request $request)
    {
        
        $user = Auth::user();
    
        if ($user->role == 1) { // Admin
            $tasks = Task::all();
        } else { // Specific user
            $tasks = Task::where('assignedTo', $user->id)->orWhere('createdBy', $user->id)->get();
        }

        return response()->json($tasks, 200);
    }

    public function logout(Request $request)
    {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }



    // Create a new task (both admin and users can create)
    public function createTask(Request $request)
    {
       // Validate incoming request data
       $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'assigned_to' => 'nullable|exists:users,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Create a new task
    $task = Task::create([
        'title' => $request->title,
        'description' => $request->description,
        'assigned_to' => $request->assigned_to,
        'created_by' => auth()->user()->id, // Get the authenticated user ID
    ]);

    return response()->json(['success' => true, 'task' => $task], 201);
}


    // Update task status (users can update their tasks or admin-assigned tasks)
    public function updateTaskStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:0,1', // 0 = Pending, 1 = Completed
        ]);

        $task = Task::where('id', $id)
            ->where(function ($query) {
                $query->where('assignedTo', Auth::user()->id)
                      ->orWhere('createdBy', Auth::user()->id);
            })
            ->firstOrFail();

        $task->completed = $validated['status'];
        $task->save();

        return response()->json(['message' => 'Task status updated successfully', 'task' => $task], 200);
    }

    // Delete a task (only admin can delete)
    public function deleteTask($id)
    {
        $user = Auth::user();

        if ($user->role != 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }

    // Get task statistics
    public function getTaskStatistics()
    {
        $totalTasks = Task::count();
        $completedTasks = Task::where('completed', 1)->count();
        $totalUsers = User::count();

        return response()->json([
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'total_users' => $totalUsers,
        ], 200);
    }

    // Assign task to a user (admin only)
    public function assignTask(Request $request)
    {
        $user = Auth::user();

        if ($user->role != 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'assign_to' => 'required|exists:users,id',
        ]);

        $task = Task::findOrFail($validated['task_id']);
        $task->assignedTo = $validated['assign_to'];
        $task->save();

        return response()->json(['message' => 'Task assigned successfully', 'task' => $task], 200);
    }
}
