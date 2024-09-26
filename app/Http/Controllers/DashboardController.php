<?php

namespace App\Http\Controllers;
use App\Notifications\TaskStatusUpdated;
use Illuminate\Support\Facades\Notification;

use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 

class DashboardController extends Controller
{
    public function index(){

        $data=[];
        if (Auth::user()->role == 1) {
            // Get counts without any conditions
            $data['usercount'] = User::where('role', 2)->count();
            $data['task_all'] = Task::count(); // Count all tasks
            $data['task_completed'] = Task::where('completed', 1)->count(); // Count completed tasks
        }else {
            $data['task_all'] = Task::where(function ($query) {
                            $query->where('createdBy', Auth::user()->id)
                                ->orWhere('assignedTo', Auth::user()->id);
                        })
                        ->count(); // Count tasks created and assigned to this user
        
        // Count completed tasks that were created by the user and assigned to the user
        $data['task_completed'] = Task::where('completed', '1')
                            ->where(function ($query) {
                                $query->where('createdBy', Auth::user()->id)
                                    ->orWhere('assignedTo', Auth::user()->id);
                            })
                         ->count(); // Count completed task

        }

        return view('dashboard',$data);
    }

    public function userlist(){
        $data['userlist']=User::where('role',2)->get();
        return view('userlist',$data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // If validation fails
        if ($validator->fails()) {
            // Redirect back with validation errors and old input
            return redirect()->back()->withErrors($validator) ->withInput();
        }

        // Create a new user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => 2,
            'password' => Hash::make($request->password),
        ]);

