@extends('admin.admin_dashboard')

@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<div class="page-content">
    <div class="row profile-body">
        <!-- left wrapper start -->
        <div class="d-none d-md-block col-md-4 col-xl-4 left-wrapper">
            <div class="card rounded">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <img class="wd-100 rounded-circle" src="{{ !empty($profileData->photo) ? url('upload/images/admin/'.$profileData->photo) : url('upload/images/admin/no_image.jpg') }}" alt="profile">
                            <span class="h4 ms-3">{{ $profileData->username }}</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="tx-11 fw-bolder mb-0 text-uppercase">Name:</label>
                        <p class="text-muted">{{ $profileData->name }}</p>
                    </div>
                    <div class="mt-3">
                        <label class="tx-11 fw-bolder mb-0 text-uppercase">Email:</label>
                        <p class="text-muted">{{ $profileData->email }}</p>
                    </div>
                    <div class="mt-3">
                        <label class="tx-11 fw-bolder mb-0 text-uppercase">Phone:</label>
                        <p class="text-muted">{{ $profileData->phone }}</p>
                    </div>
                    <div class="mt-3">
                        <label class="tx-11 fw-bolder mb-0 text-uppercase">Address:</label>
                        <p class="text-muted">{{ $profileData->address }}</p>
                    </div>
                    <div class="mt-3 d-flex social-links">
                        <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
                            <i data-feather="github"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
                            <i data-feather="twitter"></i>
                        </a>
                        <a href="javascript:;" class="btn btn-icon border btn-xs me-2">
                            <i data-feather="instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- left wrapper end -->
        <!-- middle wrapper start -->
        <div class="col-md-8 col-xl-8 middle-wrapper">
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Update Admin Profile</h6>
                        <form method="POST" action="{{ route('admin.profile.update') }}" class="forms-sample" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" value="{{ old('username', $profileData->username) }}" name="username" autocomplete="off"
                                    placeholder="Username">
                            </div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" value="{{ old('name', $profileData->name) }}" id="name" name="name" autocomplete="off"
                                    placeholder="Name">
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" value="{{ old('phone', $profileData->phone) }}" class="form-control" name="phone" autocomplete="off"
                                    placeholder="Phone">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" value="{{ old('address', $profileData->address) }}" class="form-control" name="address" autocomplete="off"
                                    placeholder="Address">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email address</label>
                                <input type="email" value="{{ old('email', $profileData->email) }}" name="email" class="form-control" placeholder="Email">
                            </div>
                            <div class="mb-3">
                                <label for="file" class="form-label">Photo</label>
                                <input class="form-control"  name="photo" type="file" id="image">
                            </div>
                            <div class="mb-3">
	                            <img id="showImage" class="wd-80 rounded-circle" src="{{ (!empty($profileData->photo)) ? url('upload/images/admin/'.$profileData->photo) : url('upload/images/admin/no_image.jpg') }}" alt="profile">
				            </div>
                            <button type="submit" class="btn btn-primary me-2">Save Changes </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- middle wrapper end -->
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('#image').change(function(e){
            var reader = new FileReader();
            reader.onload = function(e){
                $('#showImage').attr('src',e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>
@endsection
