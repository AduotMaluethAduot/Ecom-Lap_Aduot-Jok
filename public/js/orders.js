// Orders Management JavaScript
$(document).ready(function() {
    // Load orders on page load
    loadOrders();
    loadOrderStats();
    
    // Handle time period change
    $('#timePeriod').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#customFrom, #customTo').show();
        } else {
            $('#customFrom, #customTo').hide();
        }
    });
    
    // Load orders function
    function loadOrders() {
        $.ajax({
            url: '../actions/get_orders_action.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayOrders(response.data);
                } else {
                    $('#noOrdersMessage').removeClass('d-none');
                    $('#ordersTableBody').empty();
                }
            },
            error: function() {
                $('#noOrdersMessage').removeClass('d-none');
                $('#ordersTableBody').empty();
            }
        });
    }
    
    // Load order statistics
    function loadOrderStats() {
        $.ajax({
            url: '../actions/get_order_stats_action.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const stats = response.data;
                    $('#total-orders').text(stats.total_orders || 0);
                    $('#pending-orders').text(stats.pending_orders || 0);
                    $('#completed-orders').text(stats.completed_orders || 0);
                    $('#total-revenue').text('$' + (stats.total_revenue || 0).toFixed(2));
                }
            },
            error: function() {
                console.log('Error loading order stats');
            }
        });
    }
    
    // Display orders in table
    function displayOrders(orders) {
        const tbody = $('#ordersTableBody');
        tbody.empty();
        
        if (orders.length === 0) {
            $('#noOrdersMessage').removeClass('d-none');
            return;
        }
        
        $('#noOrdersMessage').addClass('d-none');
        
        orders.forEach(function(order) {
            const statusBadge = getStatusBadge(order.status);
            const row = `
                <tr>
                    <td>#${order.order_id}</td>
                    <td>${order.customer_name || 'N/A'}</td>
                    <td>${formatDate(order.order_date)}</td>
                    <td>${statusBadge}</td>
                    <td>$${parseFloat(order.total_amount || 0).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-sm btn-info me-2" onclick="viewOrderDetails(${order.order_id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="updateOrderStatus(${order.order_id})">
                            <i class="fas fa-edit"></i> Update
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });
    }
    
    // Get status badge HTML
    function getStatusBadge(status) {
        const statusClasses = {
            'pending': 'badge bg-warning',
            'processing': 'badge bg-info',
            'shipped': 'badge bg-primary',
            'delivered': 'badge bg-success',
            'cancelled': 'badge bg-danger'
        };
        
        const className = statusClasses[status] || 'badge bg-secondary';
        return `<span class="${className}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
    }
    
    // Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }
    
    // Filter orders
    window.filterOrders = function() {
        const status = $('#statusFilter').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();
        
        $.ajax({
            url: '../actions/get_orders_action.php',
            type: 'GET',
            data: {
                status: status,
                date_from: dateFrom,
                date_to: dateTo
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayOrders(response.data);
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to filter orders'
                });
            }
        });
    };
    
    // Refresh orders
    window.refreshOrders = function() {
        loadOrders();
        loadOrderStats();
        
        Swal.fire({
            icon: 'success',
            title: 'Refreshed!',
            text: 'Orders data has been updated',
            timer: 1500,
            showConfirmButton: false
        });
    };
    
    // View order details
    window.viewOrderDetails = function(orderId) {
        $.ajax({
            url: '../actions/get_order_details_action.php',
            type: 'GET',
            data: { order_id: orderId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displayOrderDetails(response.data);
                    $('#orderDetailsModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load order details'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load order details'
                });
            }
        });
    };
    
    // Display order details
    function displayOrderDetails(order) {
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Order Information</h6>
                    <p><strong>Order ID:</strong> #${order.order_id}</p>
                    <p><strong>Date:</strong> ${formatDate(order.order_date)}</p>
                    <p><strong>Status:</strong> ${getStatusBadge(order.status)}</p>
                    <p><strong>Total:</strong> $${parseFloat(order.total_amount || 0).toFixed(2)}</p>
                </div>
                <div class="col-md-6">
                    <h6>Customer Information</h6>
                    <p><strong>Name:</strong> ${order.customer_name || 'N/A'}</p>
                    <p><strong>Email:</strong> ${order.customer_email || 'N/A'}</p>
                    <p><strong>Phone:</strong> ${order.customer_phone || 'N/A'}</p>
                </div>
            </div>
            <hr>
            <h6>Order Items</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${order.items ? order.items.map(item => `
                            <tr>
                                <td>${item.product_name}</td>
                                <td>${item.quantity}</td>
                                <td>$${parseFloat(item.price).toFixed(2)}</td>
                                <td>$${parseFloat(item.total).toFixed(2)}</td>
                            </tr>
                        `).join('') : '<tr><td colspan="4">No items found</td></tr>'}
                    </tbody>
                </table>
            </div>
        `;
        
        $('#orderDetailsContent').html(content);
    }
    
    // Update order status
    window.updateOrderStatus = function(orderId) {
        const currentStatus = $('#orderDetailsContent').find('.badge').text().toLowerCase();
        
        Swal.fire({
            title: 'Update Order Status',
            input: 'select',
            inputOptions: {
                'pending': 'Pending',
                'processing': 'Processing',
                'shipped': 'Shipped',
                'delivered': 'Delivered',
                'cancelled': 'Cancelled'
            },
            inputValue: currentStatus,
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../actions/update_order_status_action.php',
                    type: 'POST',
                    data: {
                        order_id: orderId,
                        status: result.value
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: 'Order status has been updated',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                $('#orderDetailsModal').modal('hide');
                                loadOrders();
                                loadOrderStats();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update order status'
                        });
                    }
                });
            }
        });
    };
});
