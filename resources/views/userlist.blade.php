@extends('app')
@section('section')
<main id="main" class="main">

    <div class="pagetitle">
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
          <li class="breadcrumb-item">Tables</li>
          <li class="breadcrumb-item active">User list</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
                <a class="btn btn-success align-right" href="{{route('adduser')}}">Add New User</a>
                @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            
              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>
                      Name
                    </th>
                    <th>Email</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($userlist as $val)
                    <tr>
                      <td>{{$val->name}}</td>
                      <td>{{$val->email}}</td>
                      <td><a class="btn btn-success edituser" data-id="{{$val['id']}}" data-name="{{$val['name']}}" data-email="{{$val['email']}}" >Edit</a>|<a class="btn btn-danger " href="{{route('delete.user',$val->id)}}">Delete</a></td>

                    </tr>
                  @endforeach
                 
                 
                  
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->


  <div class="modal fade" id="edituser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Horizontal Form -->
          <form  class="text-center" id="updateUserForm">
            @csrf
            <input type="hidden" name="user_id" />

            <div class="row mb-3">
              <label for="inputText" class="col-sm-3 col-form-label">Name</label>
              <div class="col-sm-9">
                <input type="text" name="name" class="form-control">
              </div>
            </div>


            <div class="row mb-3">
              <label for="inputPassword" class="col-sm-3 col-form-label">Email</label>
              <div class="col-sm-9">
                <input type="email" name="email" class="form-control">
              </div>
            </div>

        
            <div class="row mb-3">
              <label for="inputPassword" class="col-sm-3 col-form-label">Password</label>
              <div class="col-sm-9">
                <input type="text" name="password" class="form-control">
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

        $(document).on('click', '.edituser', function () {
          $('#edituser input[name="user_id"]').val($(this).data('id'));

          $('#edituser input[name="name"]').val($(this).data('name'));

          $('#edituser input[name="email"]').val($(this).data('email'));

          $('#edituser').modal('show');
        });

    $('#updateUserForm').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('user.update') }}",  // Replace with your route name
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload(); // Reload the page or redirect
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
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';

                    // Collect all validation error messages
                    $.each(errors, function(key, value) {
                        errorMessage += value + '\n';
                    });

                    Swal.fire({
                        title: 'Validation Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong, please try again.',
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