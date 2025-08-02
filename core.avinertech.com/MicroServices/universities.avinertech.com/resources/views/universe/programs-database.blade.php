@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-blue-600 bg-clip-text text-transparent mb-2" id="page-title">
            {{ $title ?? 'University Programs Database' }}
        </h1>
        <p class="text-gray-600 text-lg" id="page-description">
            Explore comprehensive information about university programs worldwide, including program categories and levels.
        </p>
        @if(isset($totalRecords) && $totalRecords > 0)
            <div class="inline-flex items-center mt-3 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium" aria-live="polite">
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
            <div class="bg-gradient-to-r from-green-600 to-blue-600 px-8 py-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold" id="table-title">📚 University Programs Database</h2>
                        <p class="text-green-100 mt-1">Discover academic programs with comprehensive filtering and sorting capabilities</p>
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
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-green-200"></div>
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-green-600 border-t-transparent absolute top-0 left-0"></div>
                </div>
                <span class="mt-4 text-gray-600 font-medium">Loading programs data...</span>
                <span class="text-sm text-gray-500">Please wait while we fetch program information</span>
            </div>

            <!-- Enhanced Table Container -->
            <div class="overflow-hidden" id="table-container" style="display: none;">
                <div class="overflow-x-auto">
                    <table id="programs-table" class="min-w-full" role="table" aria-labelledby="table-title">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Programs Data Available</h3>
                    <p class="text-gray-600 mb-4">
                        We couldn't find any program data to display. Please ensure the CSV file is properly uploaded to the system.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Statistics Section -->
    @if(!isset($error) && isset($totalRecords) && $totalRecords > 0)
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6" role="region" aria-labelledby="stats-title">
            <h3 id="stats-title" class="sr-only">Data Statistics</h3>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 border border-blue-200 shadow-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-blue-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-lg font-bold text-blue-900" aria-live="polite">{{ number_format($totalRecords) }}</p>
                        <p class="text-sm font-medium text-blue-700">Total Programs</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 border border-green-200 shadow-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-green-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-lg font-bold text-green-900" id="unique-universities">Loading...</p>
                        <p class="text-sm font-medium text-green-700">Universities</p>
                    </div>
                </div>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-6 border border-purple-200 shadow-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-purple-500 rounded-lg p-3">
                            <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-lg font-bold text-purple-900" id="unique-categories">Loading...</p>
                        <p class="text-sm font-medium text-purple-700">Categories</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Accessibility Section -->
    <div class="mt-8">
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6 shadow-lg" role="region" aria-labelledby="accessibility-title">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 rounded-lg p-2 mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h3 id="accessibility-title" class="text-lg font-bold text-green-900">Accessibility & Navigation</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-green-800">
                <div>
                    <h4 class="font-semibold mb-2">Search & Filter:</h4>
                    <ul class="space-y-1">
                        <li class="flex items-start">
                            <span class="text-green-600 mr-2">•</span>
                            <span>Search by university, program name, category, or level</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-600 mr-2">•</span>
                            <span>Click column headers to sort data</span>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Keyboard Navigation:</h4>
                    <ul class="space-y-1">
                        <li class="flex items-start">
                            <span class="text-green-600 mr-2">•</span>
                            <span>Press Tab to navigate between elements</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-600 mr-2">•</span>
                            <span>Alt+S to focus search, Alt+L for page length</span>
                        </li>
                    </ul>
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
        const table = $('#programs-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            stateSave: true,
            searchDelay: 500,
            ajax: {
                url: '{{ route("programs-database") }}',
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
                            <div class="animate-spin rounded-full h-8 w-8 border-4 border-green-200"></div>
                            <div class="animate-spin rounded-full h-8 w-8 border-4 border-green-600 border-t-transparent absolute top-0 left-0"></div>
                        </div>
                        <span class="text-gray-600 font-medium">Processing programs...</span>
                    </div>
                `,
                search: 'Search programs:',
                searchPlaceholder: 'Search universities, programs, categories...',
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
                
                // Add hover effects to rows
                $('#programs-table tbody tr').hover(
                    function() {
                        $(this).addClass('bg-green-50 transform scale-[1.01] shadow-md');
                    },
                    function() {
                        $(this).removeClass('bg-green-50 transform scale-[1.01] shadow-md');
                    }
                );
                
                // Add click effects for accessibility
                $('#programs-table tbody tr').attr('tabindex', '0').css('cursor', 'pointer');
                
                // Update statistics with current data
                const currentData = this.api().rows({page: 'current'}).data().toArray();
                calculateStatistics(currentData, false);
            },
            initComplete: function(settings, json) {
                $('#loading-indicator').hide();
                $('#table-container').show();
                
                // Enhanced search box styling
                $('.dataTables_filter input').attr({
                    'aria-label': 'Search programs data',
                    'placeholder': 'Search universities, programs, categories...'
                }).addClass('search-enhanced');
                
                $('.dataTables_length select').attr('aria-label', 'Number of entries per page');
                
                // Add sorting indicators
                $('#programs-table thead th').each(function() {
                    $(this).append('<span class="sort-indicator ml-1 opacity-50"></span>');
                });
                
                // Calculate and display statistics
                calculateStatistics(json.data);
                
                // Announce when data is loaded
                $('<div>').attr({
                    'aria-live': 'polite',
                    'aria-atomic': 'true',
                    'class': 'sr-only'
                }).text('Programs database has been loaded successfully. Use arrow keys to navigate and enter to select.').appendTo('body');
            }
        });

        // Enhanced keyboard navigation
        $('#programs-table').on('keydown', 'tbody tr', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(this).addClass('ring-2 ring-green-500 bg-green-100');
                setTimeout(() => {
                    $(this).removeClass('ring-2 ring-green-500 bg-green-100');
                }, 300);
            }
        });

        // Function to calculate and display statistics
        function calculateStatistics(data, updateAll = true) {
            if (!data || data.length === 0) {
                $('#unique-universities').text('0');
                $('#unique-categories').text('0');
                return;
            }
            
            try {
                // Calculate unique universities
                const uniqueUniversities = [...new Set(data.map(item => item['University Name']).filter(Boolean))];
                $('#unique-universities').text(uniqueUniversities.length.toLocaleString());
                
                // Calculate unique categories  
                const uniqueCategories = [...new Set(data.map(item => item['Program Category']).filter(Boolean))];
                $('#unique-categories').text(uniqueCategories.length.toLocaleString());
            } catch (error) {
                console.warn('Error calculating statistics:', error);
                $('#unique-universities').text('0');
                $('#unique-categories').text('0');
            }
        }

        // Add custom search functionality
        let searchTimeout;
        $('.dataTables_filter input').on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val();
            
            searchTimeout = setTimeout(() => {
                if (searchTerm.length > 0) {
                    $(this).addClass('border-green-500 ring-2 ring-green-200');
                } else {
                    $(this).removeClass('border-green-500 ring-2 ring-green-200');
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

// Add keyboard shortcuts for common actions
$(document).on('keydown', function(e) {
    // Alt + S to focus search
    if (e.altKey && e.key === 's') {
        e.preventDefault();
        $('input[type="search"]').focus();
    }
    
    // Alt + L to focus length menu
    if (e.altKey && e.key === 'l') {
        e.preventDefault();
        $('select[name$="_length"]').focus();
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
    @apply w-80 px-4 py-3 border-2 border-gray-300 rounded-xl text-gray-900 placeholder-gray-500 focus:border-green-500 focus:ring-4 focus:ring-green-100 transition-all duration-300 shadow-sm;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E") no-repeat right 12px center;
    background-size: 20px;
    padding-right: 45px;
}

.dataTables_length select {
    @apply px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 transition-all duration-300;
}

.dataTables_info {
    @apply text-gray-600 font-medium;
}

.dataTables_paginate {
    @apply mt-6;
}

.dataTables_paginate .paginate_button {
    @apply px-4 py-2 mx-1 border-2 border-gray-300 bg-white text-gray-700 rounded-lg hover:bg-green-50 hover:border-green-300 hover:text-green-700 focus:ring-2 focus:ring-green-200 transition-all duration-300 font-medium;
}

.dataTables_paginate .paginate_button.current {
    @apply bg-green-600 border-green-600 text-white hover:bg-green-700 shadow-lg;
}

.dataTables_paginate .paginate_button.disabled {
    @apply opacity-50 cursor-not-allowed hover:bg-white hover:border-gray-300 hover:text-gray-700;
}

/* Table Row Enhancements */
#programs-table tbody tr {
    @apply transition-all duration-300;
}

#programs-table tbody tr:nth-child(odd) {
    @apply bg-gray-50/50;
}

#programs-table tbody tr:hover {
    @apply transform scale-[1.01] shadow-lg;
}

#programs-table tbody td {
    @apply px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-b border-gray-100;
}

/* Header Enhancements */
#programs-table thead th {
    @apply sticky top-0 z-10;
}

#programs-table thead th:hover {
    @apply bg-gradient-to-r from-green-50 to-blue-50;
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
    
    .from-blue-50, .from-green-50, .from-purple-50 {
        background-color: #ffffff;
        border: 2px solid #000000;
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

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    .dataTables_wrapper .dataTables_paginate,
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        display: none !important;
    }
}

/* Mobile responsive improvements */
@media (max-width: 768px) {
    .dataTables_filter input.search-enhanced {
        @apply w-full mb-4;
        font-size: 16px; /* Prevent zoom on iOS */
    }
    
    .dataTables_wrapper {
        padding: 0.5rem;
        overflow-x: auto;
    }
    
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        @apply text-sm;
    }
    
    .dataTables_length select {
        @apply text-sm;
        font-size: 16px; /* Prevent zoom on iOS */
    }
    
    #programs-table {
        @apply text-xs;
        min-width: 100%;
    }
    
    #programs-table tbody td {
        @apply px-2 py-2 text-xs;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }
    
    #programs-table thead th {
        @apply px-2 py-2 text-xs;
        white-space: nowrap;
    }
    
    /* Improve pagination on mobile */
    .dataTables_paginate .paginate_button {
        @apply px-2 py-1 text-xs;
        margin: 0 1px;
    }
    
    /* Stack DataTable controls vertically on mobile */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        @apply block w-full mb-3;
    }
    
    .dataTables_wrapper .dataTables_filter {
        @apply text-right;
    }
    
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        @apply block w-full text-center mt-3;
    }
}

/* Extra small screens */
@media (max-width: 480px) {
    .dataTables_wrapper {
        padding: 0.25rem;
    }
    
    #programs-table tbody td {
        @apply px-1 py-1;
        max-width: 100px;
        font-size: 11px;
    }
    
    #programs-table thead th {
        @apply px-1 py-1;
        font-size: 11px;
    }
    
    .dataTables_paginate .paginate_button {
        @apply px-1;
        font-size: 11px;
    }
}
</style>
@endsection 