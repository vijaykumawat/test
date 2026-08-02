<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <style>
    /* Ensure removing the topstrip leaves no empty gap */

    #main-wrapper[data-layout="vertical"][data-sidebar-position="fixed"] .left-sidebar {
        top: 0 !important;
    }


    .body-wrapper .container-fluid,
    .body-wrapper .container-sm,
    .body-wrapper .container-md,
    .body-wrapper .container-lg,
    .body-wrapper .container-xl,
    .body-wrapper .container-xxl {
        padding-top: 0px;
        max-width: 100%;
    }

    .app-header {
        top: 0px;
    }

    .blur {
        width: 100%;
        height: 100%;
        background: url("http://www.wohn-blogger.de/wp-content/themes/itheme2/skins/gray/images/body-bg.png") repeat scroll 0 0 #D1D1D1;
        color: #666666;
    }

    .alSale {
        width: 100%;
        height: 100%;
        background-color: #bd8c89;
    }

    .company-logo {
        max-height: 60px;
        /* keeps height consistent */
        max-width: 50%;
        /* prevents overflow on mobile */
        object-fit: contain;
        /* scales proportionally */
        display: block;
        margin: 0 auto;
    }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?= $this->include('employee/sidebar'); ?>
        <div class="body-wrapper">
            <?= $this->include('employee/header'); ?>
            <div id="dashboardFlash" class="container-fluid mt-3" style="padding-top: 50px; "></div>
            <?php if (session()->getFlashdata('success')): ?>
            <div class="container-fluid mt-3">
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
            <div class="container-fluid mt-3">
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('warning')): ?>
            <div class="container-fluid mt-3">
                <div class="alert alert-warning"><?= esc(session()->getFlashdata('warning')) ?></div>
            </div>
            <?php endif; ?>
            <div class="body-wrapper-inner" style="padding-top: 50px;margin-top: -50px;top: 0px;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-8 col-md-12 col-12 ">
                            <form id="saveLeadForm" action="<?php echo base_url();?>employee/save" method="post">
                                <!-- start Event Registration -->
                                <div class="card">
                                    <div class="form-actions">
                                        <div class="card-body">
                                            <div class="row">
                                                <!-- Left side: Client details -->
                                                <div class="col-md-12">
                                                    <div class="card mb-3">
                                                        <div
                                                            class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                            <h5>Client Details</h5>
                                                            <button type="button" class="btn btn-light btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#fieldSettingsModal">
                                                                <i class="ti ti-settings"></i> Settings
                                                            </button>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php
    // Helper to prettify labels
    function prettyLabel($key) {
        return ucwords(str_replace('_',' ', $key));
    }

    // Loop through all details
    if (!empty($record['details'])):
        foreach ($record['details'] as $key => $value):
            // Show field if preferences are empty OR this key is in preferences
            if (empty($fieldSettings) || in_array($key, $fieldSettings)):
    ?>
                                                            <p class="field-<?= esc($key) ?>">
                                                                <strong><?= prettyLabel($key) ?>:</strong>
                                                                <?= esc($value) ?>
                                                            </p>
                                                            <?php
            endif;
        endforeach;
    endif;
    ?>

                                                            <!-- Telecaller field (outside details array) -->
                                                            <?php if (empty($fieldSettings) || in_array('telecaller', $fieldSettings)): ?>
                                                            <p class="field-telecaller"><strong>Telecaller:</strong>
                                                                <?= esc($record['telecaller_name'] ?? '') ?></p>
                                                            <?php endif; ?>

                                                            <div class="mb-3">
                                                                <label for="remark" class="form-label">Remark:</label>
                                                                <textarea id="remark" class="form-control"
                                                                    rows="2"></textarea>
                                                            </div>

                                                            <div class="d-flex gap-2">
                                                                <button class="btn btn-success">Save</button>
                                                                <button class="btn btn-info">Quotation</button>
                                                                <button class="btn btn-secondary">Upload Policy</button>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>

                                                <!-- Right side: History -->
                                            </div>
                                        </div>
                                        <div id="saveActionFeedback" class="mt-2 small text-success"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="fieldSettingsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Choose Fields to Display</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="fieldSettingsForm">
                        <?php if (!empty($record['details'])): ?>
                        <?php foreach ($record['details'] as $key => $value): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="<?= esc($key) ?>"
                                id="field<?= ucfirst($key) ?>" checked>
                            <label class="form-check-label" for="field<?= ucfirst($key) ?>">
                                <?= ucfirst($key) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="applySettings">Apply</button>
                </div>
            </div>
        </div>
    </div>



    <?= $this->include('admin/script'); ?>
    <script>
    document.getElementById('applySettings').addEventListener('click', function() {
        // Hide all fields first
        document.querySelectorAll('[class^="field-"]').forEach(el => el.style.display = 'none');

        // Show only checked fields
        document.querySelectorAll('#fieldSettingsForm input:checked').forEach(input => {
            let fieldClass = '.field-' + input.value;
            document.querySelectorAll(fieldClass).forEach(el => el.style.display = '');
        });

        // Close modal
        var modal = bootstrap.Modal.getInstance(document.getElementById('fieldSettingsModal'));
        modal.hide();
    });
    </script>
    <script>
    document.getElementById('applySettings').addEventListener('click', function() {
        let selected = [];
        document.querySelectorAll('#fieldSettingsForm input:checked').forEach(input => {
            selected.push(input.value);
        });

        // Call controller method via AJAX
        fetch("<?= base_url('employee/saveFieldSettings') ?>", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    fields: selected
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    // Apply immediately on page
                    document.querySelectorAll('[class^="field-"]').forEach(el => el.style.display = 'none');
                    selected.forEach(field => {
                        document.querySelectorAll('.field-' + field).forEach(el => el.style
                            .display = '');
                    });
                }
            });

        var modal = bootstrap.Modal.getInstance(document.getElementById('fieldSettingsModal'));
        modal.hide();
    });
    </script>

</body>

</html>