@extends('layouts.butcher')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-edit me-2"></i>Edit Performance Record
            </h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('staff-performance.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Records
            </a>
        </div>
    </div>

    <x-alert/>

    <div class="card">
        <form method="POST" action="{{ route('staff-performance.update', $staffPerformance) }}" id="performanceForm">
            @csrf
            @method('PUT')
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Staff Member</label>
                            <select name="staff_id" id="staff_id" class="form-select @error('staff_id') is-invalid @enderror" required>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ old('staff_id', $staffPerformance->staff_id) == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} - {{ $member->position }}
                                    </option>
                                @endforeach
                            </select>
                            @error('staff_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label required">Month</label>
                            <input type="month" name="month" id="month" class="form-control @error('month') is-invalid @enderror" 
                                value="{{ old('month', $staffPerformance->month) }}" required>
                            @error('month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label required">Attendance Rate (%)</label>
                            <input type="number" name="attendance_rate" id="attendance_rate" 
                                class="form-control @error('attendance_rate') is-invalid @enderror" 
                                value="{{ old('attendance_rate', $staffPerformance->attendance_rate) }}" 
                                min="0" max="100" step="0.1" required>
                            @error('attendance_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">0-100%</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label required">Task Completion Rate (%)</label>
                            <input type="number" name="task_completion_rate" id="task_completion_rate" 
                                class="form-control @error('task_completion_rate') is-invalid @enderror" 
                                value="{{ old('task_completion_rate', $staffPerformance->task_completion_rate) }}" 
                                min="0" max="100" step="0.1" required>
                            @error('task_completion_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">0-100%</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label required">Customer Feedback Score</label>
                            <input type="number" name="customer_feedback_score" id="customer_feedback_score" 
                                class="form-control @error('customer_feedback_score') is-invalid @enderror" 
                                value="{{ old('customer_feedback_score', $staffPerformance->customer_feedback_score) }}" 
                                min="1" max="5" step="0.1" required>
                            @error('customer_feedback_score')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">1-5 scale</small>
                        </div>
                    </div>
                </div>

                <!-- Performance Preview -->
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h4 class="alert-heading">
                                <i class="fas fa-calculator me-2"></i>
                                Calculated Overall Performance
                            </h4>
                            <p class="mb-0">
                                Overall Score: <strong><span id="calculatedScore">{{ number_format($staffPerformance->overall_performance, 1) }}</span>%</strong>
                                <br>
                                <small class="text-muted">
                                    Formula: (Attendance × 30%) + (Task Completion × 40%) + (Feedback × 30%)
                                </small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                rows="3" placeholder="Add any remarks or notes about this performance evaluation">{{ old('remarks', $staffPerformance->remarks) }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>
                    Update Performance Record
                </button>
                <a href="{{ route('staff-performance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@push('page-scripts')
<script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
<script>
    // Auto-calculate overall performance
    function calculateOverallPerformance() {
        const attendance = parseFloat(document.getElementById('attendance_rate').value) || 0;
        const taskCompletion = parseFloat(document.getElementById('task_completion_rate').value) || 0;
        const feedback = parseFloat(document.getElementById('customer_feedback_score').value) || 0;
        
        const attendanceScore = (attendance / 100) * 0.3;
        const taskScore = (taskCompletion / 100) * 0.4;
        const feedbackScore = (feedback / 5) * 0.3;
        
        const overall = (attendanceScore + taskScore + feedbackScore) * 100;
        
        document.getElementById('calculatedScore').textContent = overall.toFixed(1);
    }
    
    // Add event listeners
    document.getElementById('attendance_rate').addEventListener('input', calculateOverallPerformance);
    document.getElementById('task_completion_rate').addEventListener('input', calculateOverallPerformance);
    document.getElementById('customer_feedback_score').addEventListener('input', calculateOverallPerformance);
    
    // Calculate on page load
    calculateOverallPerformance();

    // Show success toast
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    @endif
</script>
@endpush
@endsection

@section('content')
<div class="page-body">
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit me-2"></i>
                    Edit Performance Record
                </h3>
                <div class="card-actions">
                    <x-action.close route="{{ route('staff-performance.index') }}" />
                </div>
            </div>
            
            <form method="POST" action="{{ route('staff-performance.update', $staffPerformance) }}" id="performanceForm">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Staff Member</label>
                                <select name="staff_id" id="staff_id" class="form-select @error('staff_id') is-invalid @enderror" required>
                                    @foreach($staff as $member)
                                        <option value="{{ $member->id }}" {{ old('staff_id', $staffPerformance->staff_id) == $member->id ? 'selected' : '' }}>
                                            {{ $member->name }} - {{ $member->position }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('staff_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label required">Month</label>
                                <input type="month" name="month" id="month" class="form-control @error('month') is-invalid @enderror" 
                                    value="{{ old('month', $staffPerformance->month) }}" required>
                                @error('month')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Attendance Rate (%)</label>
                                <input type="number" name="attendance_rate" id="attendance_rate" 
                                    class="form-control @error('attendance_rate') is-invalid @enderror" 
                                    value="{{ old('attendance_rate', $staffPerformance->attendance_rate) }}" 
                                    min="0" max="100" step="0.1" required>
                                @error('attendance_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">0-100%</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Task Completion Rate (%)</label>
                                <input type="number" name="task_completion_rate" id="task_completion_rate" 
                                    class="form-control @error('task_completion_rate') is-invalid @enderror" 
                                    value="{{ old('task_completion_rate', $staffPerformance->task_completion_rate) }}" 
                                    min="0" max="100" step="0.1" required>
                                @error('task_completion_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">0-100%</small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label required">Customer Feedback Score</label>
                                <input type="number" name="customer_feedback_score" id="customer_feedback_score" 
                                    class="form-control @error('customer_feedback_score') is-invalid @enderror" 
                                    value="{{ old('customer_feedback_score', $staffPerformance->customer_feedback_score) }}" 
                                    min="1" max="5" step="0.1" required>
                                @error('customer_feedback_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-hint">1-5 scale</small>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Preview -->
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h4 class="alert-heading">
                                    <i class="fas fa-calculator me-2"></i>
                                    Calculated Overall Performance
                                </h4>
                                <p class="mb-0">
                                    Overall Score: <strong><span id="calculatedScore">{{ number_format($staffPerformance->overall_performance, 1) }}</span>%</strong>
                                    <br>
                                    <small class="text-muted">
                                        Formula: (Attendance × 30%) + (Task Completion × 40%) + (Feedback × 30%)
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror" 
                                    rows="3" placeholder="Add any remarks or notes about this performance evaluation">{{ old('remarks', $staffPerformance->remarks) }}</textarea>
                                @error('remarks')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Update Performance Record
                    </button>
                    <a href="{{ route('staff-performance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
    // Auto-calculate overall performance
    function calculateOverallPerformance() {
        const attendance = parseFloat(document.getElementById('attendance_rate').value) || 0;
        const taskCompletion = parseFloat(document.getElementById('task_completion_rate').value) || 0;
        const feedback = parseFloat(document.getElementById('customer_feedback_score').value) || 0;
        
        const attendanceScore = (attendance / 100) * 0.3;
        const taskScore = (taskCompletion / 100) * 0.4;
        const feedbackScore = (feedback / 5) * 0.3;
        
        const overall = (attendanceScore + taskScore + feedbackScore) * 100;
        
        document.getElementById('calculatedScore').textContent = overall.toFixed(1);
    }
    
    // Add event listeners
    document.getElementById('attendance_rate').addEventListener('input', calculateOverallPerformance);
    document.getElementById('task_completion_rate').addEventListener('input', calculateOverallPerformance);
    document.getElementById('customer_feedback_score').addEventListener('input', calculateOverallPerformance);
    
    // Calculate on page load
    calculateOverallPerformance();
</script>
@endpush
@endsection
