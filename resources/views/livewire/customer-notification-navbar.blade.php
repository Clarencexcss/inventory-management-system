<div class="nav-item dropdown">
    <a href="#" class="nav-link position-relative p-0" 
       data-bs-toggle="dropdown" 
       aria-label="Open notifications menu">
        <!-- Bell Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
        </svg>

        @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </a>

    <!-- Dropdown -->
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="width: 380px; max-height: 500px; overflow-y: auto;">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
            <span><strong>Order Notifications</strong></span>
            @if($unreadCount > 0)
                <button class="btn btn-sm btn-outline-primary" wire:click="markAllAsRead">
                    <i class="fas fa-check-double me-1"></i>Mark all read
                </button>
            @endif
        </div>
        <div class="dropdown-divider"></div>

        <div class="dropdown-list">
            @forelse($notifications as $notification)
                <div class="dropdown-item d-flex align-items-start {{ !$notification['is_read'] ? 'bg-light border-start border-primary border-3' : '' }}" 
                     wire:click="goToOrder({{ $notification['data']['order_id'] ?? '' }})" 
                     style="cursor: pointer; padding: 12px 16px;">
                    <div class="me-3 flex-shrink-0">
                        @if($notification['type'] === 'order_completed')
                            <span class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fas fa-check" style="font-size: 14px;"></i>
                            </span>
                        @elseif($notification['type'] === 'order_cancelled')
                            <span class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fas fa-times" style="font-size: 14px;"></i>
                            </span>
                        @elseif($notification['type'] === 'order_placed')
                            <span class="bg-primary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fas fa-shopping-cart" style="font-size: 14px;"></i>
                            </span>
                        @elseif($notification['type'] === 'order_status_update')
                            <span class="bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fas fa-info-circle" style="font-size: 14px;"></i>
                            </span>
                        @else
                            <span class="bg-secondary text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fas fa-bell" style="font-size: 14px;"></i>
                            </span>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-bold text-truncate mb-1" style="max-width: 250px;">{{ $notification['title'] }}</div>
                        <div class="text-muted small text-truncate mb-2" style="max-width: 250px; line-height: 1.3;">{{ $notification['message'] }}</div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="fas fa-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                            </div>
                            @if(!$notification['is_read'])
                                <span class="badge bg-primary" style="font-size: 0.6rem;">New</span>
                            @endif
                        </div>
                    </div>
                </div>
                @if(!$loop->last)
                    <div class="dropdown-divider m-0"></div>
                @endif
            @empty
                <div class="dropdown-item text-center text-muted py-5">
                    <i class="fas fa-bell-slash fa-2x mb-2 text-muted"></i>
                    <div>No notifications yet</div>
                    <small>We'll notify you when there are updates to your orders</small>
                </div>
            @endforelse
        </div>

        <div class="dropdown-divider"></div>
        <div class="dropdown-item text-center">
            <a href="{{ route('customer.orders') }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-shopping-bag me-1"></i>View All Orders
            </a>
        </div>
    </div>
</div>
