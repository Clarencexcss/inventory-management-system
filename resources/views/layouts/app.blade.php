<form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label for="name">Full Name</label>
        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="username">Username</label>
        <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" class="form-control">
    </div>

    <div class="mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="password">New Password <small>(leave blank to keep current)</small></label>
        <input type="password" name="password" class="form-control">
    </div>

    <div class="mb-3">
        <label for="photo">Profile Photo</label>
        <input type="file" name="photo" class="form-control">
        @if(auth()->user()->photo)
            <img src="{{ asset('storage/customers/' . auth()->user()->photo) }}" width="100" class="mt-2 rounded">
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>
