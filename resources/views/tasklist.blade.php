@extends('app')
@section('section')
<main id="main" class="main">

    <div class="pagetitle">
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">Task list</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                <br>

               
                <div class="row mb-3 d-flex align-items-center">
                  @if (Auth::user()->role===1)
                  <div class="col-sm-3">
                      <select id="inputState" name="user" class="form-select">
                          <option value="">Choose User...</option>
                          @foreach ($users as $u)
                          <option value="{{$u->id}}">{{$u->name}}</option>
                          @endforeach
                          
                      </select>
                  </div>
                @endif
               
              
                  <div class="col-sm-3">
                      <select id="inputState" name="status" class="form-select">
                          <option value="">Choose Status...</option>
                          <option value="1">Completed</option>
                          <option value="0">Pending</option>
                      </select>
                  </div>
              
                  <div class="col-sm-3 d-flex">
                      <button id="filter" class="btn btn-primary me-2">Filter</button>
                      <button id="reset-btn" class="btn btn-secondary">Reset</button>
                  </div>
              </div>
              
 
                    <br>
                    <a class="btn btn-success align-right" href="{{route('addtask')}}">Add New Task</a>
                    <br>
              <!-- Table with stripped rows -->
              <table class="table" id="datatable">
                <br>
                <thead>
                  <tr>
                    <th>Title </th>
                    <th>Description.</th>
                    <th>Status</th>
                    <th>Assign</th>
                    <th>Created by</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->


  <div class="modal fade" id="edittask" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Horizontal Form -->
          <form  class="text-center" id="updateTaskForm">
            @csrf
            <input type="hidden" name="task_id" />

            <div class="row mb-3">
              <label for="inputText" class="col-sm-3 col-form-label">Title</label>
              <div class="col-sm-9">
                <input type="text" name="title" class="form-control">
              </div>
            </div>


            <div class="row mb-3">
              <label for="inputPassword" class="col-sm-3 col-form-label">Description</label>
              <div class="col-sm-9">
                <textarea class="form-control" name="description" style="height: 80px"></textarea>
              </div>
            </div>

           

            @if (Auth::user()->role === 1)
            <div class="row mb-3">
                <label for="assign_to" class="col-sm-3 col-form-label">Assign To</label>
                <div class="col-sm-9">
                  <select id="assign_to" class="form-select" name="assign_to">
                    <option value="">Select</option>
                    @foreach ($users as $u)
                    <option value="{{$u->id}}">{{$u->name}}</option>
                    @endforeach
                  </select>
                </div>
            </div>
            @endif
       
            <div>
              <button type="submit" class="btn btn-primary ">Update</button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
</div>


<!--update task model-->

<div class="modal fade" id="updatetask_data" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Task Status As Completed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Horizontal Form -->
        <form  class="text-center" id="updateTaskStatus">
          @csrf
          <input type="hidden" name="task_id" />

          <div class="row mb-3">
              <label for="status" class="col-sm-3 col-form-label">Select Status</label>
              <div class="col-sm-9">
                <select id="status" class="form-select" name="status">
                  <option value="">Select</option>
                  <option value="1">Completed</option>
                  
                </select>
              </div>
          </div>
     
          <div>
            <button type="submit" class="btn btn-primary ">Update</button>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


  
  <script>
  $(document).ready(function(){

      // Initialize DataTable
      var dt = $('#datatable').DataTable({
          processing: true,
          serverSide: true,
          ajax: {
              url: "{{ route('task_dt') }}",
              data: function(d) {
                  d.user = $("select[name=user]").val();
                  d.status = $("select[name=status]").val();
              }
          },
          columns:[
              {data:'title', name:'title'},
              {data:'description', name:'description'},
              {data:'status', name:'status', className:"text-center"},
              {data:'assignee_name', name:'assignee_name'},
              {data:'creator_name', name:'creator_name', className:"text-center"},
              {data:'actions', name:'actions', className:"text-center"}
          ],
          ordering: false
      });

      // Filter button action
      $('#filter').click(function(){
          dt.draw();
      });


      $(document).on('click', '.delete_task', function() {
        var taskId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('task.delete') }}",  // Replace with your route name
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: taskId
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();  // Reload the page to reflect changes
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });

      });


      
      // Reset button action
      $('#reset-btn').click(function(){
          $("select[name=user]").val('');
          $("select[name=status]").val('');
          dt.draw();
      });


    $(document).on('click', '.edittask', function () {
        var taskId = $(this).data('id');
        var taskTitle = $(this).data('title');
        var taskDescription = $(this).data('description');
        
        $('#edittask input[name="task_id"]').val(taskId);
        $('#edittask input[name="title"]').val(taskTitle);
        $('#edittask textarea[name="description"]').val(taskDescription);
        $('select[name="assign_to"]').val($(this).data('assignedto'));
        $('#edittask').modal('show');
    });

    $(document).on('click', '.update_task', function () {
        var taskId = $(this).data('id');
        
        $('#updatetask_data input[name="task_id"]').val(taskId);
        $('#updatetask_data').modal('show');
    });

    $('#updateTaskStatus').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("task.update.status") }}', // Update with your route
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                                location.reload();  // Reload the page to reflect changes
                            });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';
                $.each(errors, function(key, value) {
                    errorMessage += value + '\n';
                });
                
                Swal.fire({
                    title: 'Validation Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });

    

 

    // Handle the form submission via AJAX
    $('#updateTaskForm').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();
    
    $.ajax({
        url: "{{ route('task.update') }}", // Route for updating task
        method: "POST",
        data: formData,
        success: function (response) {
            if (response.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Task updated successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload(); // Reload the page or table after success
                    }
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function (xhr) {
            // Handle validation errors
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                let errorMessages = '';
                
                $.each(errors, function (key, value) {
                    errorMessages += value[0] + '\n'; // Display each error message
                });

                Swal.fire({
                    title: 'Validation Error!',
                    text: errorMessages,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        }
    });
});





  });
  </script>

@endsection
