@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2" id="page-title">
            {{ $title ?? 'QS World University Rankings' }}
        </h1>
        <p class="text-gray-600 text-lg" id="page-description">
            Explore the QS World University Rankings data with comprehensive information about universities worldwide.
        </p>
        @if(isset($totalRecords) && $totalRecords > 0)
            <div class="inline-flex items-center mt-3 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium" aria-live="polite">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Total records: {{ number_format($totalRecords) }}
            </div>
        @endif
    </div>

    <!-- Error Message -->
    @if(isset($error))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg shadow-sm" role="alert" aria-live="assertive">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Error Loading Data</h3>
                    <p class="text-sm text-red-700 mt-1">{{ $error }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Modern Data Table Card -->
    @if(!isset($error))
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100">
            <!-- Enhanced Table Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold" id="table-title">🏆 University Rankings Data</h2>
                        <p class="text-blue-100 mt-1">Search, sort, and explore university rankings with advanced analytics</p>
                    </div>
                    <div class="hidden md:flex items-center space-x-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-2">
                            <span class="text-sm font-medium">Live Data</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loading-indicator" class="flex flex-col justify-center items-center p-12" aria-live="polite">
                <div class="relative">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-200"></div>
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent absolute top-0 left-0"></div>
                </div>
                <span class="mt-4 text-gray-600 font-medium">Loading rankings data...</span>
                <span class="text-sm text-gray-500">Please wait while we fetch the latest information</span>
            </div>

            <!-- Enhanced Table Container -->
            <div class="overflow-hidden" id="table-container" style="display: none;">
                <div class="overflow-x-auto">
                    <table id="qs-rankings-table" class="min-w-full" role="table" aria-labelledby="table-title">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                @if(isset($headers) && count($headers) > 0)
                                    @foreach($headers as $header)
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider hover:bg-gray-100 transition-colors cursor-pointer relative group">
                                            <div class="flex items-center space-x-2">
                                                <span>{{ $header }}</span>
                                                <svg class="w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                                </svg>
                                            </div>
                                        </th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100" role="rowgroup">
                            <!-- Data will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Enhanced No Data Message -->
            <div id="no-data-message" class="p-12 text-center" style="display: none;" role="alert">
                <div class="max-w-md mx-auto">
                    <div class="bg-gray-100 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Rankings Data Available</h3>
                    <p class="text-gray-600 mb-4">
                        We couldn't find any data to display. Please ensure the CSV file is properly uploaded to the system.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Accessibility & Stats Section -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Accessibility Instructions -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 shadow-lg" role="region" aria-labelledby="accessibility-title">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 rounded-lg p-2 mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <h3 id="accessibility-title" class="text-lg font-bold text-blue-900">Accessibility Features</h3>
            </div>
            <ul class="text-sm text-blue-800 space-y-2">
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">•</span>
                    <span>Use the search box to filter data across all columns</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">•</span>
                    <span>Click column headers to sort data ascending/descending</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">•</span>
                    <span>Navigate with Tab key and screen reader compatible</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 mr-2">•</span>
                    <span>Responsive design works on all device sizes</span>
                </li>
            </ul>
        </div>

        <!-- Quick Stats -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-6 shadow-lg">
            <div class="flex items-center mb-4">
                <div class="bg-purple-100 rounded-lg p-2 mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-purple-900">Quick Statistics</h3>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600" id="total-displayed">-</div>
                    <div class="text-sm text-purple-700">Currently Showing</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600" id="total-filtered">-</div>
                    <div class="text-sm text-purple-700">After Filtering</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
    // Check if we have headers data
    @if(isset($headers) && count($headers) > 0)
        // Initialize DataTable with enhanced features
        const table = $('#qs-rankings-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            stateSave: true,
            searchDelay: 500,
            ajax: {
                url: '{{ route("qs-rankings") }}',
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable Ajax Error:', error);
                    $('#loading-indicator').hide();
                    $('#table-container').hide();
                    $('#no-data-message').show();
                }
            },
            columns: [
                @foreach($headers as $header)
                    { 
                        data: '{{ $header }}',
                        name: '{{ $header }}',
                        title: '{{ $header }}',
                        defaultContent: '-',
                        orderable: true,
                        searchable: true,
                        @if($header === 'University Path')
                        render: function(data, type, row) {
                            if (type === 'display' && data && data.includes('<a href=')) {
                                return data; // Return HTML as-is for display
                            }
                            return data || '-';
                        }
                        @endif
                    },
                @endforeach
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[0, 'asc']], // Default sort by first column
            language: {
                processing: `
                    <div class="flex flex-col items-center justify-center p-8">
                        <div class="relative mb-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-200"></div>
                            <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent absolute top-0 left-0"></div>
                        </div>
                        <span class="text-gray-600 font-medium">Processing rankings...</span>
                    </div>
                `,
                search: 'Search rankings:',
                searchPlaceholder: 'Search universities, countries, rankings...',
                lengthMenu: 'Show _MENU_ entries per page',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                infoEmpty: 'No entries available',
                infoFiltered: '(filtered from _MAX_ total entries)',
                paginate: {
                    first: '⇤ First',
                    last: 'Last ⇥',
                    next: 'Next ›',
                    previous: '‹ Prev'
                },
                aria: {
                    sortAscending: ': click to sort column ascending',
                    sortDescending: ': click to sort column descending'
                }
            },
            drawCallback: function(settings) {
                // Update page info for screen readers
                const info = this.api().page.info();
                $('#table-container').attr('aria-live', 'polite');
                
                // Update statistics with null checks
                if (info && typeof info.recordsDisplay !== 'undefined' && typeof info.recordsFiltered !== 'undefined') {
                    $('#total-displayed').text(info.recordsDisplay.toLocaleString());
                    $('#total-filtered').text(info.recordsFiltered.toLocaleString());
                } else {
                    $('#total-displayed').text('0');
                    $('#total-filtered').text('0');
                }
                
                // Add hover effects to rows
                $('#qs-rankings-table tbody tr').hover(
                    function() {
                        $(this).addClass('bg-blue-50 transform scale-[1.01] shadow-md');
                    },
                    function() {
                        $(this).removeClass('bg-blue-50 transform scale-[1.01] shadow-md');
                    }
                );
                
                // Add click effects for accessibility
                $('#qs-rankings-table tbody tr').attr('tabindex', '0').css('cursor', 'pointer');
            },
            initComplete: function(settings, json) {
                $('#loading-indicator').hide();
                $('#table-container').show();
                
                // Enhanced search box styling
                $('.dataTables_filter input').attr({
                    'aria-label': 'Search rankings data',
                    'placeholder': 'Search universities, countries, rankings...'
                }).addClass('search-enhanced');
                
                $('.dataTables_length select').attr('aria-label', 'Number of entries per page');
                
                // Add sorting indicators
                $('#qs-rankings-table thead th').each(function() {
                    $(this).append('<span class="sort-indicator ml-1 opacity-50"></span>');
                });
                
                // Announce when data is loaded
                $('<div>').attr({
                    'aria-live': 'polite',
                    'aria-atomic': 'true',
                    'class': 'sr-only'
                }).text('QS Rankings data has been loaded successfully. Use arrow keys to navigate and enter to select.').appendTo('body');
                
                // Initial stats update
                const info = this.api().page.info();
                if (info && typeof info.recordsDisplay !== 'undefined' && typeof info.recordsFiltered !== 'undefined') {
                    $('#total-displayed').text(info.recordsDisplay.toLocaleString());
                    $('#total-filtered').text(info.recordsFiltered.toLocaleString());
                } else {
                    $('#total-displayed').text('0');
                    $('#total-filtered').text('0');
                }
            }
        });

        // Enhanced keyboard navigation
        $('#qs-rankings-table').on('keydown', 'tbody tr', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).addClass('ring-2 ring-blue-500 bg-blue-100');
                setTimeout(() => {
                    $(this).removeClass('ring-2 ring-blue-500 bg-blue-100');
                }, 300);
            }
        });

        // Add custom search functionality
        let searchTimeout;
        $('.dataTables_filter input').on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val();
            
            searchTimeout = setTimeout(() => {
                if (searchTerm.length > 0) {
                    $(this).addClass('border-blue-500 ring-2 ring-blue-200');
                } else {
                    $(this).removeClass('border-blue-500 ring-2 ring-blue-200');
                }
            }, 300);
        });

    @else
        // No headers available, show no data message
        $('#loading-indicator').hide();
        $('#no-data-message').show();
    @endif
});