        // Return back with success message
        return redirect()->back()->with('success', 'User created successfully!');
    }

    public function deleteuser($id)
    {
        // Find the user by ID
        $user = User::find($id);
        
        if ($user) {
            // Delete the user
            $user->delete();
            
            return redirect()->back()->with('success', 'User deleted successfully!');
        }
    
        return redirect()->back()->with('error', 'User not found!');
    }

    public function tasklist(){

       

        $data['users']=User::where('role',2)->get();

        return view('tasklist',$data);
    }





    public function taskstore(Request $request)
    {

        $userRole = Auth::user()->role;

        // Validation for the input fields
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assign_to' => ($userRole === 1) ? 'required|exists:users,id' : 'nullable', // Assign_to required for role 1, nullable for role 2
        ]);

        // Create the task
        $task = new Task();
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->createdBy = Auth::user()->id;
        $task->assignedTo = $request->input('assign_to', Auth::user()->id); // Assign to selected user or self
        $task->completed = false;
        $task->save();

        return redirect()->back()->with('success', 'Task created successfully!');
    }


    public function updateTaskStatus(Request $request)
    {
        // Validate request data

        $validator = Validator::make($request->all(), [            
            'task_id' => 'required|exists:tasks,id',
            'status' => 'required|in:0,1', // 0 for pending, 1 for completed
        ]);

         
       
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Task Id is required',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }



        try {
            // Find the task by ID
            $task = Task::findOrFail($request->task_id);

            // Update the task status
            $task->completed = 1;
            $task->save();

            // Send notification only if the task is completed
            if ($request->status == 1) {
                $user = Auth::user(); // Get the currently authenticated user
                $admin = User::where('role',1)->first(); // Get the admin user by id (id = 1)

                // Send notification to the admin
                Notification::send($admin, new TaskStatusUpdated($task, $user));
            }


            return response()->json(['success' => true, 'message' => 'Task status updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update task status.'. $e->getMessage()]);
        }
    }

    public function markNotificationAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }



    public function deleteTask(Request $request)
    {
       

        
        $validator = Validator::make($request->all(), [            
            'id' => 'required|exists:tasks,id',

        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Find the task by id
            $task = Task::findOrFail($request->id);

            // Delete the task
            $task->delete();

            return response()->json(['success' => true, 'message' => 'Task deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting task: ' . $e->getMessage()]);
        }
    }



    public function gettask_list(Request $request){

        $limit = $request->length;
        $start = $request->start;
        $order = 'tasks.id';
        $dir = 'desc';

        $search = $request->search['value'];

        $taskQuery = Task::join('users as creators', 'tasks.createdBy', '=', 'creators.id')
        ->join('users as assignees', 'tasks.assignedTo', '=', 'assignees.id')
        ->select('tasks.*', 'creators.name as creator_name', 'assignees.name as assignee_name'); // Select required fields

        // Check user role
        if (Auth::user()->role == 1) {
        // Role 1: Admin can view all tasks
        $taskQuery->when($request->user, function($q) use($request) {
        $q->where('tasks.assignedTo', $request->user);
        })
        ->when($request->status !== null, function($q) use($request) {
        $q->where('tasks.completed', $request->status);
        });
        } else {
        // Role 2: Regular user can only view their assigned tasks
        $taskQuery->where('tasks.assignedTo', Auth::user()->id)
        ->when($request->status !== null, function($q) use($request) {
        $q->where('tasks.completed', $request->status);
        });
        }
       
              

        $totalFiltered = $taskQuery->count();
       
        $task_all_data = $taskQuery->select(
            'tasks.*', 'creators.name as creator_name', 'assignees.name as assignee_name'
            )
            ->limit($limit)
            ->offset($start)
            ->orderBy($order,$dir)
            ->get()->toArray();

        foreach($task_all_data as $k=>$val){
           
            
            if (Auth::user()->role === 1){
                $actions = '<button data-id="'.$val['id'].'" data-title="'.$val['title'].'" data-description="'.$val['description'].'"  data-assignedTo="'.$val['assignedTo'].'"  class="btn btn-sm btn-primary btn-xs edittask mb-1">Edit</button> ';

                $actions .= '<button data-id='.$val['id'].' class="btn btn-sm btn-danger btn-xs delete_task  mb-1">Delete</button>';
            }elseif(Auth::user()->role === 2 &&  $val['createdBy']=== Auth::user()->id) {
                $actions = '<button data-id="'.$val['id'].'" data-title="'.$val['title'].'" data-description="'.$val['description'].'" class="btn btn-sm btn-primary btn-xs edittask mb-1">Edit</button> ';

                $actions .= '<button data-id='.$val['id'].'  class="btn btn-sm btn-danger btn-xs delete_task  mb-1">Delete</button>';
                
                if($val['completed']==0){
                    $actions .= '  <button data-id='.$val['id'].' class="btn btn-sm btn-success btn-xs  update_task mb-1">Submit Task</button>';
                }
                
            }else{
                if($val['completed']==0){
                    $actions = '<button data-id='.$val['id'].' class="btn btn-sm btn-success btn-xs update_task mb-1">Submit Task</button>';
                }else{
                    $actions='-';
                }
            }
           

            if ($val['completed'] === 1){
                $task_all_data[$k]['status'] = 'Completed';
            }elseif ($val['completed'] === 0){
                $task_all_data[$k]['status'] = 'Pending';
            }
            else {
                $task_all_data[$k]['status'] = '-';
            }

            $task_all_data[$k]['actions'] = $actions;
        }
        $json_data = array(
            "draw"            => intval($request->draw),
            "recordsTotal"    => intval($totalFiltered),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $task_all_data
        );
        return response()->json($json_data);


       
    }


    public function updateTask(Request $request)
    {

        $userRole = Auth::user()->role;
        
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|exists:tasks,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assign_to' => ($userRole === 1) ? 'required|exists:users,id' : 'nullable',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }
        try {
            // Find the task
            $task = Task::findOrFail($request->task_id);

            // Update fields
            $task->title = $request->title;
            $task->description = $request->description;

            // Update assigned user only if role is 1
            if (Auth::user()->role == 1) {
                $task->assignedTo = $request->assign_to;
            }

            $task->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }



    public function updateUser(Request $request)
    {

        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user_id,  // Email must be unique but can be the same as the current one
            'password' => 'nullable|string|min:8',  
        ]);


          // Check if validation fails
          if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422); // 422 Unprocessable Entity
        }

        try {
            // Find the user
            $user = User::findOrFail($request->user_id);

            // Update fields
            $user->name = $request->name;
            $user->email = $request->email;

            // Only update password if provided
            if ($request->password) {
                $user->password = bcrypt($request->password);
            }

            // Save the user
            $user->save();

            return response()->json(['success' => true, 'message' => 'User updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()]);
        }
    }






    




    public function adduser(){
        return view('adduser');
    }

    public function addtask(){
        
        $data['users']=User::where('role',2)->get();
        return view('addtask',$data);
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login');      
    }
}
