<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!doctype html>
<html lang="en">

<head>
    <?= $this->include('admin/link'); ?>
    <link rel="stylesheet" href="<?= base_url('/assets/css/toast.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/common.css') ?>" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
    html,
    body {
        margin: 0;
        padding: 0;
    }

    #main-wrapper,
    .page-wrapper {
        padding-top: 0 !important;
    }

    .body-wrapper,
    .body-wrapper-inner {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .app-header,
    .navbar {
        top: 0 !important;
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #0069d9;
        border-radius: 8px;
        padding: 6px 12px;
        margin-left: 0.5em;
        box-shadow: 0 2px 4px rgba(0, 105, 217, 0.1);
    }

    .dataTables_wrapper .dataTables_length select {
        border-radius: 6px;
    }

    /* Remove forced border */
    .border {
        border: none !important;
    }

    /* Smaller font size for table header and row data + compact row height */
    #resultsTable th,
    #resultsTable td {
        font-size: 0.8rem;
        padding: 0.2rem 0.4rem;
        line-height: 1.2;
        vertical-align: middle;
    }

    /* Truncate long table headers with ellipsis instead of wrapping to next line */
    #resultsTable th {
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Reserve space for DataTables sort icons and keep them beside the header text */
    #resultsTable th.sorting,
    #resultsTable th.sorting_asc,
    #resultsTable th.sorting_desc,
    #resultsTable th.sorting_asc_disabled,
    #resultsTable th.sorting_desc_disabled {
        position: relative;
        padding-right: 26px !important;
    }

    #resultsTable th.sorting:before,
    #resultsTable th.sorting:after,
    #resultsTable th.sorting_asc:before,
    #resultsTable th.sorting_asc:after,
    #resultsTable th.sorting_desc:before,
    #resultsTable th.sorting_desc:after,
    #resultsTable th.sorting_asc_disabled:before,
    #resultsTable th.sorting_asc_disabled:after,
    #resultsTable th.sorting_desc_disabled:before,
    #resultsTable th.sorting_desc_disabled:after {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
    }

    #resultsTable th.sorting:before,
    #resultsTable th.sorting_asc:before,
    #resultsTable th.sorting_desc:before,
    #resultsTable th.sorting_asc_disabled:before,
    #resultsTable th.sorting_desc_disabled:before {
        right: 1em;
    }

    #resultsTable th.sorting:after,
    #resultsTable th.sorting_asc:after,
    #resultsTable th.sorting_desc:after,
    #resultsTable th.sorting_asc_disabled:after,
    #resultsTable th.sorting_desc_disabled:after {
        right: 0.5em;
    }

    /* Compact icon-only edit/delete buttons so they don't inflate row height */
    #resultsTable .edit-record-btn,
    #resultsTable .delete-record-btn {
        padding: 0 0.15rem;
        font-size: 0.85rem;
        line-height: 1.2;
        border: none;
        background: transparent;
        vertical-align: middle;
    }

    #resultsTable .edit-record-btn:hover {
        color: #0d6efd;
    }

    #resultsTable .delete-record-btn:hover {
        color: #dc3545;
    }

    /* Truncate long cell content with ellipsis instead of wrapping to next line */
    #resultsTable td {
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Let the table shrink to fit its visible columns instead of stretching to full width.
       When only a few columns are shown, columns keep a natural compact width. */
    #resultsTable {
        width: auto !important;
        min-width: 100%;
        table-layout: auto;
    }

    /* Edit modal: make body scrollable so all fields are reachable even on small screens */
    #editRecordModal .modal-dialog {
        max-height: 90vh;
    }

    #editRecordModal .modal-content {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }

    #editRecordModal .modal-body {
        overflow-y: auto;
        flex: 1 1 auto;
        max-height: calc(90vh - 130px);
        /* leaves room for header + footer */
        -webkit-overflow-scrolling: touch;
    }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar -->
        <?php include 'sidebar.php'; ?>
        <!-- Main wrapper -->
        <div class="body-wrapper">
            <!-- Header -->
            <?= $this->include('admin/header'); ?>

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex mb-3 align-items-center">
                                        <h4 class="card-title mb-0">All Data</h4>
                                        <div class="ms-auto d-flex gap-2">
                                            <button type="button" id="deleteSelectedBtn"
                                                class="btn btn-outline-danger btn-sm shadow-sm d-none">
                                                <i class="ti ti-trash me-2"></i> Delete Selected (<span
                                                    id="selectedCount">0</span>)
                                            </button>
                                            
                                            <button type="button" onclick="downloadExcel()"
                                                class="btn btn-outline-success btn-sm shadow-sm">
                                                <i class="ti ti-download me-2"></i> Download Excel
                                            </button>
                                            <button type="button" id="columnSettingsBtn"
                                                class="btn btn-outline-secondary btn-sm shadow-sm">
                                                <i class="ti ti-settings me-2"></i> Settings
                                            </button>
                                        </div>
                                    </div>

                                    <div class="table-responsive border rounded-2">
                                        <table id="resultsTable" class="table table-striped table-hover align-middle">
                                            <thead class="table-info">
                                                <tr>
                                                    <th><input type="checkbox" id="selectAllCheckbox"
                                                            title="Select All"></th>
                                                    <th>ID</th>
                                                    <th>Reg Date</th>
                                                    <th>Reg Mon</th>
                                                    <th>Reg Num</th>
                                                    <th>Owner</th>
                                                    <th>Address</th>
                                                    <th>Make</th>
                                                    <th>Mode</th>
                                                    <th>Fuel</th>
                                                    <th>Sale</th>
                                                    <th>Seat</th>
                                                    <th>C C</th>
                                                    <th>Mobile</th>
                                                    <th>Expiry</th>
                                                    <th>Prev Insurance</th>
                                                    <th>Telecaller</th>
                                                    <th>Data Upload</th>
                                                    <th>Action Taken</th>
                                                    <th>Important</th>
                                                    <th>Interested</th>
                                                    <th>Already Sale</th>
                                                    <th>Sale In GB</th>
                                                    <th>Modify Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>

                                        </table>

                                    </div>

                                    <!-- Edit Record Modal -->
                                    <div class="modal fade" id="editRecordModal" tabindex="-1"
                                        aria-labelledby="editRecordModalLabel" aria-hidden="true">
                                        <div
                                            class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editRecordModalLabel">
                                                        <i class="ti ti-pencil me-2"></i>Edit Record
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form id="editRecordForm" method="post">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="recordId" id="editRecordId" value="">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label">Reg Date</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="regDate">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Reg Month</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="regDateMonth">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Reg Number</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="regNumber">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Owner Name</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="ownerName">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Mobile</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="mobile">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Expiry Date</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="expiryDate">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Vehicle Maker</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="vehicleMaker">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Vehicle Model</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="vehicleModel">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Fuel Type</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="fuelType">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Sale Amount</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="saleAmt">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Seat Capacity</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="seatCapacity">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Cubic Capacity</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="cubicCapacity">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Prev Insurance Company</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="prevInsuCompany">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Telecaller</label>
                                                                <select class="form-select form-select-sm"
                                                                    name="telecaller">
                                                                    <option value="">-- Select Telecaller --</option>
                                                                    <?php if (!empty($employees)): ?>
                                                                    <?php foreach ($employees as $emp): ?>
                                                                    <option value="<?= esc($emp['employeeId']) ?>">
                                                                        <?= esc($emp['name']) ?>
                                                                    </option>
                                                                    <?php endforeach; ?>
                                                                    <?php endif; ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <label class="form-label">Address</label>
                                                                <textarea class="form-control form-control-sm"
                                                                    name="address" rows="2"></textarea>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Action Taken</label>
                                                                <select class="form-select form-select-sm"
                                                                    name="actionTaken">
                                                                    <option value="0">No</option>
                                                                    <option value="1">Yes</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Important</label>
                                                                <select class="form-select form-select-sm"
                                                                    name="isImportant">
                                                                    <option value="0">No</option>
                                                                    <option value="1">Yes</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Interested</label>
                                                                <select class="form-select form-select-sm"
                                                                    name="isIntrested">
                                                                    <option value="0">No</option>
                                                                    <option value="1">Yes</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Already Sale</label>
                                                                <select class="form-select form-select-sm"
                                                                    name="alreadySale">
                                                                    <option value="0">No</option>
                                                                    <option value="1">Yes</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label">Sale In GB</label>
                                                                <select class="form-select form-select-sm"
                                                                    name="saleInGb">
                                                                    <option value="0">No</option>
                                                                    <option value="1">Yes</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary"
                                                            id="saveRecordBtn">
                                                            <i class="ti ti-device-floppy me-1"></i> Save Changes
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Edit Record Modal -->

                                    <!-- Delete Record Confirmation Modal -->
                                    <div class="modal fade" id="deleteRecordModal" tabindex="-1"
                                        aria-labelledby="deleteRecordModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteRecordModalLabel">
                                                        <i class="ti ti-alert-triangle me-2"></i>Confirm Deletion
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-1">Are you sure you want to delete this record?</p>
                                                    <p class="mb-0 text-muted">
                                                        Record ID: <strong id="deleteRecordIdLabel"></strong>
                                                        <span id="deleteRecordNameLabel"></span>
                                                    </p>
                                                    <p class="mt-2 mb-0 text-danger small">
                                                        <i class="ti ti-info-circle me-1"></i>This action cannot be
                                                        undone.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                                                        <i class="ti ti-trash me-1"></i> Yes, Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Delete Record Confirmation Modal -->

                                    <!-- Column Settings Modal -->
                                    <div class="modal fade" id="columnSettingsModal" tabindex="-1"
                                        aria-labelledby="columnSettingsModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="columnSettingsModalLabel">
                                                        <i class="ti ti-settings me-2"></i>Display Fields
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <p class="text-muted small mb-0 me-auto">Select the columns you
                                                            want to see in the table.</p>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-secondary me-1"
                                                            id="selectAllColumnsBtn">
                                                            Select All
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                            id="selectNoneColumnsBtn">
                                                            Select None
                                                        </button>
                                                    </div>
                                                    <div id="columnSettingsList" class="row g-2"></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary"
                                                        id="resetColumnsBtn">
                                                        Reset to Default
                                                    </button>
                                                    <button type="button" class="btn btn-primary" id="saveColumnsBtn">
                                                        <i class="ti ti-device-floppy me-1"></i> Save
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Column Settings Modal -->

                                    <!-- Bulk Delete Confirmation Modal -->
                                    <div class="modal fade" id="bulkDeleteModal" tabindex="-1"
                                        aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="bulkDeleteModalLabel">
                                                        <i class="ti ti-alert-triangle me-2"></i>Confirm Bulk Deletion
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-1">
                                                        Are you sure you want to delete
                                                        <strong id="bulkDeleteCountLabel">0</strong> selected record(s)?
                                                    </p>
                                                    <p class="mt-2 mb-0 text-danger small">
                                                        <i class="ti ti-info-circle me-1"></i>This action cannot be
                                                        undone.
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="button" class="btn btn-danger"
                                                        id="confirmBulkDeleteBtn">
                                                        <i class="ti ti-trash me-1"></i> Yes, Delete All
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Bulk Delete Confirmation Modal -->
                                </div>
                            </div>
                            <!-- End Info Table -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $this->include('admin/script'); ?>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#resultsTable')) {
            $('#resultsTable').DataTable({
                // Server-side processing: only the current page chunk is
                // fetched from the server, so the page loads fast even
                // with 10,000+ records (instead of loading all rows).
                processing: true,
                serverSide: true,
                ajax: "<?= site_url('admin/all-data-api') ?>",
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100, 200],
                order: [[1, 'asc']],
                columns: [
                    { data: 'select', orderable: false, searchable: false },
                    { data: 'recordId' },
                    { data: 'regDate' },
                    { data: 'regDateMonth' },
                    { data: 'regNumber' },
                    { data: 'ownerName' },
                    { data: 'address' },
                    { data: 'vehicleMaker' },
                    { data: 'vehicleModel' },
                    { data: 'fuelType' },
                    { data: 'saleAmt' },
                    { data: 'seatCapacity' },
                    { data: 'cubicCapacity' },
                    { data: 'mobile' },
                    { data: 'expiryDate' },
                    { data: 'prevInsuCompany' },
                    { data: 'telecaller' },
                    { data: 'dataUploadDate' },
                    { data: 'actionTaken' },
                    { data: 'isImportant' },
                    { data: 'isIntrested' },
                    { data: 'alreadySale' },
                    { data: 'saleInGb' },
                    { data: 'modifiyDate' },
                    { data: 'action', orderable: false, searchable: false }
                ],
                // Re-apply the row highlight color (already-sold / action-taken)
                createdRow: function(row, data, dataIndex) {
                    if (data.rowStyle) {
                        $(row).find('td').attr('style', data.rowStyle);
                    }
                },
                language: {
                    search: "Search:",
                    paginate: {
                        previous: "Prev",
                        next: "Next"
                    },
                    zeroRecords: "No matching records found."
                }
            });
        }

        // ===== Column Visibility Settings =====
        var COLUMNS_STORAGE_KEY = 'allDataVisibleColumns';
        var columnsDef = [{
                index: 1,
                label: 'Record ID',
                def: true
            },
            {
                index: 2,
                label: 'Reg Date',
                def: true
            },
            {
                index: 3,
                label: 'Reg Month',
                def: false
            },
            {
                index: 4,
                label: 'Reg Number',
                def: true
            },
            {
                index: 5,
                label: 'Owner Name',
                def: true
            },
            {
                index: 6,
                label: 'Address',
                def: false
            },
            {
                index: 7,
                label: 'Vehicle Maker',
                def: false
            },
            {
                index: 8,
                label: 'Vehicle Model',
                def: false
            },
            {
                index: 9,
                label: 'Fuel Type',
                def: false
            },
            {
                index: 10,
                label: 'Sale Amount',
                def: false
            },
            {
                index: 11,
                label: 'Seat Capacity',
                def: true
            },
            {
                index: 12,
                label: 'Cubic Capacity',
                def: true
            },
            {
                index: 13,
                label: 'Mobile',
                def: true
            },
            {
                index: 14,
                label: 'Expiry',
                def: true
            },
            {
                index: 15,
                label: 'Prev Insurance Company',
                def: false
            },
            {
                index: 16,
                label: 'Telecaller',
                def: true
            },
            {
                index: 17,
                label: 'Data Upload Date',
                def: true
            },
            {
                index: 18,
                label: 'Action Taken',
                def: false
            },
            {
                index: 19,
                label: 'Important',
                def: false
            },
            {
                index: 20,
                label: 'Interested',
                def: false
            },
            {
                index: 21,
                label: 'Already Sale',
                def: false
            },
            {
                index: 22,
                label: 'Sale In GB',
                def: false
            },
            {
                index: 23,
                label: 'Modify Date',
                def: false
            }
        ];

        function getVisibleColumns() {
            var stored = localStorage.getItem(COLUMNS_STORAGE_KEY);
            if (stored !== null) {
                try {
                    return JSON.parse(stored);
                } catch (e) {
                    localStorage.removeItem(COLUMNS_STORAGE_KEY);
                }
            }
            return columnsDef.filter(function(c) {
                return c.def;
            }).map(function(c) {
                return c.index;
            });
        }

        function applyColumnVisibility() {
            var visible = getVisibleColumns();
            var table = $('#resultsTable').DataTable();
            columnsDef.forEach(function(col) {
                table.column(col.index).visible(visible.indexOf(col.index) !== -1, false);
            });
            table.columns.adjust().draw(false);
        }

        function buildColumnSettings() {
            var visible = getVisibleColumns();
            var $list = $('#columnSettingsList');
            $list.empty();
            columnsDef.forEach(function(col) {
                var checked = visible.indexOf(col.index) !== -1 ? ' checked' : '';
                $list.append(
                    '<div class="col-6">' +
                    '<div class="form-check">' +
                    '<input class="form-check-input column-check" type="checkbox" value="' + col
                    .index + '" id="colCheck' + col.index + '"' + checked + '>' +
                    '<label class="form-check-label" for="colCheck' + col.index + '">' + col.label +
                    '</label>' +
                    '</div>' +
                    '</div>'
                );
            });
        }

        var columnSettingsModal = new bootstrap.Modal(document.getElementById('columnSettingsModal'));

        $('#columnSettingsBtn').on('click', function() {
            buildColumnSettings();
            columnSettingsModal.show();
        });

        $('#saveColumnsBtn').on('click', function() {
            var visible = [];
            $('.column-check:checked').each(function() {
                visible.push(parseInt($(this).val(), 10));
            });
            try {
                localStorage.setItem(COLUMNS_STORAGE_KEY, JSON.stringify(visible));
            } catch (e) {
                /* storage unavailable - apply for this session only */
            }
            applyColumnVisibility();
            columnSettingsModal.hide();
        });

        $('#resetColumnsBtn').on('click', function() {
            localStorage.removeItem(COLUMNS_STORAGE_KEY);
            buildColumnSettings();
            applyColumnVisibility();
        });

        // Quick actions inside the settings modal
        $('#selectAllColumnsBtn').on('click', function() {
            $('.column-check').prop('checked', true);
        });

        $('#selectNoneColumnsBtn').on('click', function() {
            $('.column-check').prop('checked', false);
        });

        // Apply saved/default visibility right after table init
        applyColumnVisibility();

        // Show full cell/header content as tooltip on hover for truncated cells
        function setCellTitles() {
            $('#resultsTable td, #resultsTable th').each(function() {
                var text = $(this).text().trim();
                if (text) {
                    $(this).attr('title', text);
                }
            });
        }
        $('#resultsTable').on('draw.dt', setCellTitles);
        setCellTitles();

        // ===== Edit Record Modal =====
        var editRecordModal = new bootstrap.Modal(document.getElementById('editRecordModal'));

        // ===== Bulk Select & Delete =====
        function updateSelectedCount() {
            var count = $('.row-checkbox:checked').length;
            $('#selectedCount').text(count);
            $('#deleteSelectedBtn').toggleClass('d-none', count === 0);
        }

        // Select all / deselect all checkboxes on current page
        $('#selectAllCheckbox').on('change', function() {
            var checked = $(this).prop('checked');
            $('.row-checkbox').prop('checked', checked);
            updateSelectedCount();
        });

        // Keep header checkbox in sync when individual rows are toggled
        $(document).on('change', '.row-checkbox', function() {
            var total = $('.row-checkbox').length;
            var checked = $('.row-checkbox:checked').length;
            $('#selectAllCheckbox').prop('checked', total > 0 && total === checked);
            updateSelectedCount();
        });

        // ===== Bulk Delete Modal =====
        var bulkDeleteModal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));

        $('#deleteSelectedBtn').on('click', function() {
            var count = $('.row-checkbox:checked').length;
            if (count === 0) {
                return;
            }
            $('#bulkDeleteCountLabel').text(count);
            bulkDeleteModal.show();
        });

        // Confirm bulk deletion via AJAX
        $('#confirmBulkDeleteBtn').on('click', function() {
            var ids = [];
            $('.row-checkbox:checked').each(function() {
                ids.push($(this).val());
            });
            if (ids.length === 0) {
                return;
            }
            var $btn = $(this);
            $btn.prop('disabled', true);
            $.ajax({
                url: '<?= site_url('admin/delete-records') ?>',
                type: 'POST',
                data: {
                    'recordIds[]': ids,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false);
                    if (res && res.success) {
                        bulkDeleteModal.hide();
                        location.reload();
                    } else {
                        alert((res && res.message) ? res.message :
                            'Failed to delete selected records');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    alert('Failed to delete selected records. Please try again.');
                }
            });
        });

        // ===== Delete Record Modal =====
        var deleteRecordModal = new bootstrap.Modal(document.getElementById('deleteRecordModal'));
        var deleteRecordId = null;

        // Open delete confirmation modal
        $(document).on('click', '.delete-record-btn', function() {
            deleteRecordId = $(this).data('record-id');
            var name = $(this).data('record-name');
            $('#deleteRecordIdLabel').text(deleteRecordId);
            $('#deleteRecordNameLabel').text(name ? ' (' + name + ')' : '');
            deleteRecordModal.show();
        });

        // Confirm deletion via AJAX
        $('#confirmDeleteBtn').on('click', function() {
            if (!deleteRecordId) {
                return;
            }
            var $btn = $(this);
            $btn.prop('disabled', true);
            $.ajax({
                url: '<?= site_url('admin/delete-record') ?>',
                type: 'POST',
                data: {
                    recordId: deleteRecordId,
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                },
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false);
                    if (res && res.success) {
                        deleteRecordModal.hide();
                        location.reload();
                    } else {
                        alert((res && res.message) ? res.message :
                            'Failed to delete record');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    alert('Failed to delete record. Please try again.');
                }
            });
        });

        // Open modal and populate fields with the clicked record
        $(document).on('click', '.edit-record-btn', function() {
            var d = $(this).data('record');
            if (!d) {
                return;
            }
            var $form = $('#editRecordForm');
            $form.find('[name="recordId"]').val(d.recordId);
            $form.find('[name="regDate"]').val(d.regDate);
            $form.find('[name="regDateMonth"]').val(d.regDateMonth);
            $form.find('[name="regNumber"]').val(d.regNumber);
            $form.find('[name="ownerName"]').val(d.ownerName);
            $form.find('[name="address"]').val(d.address);
            $form.find('[name="vehicleMaker"]').val(d.vehicleMaker);
            $form.find('[name="vehicleModel"]').val(d.vehicleModel);
            $form.find('[name="fuelType"]').val(d.fuelType);
            $form.find('[name="saleAmt"]').val(d.saleAmt);
            $form.find('[name="seatCapacity"]').val(d.seatCapacity);
            $form.find('[name="cubicCapacity"]').val(d.cubicCapacity);
            $form.find('[name="mobile"]').val(d.mobile);
            $form.find('[name="expiryDate"]').val(d.expiryDate);
            $form.find('[name="prevInsuCompany"]').val(d.prevInsuCompany);
            $form.find('[name="telecaller"]').val(d.telecallerId || '');
            $form.find('[name="actionTaken"]').val(String(d.actionTaken));
            $form.find('[name="isImportant"]').val(String(d.isImportant));
            $form.find('[name="isIntrested"]').val(String(d.isIntrested));
            $form.find('[name="alreadySale"]').val(String(d.alreadySale));
            $form.find('[name="saleInGb"]').val(String(d.saleInGb));
            editRecordModal.show();
        });

        // Save changes via AJAX
        $('#editRecordForm').on('submit', function(e) {
            e.preventDefault();
            var $btn = $('#saveRecordBtn');
            $btn.prop('disabled', true);
            $.ajax({
                url: '<?= site_url('admin/update-record') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    $btn.prop('disabled', false);
                    if (res && res.success) {
                        editRecordModal.hide();
                        location.reload();
                    } else {
                        alert((res && res.message) ? res.message :
                            'Failed to update record');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    alert('Failed to update record. Please try again.');
                }
            });
        });
    });

    function downloadExcel() {
        window.location.href = "<?= site_url('admin/export-calling-data') ?>";
    }
    </script>
</body>

</html>