// Enhanced focus management
$(document).on('keydown', function(e) {
    if (e.key === 'Tab') {
        const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
        const focusableContent = $(focusableElements).filter(':visible');
        
        if (e.shiftKey) {
            if ($(document.activeElement).is(focusableContent.first())) {
                e.preventDefault();
                focusableContent.last().focus();
            }
        } else {
            if ($(document.activeElement).is(focusableContent.last())) {
                e.preventDefault();
                focusableContent.first().focus();
            }
        }
    }
});
</script>

<style>
/* Enhanced DataTables Styling */
.dataTables_wrapper {
    padding: 1.5rem;
}

.dataTables_filter {
    margin-bottom: 1.5rem;
}

.dataTables_filter input.search-enhanced {
    @apply w-80 px-4 py-3 border-2 border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition-all duration-300 shadow-sm;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E") no-repeat right 12px center;
    background-size: 20px;
    padding-right: 45px;
}

.dataTables_length select {
    @apply px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300;
}

.dataTables_info {
    @apply text-gray-600 font-medium;
}

.dataTables_paginate {
    @apply mt-6;
}

.dataTables_paginate .paginate_button {
    @apply px-4 py-2 mx-1 border-2 border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 focus:ring-2 focus:ring-blue-200 transition-all duration-300 font-medium;
}

.dataTables_paginate .paginate_button.current {
    @apply bg-blue-600 border-blue-600 text-white hover:bg-blue-700 shadow-lg;
}

.dataTables_paginate .paginate_button.disabled {
    @apply opacity-50 cursor-not-allowed hover:bg-white hover:border-gray-300 hover:text-gray-700;
}

/* Table Row Enhancements */
#qs-rankings-table tbody tr {
    @apply transition-all duration-300;
}

#qs-rankings-table tbody tr:nth-child(odd) {
    @apply bg-gray-50/50;
}

#qs-rankings-table tbody tr:hover {
    @apply transform scale-[1.01] shadow-lg;
}

#qs-rankings-table tbody td {
    @apply px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-b border-gray-100;
}

/* Header Enhancements */
#qs-rankings-table thead th {
    @apply sticky top-0 z-10;
}

#qs-rankings-table thead th:hover {
    @apply bg-gradient-to-r from-blue-50 to-purple-50;
}

/* Custom Loading Animation */
@keyframes tableLoad {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

#table-container {
    animation: tableLoad 0.5s ease-out;
}

/* Screen reader only class */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .bg-gray-50 {
        background-color: #ffffff;
        border: 2px solid #000000;
    }
    
    .text-gray-600 {
        color: #000000;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .animate-spin {
        animation: none;
    }
    
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .dataTables_filter input.search-enhanced {
        @apply w-full;
    }
    
    .dataTables_wrapper {
        padding: 1rem;
    }
    
    #qs-rankings-table tbody td {
        @apply px-3 py-3 text-xs;
    }
}
</style>
@endsection 