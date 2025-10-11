<div class="nav-item dropdown">
    <a href="#" class="nav-link position-relative p-0" 
       data-bs-toggle="dropdown" 
       aria-label="Open notifications menu">
        <!-- Bell Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26"viewBox="0 0 23 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2-3v-3a7 7 0 0 1 4-6"/>
            <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
        </svg>

        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </a>

    <!-- Dropdown -->
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="width: 350px;">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <span>Order Notifications</span>
            @if($unreadCount > 0)
                <button class="btn btn-sm btn-outline-primary" wire:click="markAllAsRead">Mark all read</button>
            @endif
        </div>
        <div class="dropdown-divider"></div>

        <div class="dropdown-list" style="max-height: 300px; overflow-y: auto;">
            @forelse($notifications as $notification)
                <div class="dropdown-item d-flex align-items-start {{ !$notification['is_read'] ? 'bg-light' : '' }}" wire:click="goToOrder({{ $notification['data']['order_id'] ?? '' }})" style="cursor: pointer;">
                    <div class="me-3">
                        @if($notification['type'] === 'order_approved')
                            <span class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-check"></i>
                            </span>
                        @elseif($notification['type'] === 'order_cancelled')
                            <span class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-times"></i>
                            </span>
                        @else
                            <span class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-info"></i>
                            </span>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-truncate" style="max-width: 200px;">{{ $notification['title'] }}</div>
                        <div class="text-muted small text-truncate" style="max-width: 200px;">{{ $notification['message'] }}</div>
                        <div class="text-muted small mt-1">{{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}</div>
                    </div>
                </div>
                @if(!$loop->last)
                    <div class="dropdown-divider"></div>
                @endif
            @empty
                <div class="dropdown-item text-center text-muted py-4">
                    No notifications
                </div>
            @endforelse
        </div>

        <div class="dropdown-divider"></div>
        <div class="dropdown-item text-center">
            <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-primary">View All Orders</a>
        </div>
    </div>
</div>
