// Reports & Analytics JavaScript
$(document).ready(function() {
    // Initialize charts
    let salesChart, ordersChart, productsChart, customersChart;
    
    // Load initial report
    generateReport();
    
    // Handle time period change
    $('#timePeriod').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#customFrom, #customTo').show();
        } else {
            $('#customFrom, #customTo').hide();
        }
    });
    
    // Generate report function
    window.generateReport = function() {
        const reportType = $('#reportType').val();
        const timePeriod = $('#timePeriod').val();
        const customFrom = $('#customFrom').val();
        const customTo = $('#customTo').val();
        
        // Show loading
        Swal.fire({
            title: 'Generating Report...',
            text: 'Please wait',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: '../actions/get_reports_action.php',
            type: 'GET',
            data: {
                report_type: reportType,
                time_period: timePeriod,
                custom_from: customFrom,
                custom_to: customTo
            },
            dataType: 'json',
            success: function(response) {
                Swal.close();
                if (response.status === 'success') {
                    updateMetrics(response.data.metrics);
                    updateCharts(response.data.charts);
                    updateReportTable(response.data.table);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to generate report'
                });
            }
        });
    };
    
    // Update metrics
    function updateMetrics(metrics) {
        $('#totalRevenue').text('$' + (metrics.total_revenue || 0).toFixed(2));
        $('#totalOrders').text(metrics.total_orders || 0);
        $('#totalCustomers').text(metrics.total_customers || 0);
        $('#totalProducts').text(metrics.total_products || 0);
    }
    
    // Update charts
    function updateCharts(chartData) {
        // Sales Trend Chart
        if (salesChart) salesChart.destroy();
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: chartData.sales.labels || [],
                datasets: [{
                    label: 'Sales',
                    data: chartData.sales.data || [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Orders by Status Chart
        if (ordersChart) ordersChart.destroy();
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        ordersChart = new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: chartData.orders.labels || [],
                datasets: [{
                    data: chartData.orders.data || [],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Top Products Chart
        if (productsChart) productsChart.destroy();
        const productsCtx = document.getElementById('productsChart').getContext('2d');
        productsChart = new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: chartData.products.labels || [],
                datasets: [{
                    label: 'Sales',
                    data: chartData.products.data || [],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Customer Growth Chart
        if (customersChart) customersChart.destroy();
        const customersCtx = document.getElementById('customersChart').getContext('2d');
        customersChart = new Chart(customersCtx, {
            type: 'line',
            data: {
                labels: chartData.customers.labels || [],
                datasets: [{
                    label: 'New Customers',
                    data: chartData.customers.data || [],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Update report table
    function updateReportTable(tableData) {
        const tbody = $('#reportTableBody');
        tbody.empty();
        
        if (tableData.length === 0) {
            tbody.append('<tr><td colspan="4" class="text-center text-muted">No data available</td></tr>');
            return;
        }
        
        tableData.forEach(function(row) {
            const tr = `
                <tr>
                    <td>${row.date}</td>
                    <td>${row.orders}</td>
                    <td>$${parseFloat(row.revenue || 0).toFixed(2)}</td>
                    <td>${row.customers}</td>
                </tr>
            `;
            tbody.append(tr);
        });
    }
    
    // Export report
    window.exportReport = function() {
        const reportType = $('#reportType').val();
        const timePeriod = $('#timePeriod').val();
        
        Swal.fire({
            title: 'Export Report',
            text: 'Choose export format',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'CSV',
            cancelButtonText: 'PDF',
            showDenyButton: true,
            denyButtonText: 'Excel'
        }).then((result) => {
            if (result.isConfirmed) {
                exportToCSV();
            } else if (result.isDenied) {
                exportToExcel();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                exportToPDF();
            }
        });
    };
    
    // Export to CSV
    function exportToCSV() {
        window.open('export_report_action.php?format=csv&report_type=' + $('#reportType').val() + '&time_period=' + $('#timePeriod').val());
    }
    
    // Export to Excel
    function exportToExcel() {
        window.open('export_report_action.php?format=excel&report_type=' + $('#reportType').val() + '&time_period=' + $('#timePeriod').val());
    }
    
    // Export to PDF
    function exportToPDF() {
        window.open('export_report_action.php?format=pdf&report_type=' + $('#reportType').val() + '&time_period=' + $('#timePeriod').val());
    }
    
    // Print report
    window.printReport = function() {
        window.print();
    };
});
