<!-- View Mode -->
<div id="viewMode" style="{{ $errors->any() ? 'display: none;' : 'display: block;' }}">
    <div class="mb-3">
        <label class="fw-bold">Full Name</label>
        <p class="form-control-plaintext">{{ auth()->user()->name }}</p>
    </div>
    
    <div class="mb-3">
        <label class="fw-bold">Username</label>
        <p class="form-control-plaintext">{{ auth()->user()->username ?? 'Not set' }}</p>
    </div>
    
    <div class="mb-3">
        <label class="fw-bold">Phone</label>
        <p class="form-control-plaintext">{{ auth()->user()->phone }}</p>
    </div>
    
    <div class="mb-3">
        <label class="fw-bold">Email</label>
        <p class="form-control-plaintext">{{ auth()->user()->email ?? 'Not set' }}</p>
    </div>
    
    <div class="mb-3">
        <label class="fw-bold">Address</label>
        <p class="form-control-plaintext">{{ auth()->user()->address ?? 'Not set' }}</p>
    </div>
    
    @if(auth()->user()->photo)
        <div class="mb-3">
            <label class="fw-bold">Profile Photo</label>
            <div>
                <img src="{{ asset('storage/customers/' . auth()->user()->photo) }}" width="100" class="rounded">
            </div>
        </div>
    @endif
    
    <button type="button" class="btn btn-primary" id="editProfileBtn">
        <i class="fas fa-edit me-2"></i>Edit Profile
    </button>
</div>