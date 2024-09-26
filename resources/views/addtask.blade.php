@extends('app')
@section('section')
<main id="main" class="main">

    <div class="pagetitle">
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Task</li>
          <li class="breadcrumb-item active">Add</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body ">
              <h5 class="card-title">Add Form</h5>
              @if(session('success'))
              <script>
                  document.addEventListener('DOMContentLoaded', function() {
                      Swal.fire({
                          icon: 'success',
                          title: 'Success',
                          text: '{{ session('success') }}',
                      });
                  });
              </script>
          @endif
       <!-- Validation Errors -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

              <!-- Horizontal Form -->
              <form action="{{ route('task.store') }}" method="POST" class="text-center">
                @csrf
                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">Title</label>
                  <div class="col-sm-3">
                    <input type="text" name="title" class="form-control" id="inputText">
                  </div>

                  <label for="inputEmail3" class="col-sm-2 col-form-label">Description</label>
                  <div class="col-sm-3">
                    <textarea class="form-control" name="description"></textarea>
                  </div>
                </div>
                @if (Auth::user()->role === 1)
                <div class="row mb-3">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Assign To</label>
                    <div class="col-sm-3">
                      <select id="inputState" class="form-select" name="assign_to">
                        <option value="">Select</option>
                        @foreach ($users as $u)
                        <option value="{{$u->id}}">{{$u->name}}</option>

                        @endforeach
                        
                    </select>
                    </div>
                </div>
                @endif
           
                <div >
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
              </form><!-- End Horizontal Form -->

            </div>
          </div>

       
        </div>

     
      </div>
    </section>

  </main>
@endsection