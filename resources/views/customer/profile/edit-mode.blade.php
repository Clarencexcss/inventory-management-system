<!-- Edit Mode -->
<form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data" id="editForm" style="{{ $errors->any() ? 'display: block;' : 'display: none;' }}">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label for="name">Full Name</label>
        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="form-control" required id="nameInput">
        <small class="form-text text-muted" id="nameHelp">Name may only contain letters, spaces, periods, hyphens, and apostrophes.</small>
        @if ($errors->has('name'))
            <div class="text-danger">{{ $errors->first('name') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="username">Username</label>
        <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" class="form-control">
        @if ($errors->has('username'))
            <div class="text-danger">{{ $errors->first('username') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="phone">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}" class="form-control" placeholder="e.g., 09123456789 or +63123456789" id="phoneInput">
        <small class="form-text text-muted">Phone number must start with +63 and be exactly 11 digits. If you type "09", it will be automatically converted to "+63".</small>
        @if ($errors->has('phone'))
            <div class="text-danger">{{ $errors->first('phone') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="email">Email <small>(optional - leave blank to keep current)</small></label>
        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" class="form-control">
        @if ($errors->has('email'))
            <div class="text-danger">{{ $errors->first('email') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="address">Address</label>
        <textarea name="address" class="form-control" rows="3">{{ old('address', auth()->user()->address) }}</textarea>
        @if ($errors->has('address'))
            <div class="text-danger">{{ $errors->first('address') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="password">New Password <small>(optional - leave blank to keep current)</small></label>
        <input type="password" name="password" class="form-control" id="passwordField">
        @if ($errors->has('password'))
            <div class="text-danger">{{ $errors->first('password') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="password_confirmation">Confirm New Password <small>(required only when changing password)</small></label>
        <input type="password" name="password_confirmation" class="form-control" id="passwordConfirmationField">
        @if ($errors->has('password_confirmation'))
            <div class="text-danger">{{ $errors->first('password_confirmation') }}</div>
        @endif
    </div>

    <div class="mb-3">
        <label for="photo">Profile Photo</label>
        <input type="file" name="photo" class="form-control">
        @if(auth()->user()->photo)
            <img src="{{ asset('storage/customers/' . auth()->user()->photo) }}" width="100" class="mt-2 rounded">
        @endif
        @if ($errors->has('photo'))
            <div class="text-danger">{{ $errors->first('photo') }}</div>
        @endif
    </div>

    <button type="button" class="btn btn-secondary" id="cancelEditBtn">Cancel</button>
    <button type="submit" class="btn btn-primary">Update Profile</button>
</form>