<div class="nav-item dropdown">
    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" 
       data-bs-toggle="dropdown" 
       aria-label="Open notifications menu"
       wire:click="toggleDropdown">
        <span class="position-relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="26" height="26" viewBox="0 0 23 20" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
            </svg>
            @if($unreadCount > 0)
                <span class="badge bg-red position-absolute top-0 start-100 translate-middle rounded-pill" style="font-size: 0.6rem;">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </span>
    </a>
    
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="width: 350px;">
        <div class="dropdown-header">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Notifications</h6>
                @if($unreadCount > 0)
                    <button class="btn btn-sm btn-outline-primary" wire:click="markAllAsRead">
                        Mark all read
                    </button>
                @endif
            </div>
        </div>
        
        <div class="dropdown-divider"></div>
        
        @if(count($notifications) > 0)
            <div class="dropdown-list" style="max-height: 300px; overflow-y: auto;">
                @foreach($notifications as $notification)
                    <div class="dropdown-item d-flex align-items-start {{ !$notification['is_read'] ? 'bg-light' : '' }}" 
                         wire:click="goToOrder({{ $notification['order_id'] }})"
                         style="cursor: pointer;">
                        <div class="flex-shrink-0 me-3">
                            @if($notification['type'] === 'pending_order')
                                <span class="bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 2l3.09 6.26l6.91 1.01l-5 4.87l1.18 6.88l-6.18 -3.25l-6.18 3.25l1.18 -6.88l-5 -4.87l6.91 -1.01z"/>
                                    </svg>
                                </span>
                            @elseif($notification['type'] === 'cancelled_order')
                                <span class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M18 6l-12 12"/>
                                        <path d="M6 6l12 12"/>
                                    </svg>
                                </span>
                            @else
                                <span class="bg-info text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 12l2 2l4 -4"/>
                                    </svg>
                                </span>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold text-truncate" style="max-width: 200px;">
                                        {{ $notification['title'] }}
                                    </div>
                                    <div class="text-muted small text-truncate" style="max-width: 200px;">
                                        {{ $notification['message'] }}
                                    </div>
                                </div>
                                @if(!$notification['is_read'])
                                    <span class="badge bg-primary rounded-pill" style="font-size: 0.5rem;">New</span>
                                @endif
                            </div>
                            <div class="text-muted small mt-1">
                                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @if(!$loop->last)
                        <div class="dropdown-divider"></div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="dropdown-item text-center text-muted py-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg mb-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                    <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                </svg>
                <div>No notifications</div>
            </div>
        @endif
        
        <div class="dropdown-divider"></div>
        <div class="dropdown-item text-center">
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                View All Orders
            </a>
        </div>
    </div>
</div>
