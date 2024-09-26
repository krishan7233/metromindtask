@extends('app')
@section('section')
<main id="main" class="main">

    <div class="pagetitle">
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
          <li class="breadcrumb-item">User</li>
          <li class="breadcrumb-item active">Add</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
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



        <form class="text-center" action="{{ route('user.store') }}" method="POST">

          @csrf
                <div class="row mb-3">
                  <label for="inputEmail3" class="col-sm-2 col-form-label">Your Name</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" id="inputText" name="name">
                  </div>

                  <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                  <div class="col-sm-3">
                    <input type="email" class="form-control" id="inputEmail" name="email">
                  </div>
                </div>

                <div class="row mb-3">
                    <label for="inputEmail3" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-3">
                      <input type="text" class="form-control" id="inputText" name="password">
                    </div>
                  </div>
                <div >
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
              </form>

            </div>
          </div>

      
        </div>

      
      </div>
    </section>

  </main>


@endsection