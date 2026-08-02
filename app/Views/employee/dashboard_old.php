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
        padding-top: 100px;
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
    </style>
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!-- Sidebar Start -->
        <?= $this->include('employee/sidebar'); ?>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <?= $this->include('employee/header'); ?>
            <!--  Header End -->
            <?php
              if (isset($isDataAvailable) && $isDataAvailable){
            ?>
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-8 col-md-12 col-12 align-self-center">
                            <form action="<?php echo base_url();?>employee/save" method="post">
                                <!-- start Event Registration -->
                                <div class="card 
                                        <?php if($actionTaken){ echo "blur"; } ?> 
                                        <?php if($alreadySale==1){ echo "alSale"; } ?> shadow"
                                    <?php if($actionTaken==0 && $alreadySale==0){ ?> style="background-color:#fcfcfc;"
                                    <?php } ?>>
                                    <div class="form-actions">
                                        <!-- <div class="card-body">
                                            <h3 class="card-title
                                        <div class="card-body border-top">
                                            <input type="hidden" id="recordId" name="recordId"
                                                value="<?php echo $recordId; ?>">
                                            <button type="submit" id=""
                                                class="btn btn-outline-success btn-sm shadow-sm">
                                                Save
                                            </button>
                                            <button type="button" id="cancelBtn"
                                                class="btn btn-outline-dark btn-sm shadow-sm">
                                                Cancel
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#quotationModal">
                                                Quotation
                                            </button>
                                        </div> -->
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-1">
                                                    <a
                                                        href="<?php echo base_url();?>employee/starRecord/<?php echo $recordId;?>/<?php if($isImportant){echo "0";}else{echo "1";}?>">
                                                        <i class="ti ti-star"
                                                            style="font-size:48px;color:<?php if(!$isImportant){echo 'black';}else{echo "#FFD700";} ?>"></i>
                                                    </a>
                                                </div>
                                                <div class="col-sm-11">
                                                    <dt class="" style="font-size:28px;">
                                                        <?php echo $ownerName;?></dt>

                                                </div>

                                            </div>
                                            <hr>
                                            <div class="row">
                                                <dt class="col-sm-2 text-end">Record Id</dt>
                                                <dd class="col-sm-4"><?php echo $recordId;?></dd>
                                                <dt class="col-sm-2 text-end">Address</dt>
                                                <dd class="col-sm-4"><?php echo $address;?></dd>
                                                <dt class="col-sm-2 text-end">Reg. Number</dt>
                                                <dd class="col-sm-4"><?php echo $regNumber;?></dd>


                                                <dt class="col-sm-2 text-end">Vehicle Model</dt>
                                                <dd class="col-sm-4"><?php echo $vehicleModel;?></dd>
                                                <dt class="col-sm-2 text-end">Reg. Date</dt>
                                                <dd class="col-sm-4"
                                                    style="font-weight: bold; background-color: yellow;">
                                                    <?php echo $regDate;?></dd>
                                                <dt class="col-sm-2 text-end">Vehicle Maker</dt>
                                                <dd class="col-sm-4"><?php echo $vehicleMaker;?></dd>
                                                <dt class="col-sm-2 text-end">Cubic Capacity</dt>
                                                <dd class="col-sm-4"><?php echo $cubicCapacity;?></dd>


                                                <dt class="col-sm-2 text-end">Seat Capacity</dt>
                                                <dd class="col-sm-4"><?php echo $seatCapacity;?></dd>
                                                <dt class="col-sm-2 text-end">Mobile No</dt>
                                                <dd class="col-sm-4"><a
                                                        href="https://wa.me/<?php echo $mobile;?>/?text=urlencodedtext"><?php echo $mobile;?></a>
                                                </dd>
                                                <dt class="col-sm-2 text-end">Finance</dt>
                                                <dd class="col-sm-4"> - </dd>
                                                <dt class="col-sm-2 text-end">Prev Insu Company</dt>
                                                <dd class="col-sm-4"><?php echo $prevInsuCompany;?></dd>
                                                <dt class="col-sm-2 text-end">Fuel Type</dt>
                                                <dd class="col-sm-4"><?php echo $fuelType;?></dd>
                                                <dt class="col-sm-2 text-end">Expiry Date</dt>
                                                <dd class="col-sm-4"
                                                    style="font-weight: bold; background-color: yellow;">
                                                    <?php echo $expiryDate;?></dd>
                                                <dt class="col-sm-2 text-end">Telecaller</dt>
                                                <dd class="col-sm-4"><?php echo $telecaller;?></dd>
                                                <dt class="col-sm-2 text-end">Sale Amount</dt>
                                                <dd class="col-sm-4"><?php echo $saleAmt;?></dd>
                                                <dt class="col-sm-2 text-end">Action</dt>
                                                <dd class="col-sm-4">
                                                    <?php if($actionTaken){ echo "Calling Done";} else{ echo "Not Yet";} ?>
                                                </dd>
                                                <dt class="col-sm-2 text-end">Status *</dt>
                                                <dd class="col-sm-3">
                                                    <div class="form-group">
                                                        <select class="form-control select2" placeholder="Select Status"
                                                            name="status" style="width: 100%;" autofocus required>
                                                            <option></option>
                                                            <option>Intrested - Quote Sent</option>
                                                            <option>Call Done - Cust not available in city</option>
                                                            <option>Call Not Received - Quote Sent</option>
                                                            <option>Today - Cust coming to office</option>
                                                            <option>Tommorrow - Cust coming to office</option>
                                                            <option>Need to Call Back</option>
                                                            <option>Already Sale</option>
                                                            <option>Sale In GB</option>
                                                            <option>Not Intrested</option>
                                                            <option>Wrong Number</option>
                                                            <option>Switch Off</option>
                                                            <option>Agent</option>
                                                            <option>Other</option>
                                                        </select>
                                                    </div>
                                                </dd>

                                                <div class="col-sm-1"></div>
                                                <dt class="col-sm-2 text-end">Remark</dt>
                                                <dd class="col-sm-3">
                                                    <div class="form-group">
                                                        <input type="text" name="remark" class="form-control"
                                                            placeholder="Enter ...">
                                                    </div>
                                                </dd>
                                            </div>
                                        </div>
                                        <div class="card-body border-top">
                                            <input type="hidden" id="recordId" name="recordId"
                                                value="<?php echo $recordId; ?>">

                                            <button type="submit" class="btn btn-success btn-sm shadow-sm ">
                                                Save
                                            </button>

                                            <button type="button" class="btn btn-primary btn-sm shadow-sm "
                                                data-bs-toggle="modal" data-bs-target="#quotationModal">
                                                Quotation
                                            </button>
                                        </div>

                                    </div>

                                </div>
                                <!-- end Event Registration -->
                            </form>
                        </div>

                        <div class="col-lg-4 col-md-12 col-12 ">
                            <!-- start Event Registration -->
                            <!--
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex mb-1 align-items-center">
                                        <h4 class="card-title mb-0">Calling History </h4>
                                        <div class="ms-auto flex-shrink-0">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="table-responsive border rounded-2" data-bs-theme="<?php if($actionTaken){ echo "dark";} else{ echo "";}?> <?php if($alreadySale==1){echo "alSale";} ?>">
                                        
                                        <table class="table table-sm text-nowrap table-<?php if($actionTaken){ echo "dark";} else{ echo "";}?> <?php if($alreadySale==1){echo "alSale";} ?>  mb-0 align-middle">
                                            <thead class="text-<?php if($actionTaken){ echo "dark";} else{ echo "";}?> <?php if($alreadySale==1){echo "alSale";} ?> fs-4">
                                                <tr>
                                                    <th>
                                                        <h6 class="fs-4 fw-semibold text-<?php if($actionTaken){ echo "white";} else{ echo "black";}?>  mb-0">Date</h6>
                                                    </th>
                                                    <th>
                                                        <h6 class="fs-4 fw-semibold text-<?php if($actionTaken){ echo "white";} else{ echo "black";}?> mb-0">Status</h6>
                                                    </th>
                                                    <th>
                                                        <h6 class="fs-4 fw-semibold text-<?php if($actionTaken){ echo "white";} else{ echo "black";}?> mb-0">Remark</h6>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    if($historyData){
                                                        foreach($historyData as $row){
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="ms-3">
                                                                 <p class="mb-0 fw-normal fs-4"><?php echo $row['dateCreated'];?></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="mb-0 fw-normal fs-4"><?php echo $row['status'];?></p>
                                                    </td>
                                                     <td>
                                                        <p class="mb-0 fw-normal fs-4"><?php echo $row['remark'];?></p>
                                                    </td>
                                                </tr>
                                                <?php 
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table> 
                                    </div>
                                </div>
                            </div>-->

                            <div class="card 
                                        <?php if($actionTaken){ echo "blur"; } ?> 
                                        <?php if($alreadySale==1){ echo "alSale"; } ?> shadow"
                                <?php if($actionTaken==0 && $alreadySale==0){ ?> style="background-color:#fcfcfc;"
                                <?php } ?>>
                                <div class="form-actions">
                                    <div class="card-body">
                                        <h3 class="card-title mb-3">History</h3>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr
                                                    class="<?php if($actionTaken){ echo "blur";} else{ echo "";}?> <?php if($alreadySale==1){echo "alSale";} ?>">
                                                    <th style="width:120px">Date</th>
                                                    <th>Status</th>
                                                    <th>Remark</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                    if($historyData){
                                                        foreach($historyData as $row){
                                                    ?>

                                                <tr
                                                    class="<?php if($actionTaken){ echo "blur";} else{ echo "";}?> <?php if($alreadySale==1){echo "alSale";} ?>">
                                                    <td><?php echo $row['dateCreated'];?></td>
                                                    <td><?php echo $row['status'];?></td>
                                                    <td><?php echo $row['remark'];?></td>
                                                </tr>

                                                <?php 
                                                    }
                                                }
                                                ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                                                    
                }
                else{
              ?>
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="form-actions">
                                    <div class="card-body">
                                        <h3> Oops! &nbsp; Record not found</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php        
                }
             ?>
            <!-- Quotation Modal -->
            <!-- Quotation Modal -->
            <div class="modal fade" id="quotationModal" tabindex="-1" aria-labelledby="quotationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog ">
                    <div class="modal-content">
                        <form id="quotationForm" action="<?= base_url('Tcpdfexample/quote'); ?>" method="post">

                        <div class="modal-header">
                            <h5 class="modal-title" id="quotationModalLabel">Generate Quotation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" name="recordId" value="<?= esc($recordId ?? '') ?>">
                            <input type="hidden" name="company" value="SHRIRAM">
                            <input type="hidden" name="idv" value="600000">
                            <input type="hidden" name="ncb" value="0">
                            <input type="hidden" name="cc" value="<?= esc($cubicCapacity ?? 0) ?>">
                            <input type="hidden" name="cng" value="0">
                            <input type="hidden" name="zero_dep" value="0">
                            <input type="hidden" name="seatCapacity" value="<?= esc($seatCapacity ?? '') ?>">
                            <input type="hidden" name="regDate" value="<?= esc($regDate ?? '') ?>">
                            <input type="hidden" name="ownerName" value="<?= esc($ownerName ?? '') ?>">
                            <input type="hidden" name="regNumber" value="<?= esc($regNumber ?? '') ?>">
                            <input type="hidden" name="vehicleModel" value="<?= esc($vehicleModel ?? '') ?>">
                            <input type="hidden" name="vehicleMaker" value="<?= esc($vehicleMaker ?? '') ?>">
                            <input type="hidden" name="mobile" value="<?= esc($mobile ?? '') ?>">
                            <input type="hidden" name="fuelType" value="<?= esc($fuelType ?? '') ?>">



                            <ul class="nav nav-tabs mb-3" id="quotationCompanyTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="shriram-tab" data-bs-toggle="tab" data-bs-target="#shriram-pane" type="button" role="tab" aria-controls="shriram-pane" aria-selected="true">Shriram</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="sbi-tab" data-bs-toggle="tab" data-bs-target="#sbi-pane" type="button" role="tab" aria-controls="sbi-pane" aria-selected="false">SBI</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="reliance-tab" data-bs-toggle="tab" data-bs-target="#reliance-pane" type="button" role="tab" aria-controls="reliance-pane" aria-selected="false">Reliance</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="quotationCompanyTabsContent">
                                <div class="tab-pane fade show active" id="shriram-pane" data-company="SHRIRAM" data-idv-input="shriram_idv" data-ncb-input="shriram_ncb" data-cc-input="shriram_cc" data-cng-input="shriram_cng" data-zero-dep-input="shriram_zero_dep" role="tabpanel" aria-labelledby="shriram-tab">
                                    <p class="text-muted small mb-3">Fill in the Shriram quotation details.</p>
                                    <div class="mb-1">
                                        <label>IDV</label>
                                        <input type="number" name="shriram_idv" class="form-control" value="600000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>NCB</label>
                                        <select name="shriram_ncb" class="form-control">
                                            <option value="0">0%</option>
                                            <option value="20">20%</option>
                                            <option value="25">25%</option>
                                            <option value="35">35%</option>
                                            <option value="45">45%</option>
                                            <option value="50">50%</option>
                                        </select>
                                    </div>
                                    <!--
                                    <div class="mb-3">
                                        <label>Cubic Capacity (CC)</label>
                                        <input type="number" name="shriram_cc" class="form-control" value="1197" required>
                                    </div>-->
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input secondary" type="checkbox" name="shriram_cng" value="1">
                                        <label class="form-check-label">CNG Kit Installed</label>
                                    </div>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input secondary" type="checkbox" name="shriram_zero_dep" value="1">
                                        <label class="form-check-label">Zero Dep</label>
                                    </div>
                                    <div class="mt-3 p-3 border rounded bg-light">
                                        <div class="small text-muted">Estimated Final Premium</div>
                                        <div class="h5 mb-0 text-primary quotation-premium-value">₹ 0.00</div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="sbi-pane" data-company="SBI" data-idv-input="sbi_idv" data-ncb-input="sbi_ncb" data-cc-input="sbi_cc" data-cng-input="sbi_cng" data-zero-dep-input="sbi_zero_dep" role="tabpanel" aria-labelledby="sbi-tab">
                                    <p class="text-muted small mb-3">Fill in the SBI quotation details.</p>
                                    <div class="mb-1">
                                        <label>IDV</label>
                                        <input type="number" name="sbi_idv" class="form-control" value="600000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>NCB</label>
                                        <select name="sbi_ncb" class="form-control">
                                            <option value="0">0%</option>
                                            <option value="20">20%</option>
                                            <option value="25">25%</option>
                                            <option value="35">35%</option>
                                            <option value="45">45%</option>
                                            <option value="50">50%</option>
                                        </select>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input secondary" type="checkbox" name="sbi_cng" value="1">
                                        <label class="form-check-label">CNG Kit Installed</label>
                                    </div>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input secondary" type="checkbox" name="sbi_zero_dep" value="1">
                                        <label class="form-check-label">Zero Dep</label>
                                    </div>
                                    <div class="mt-3 p-3 border rounded bg-light">
                                        <div class="small text-muted">Estimated Final Premium</div>
                                        <div class="h5 mb-0 text-primary quotation-premium-value">₹ 0.00</div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="reliance-pane" data-company="RELIANCE" data-idv-input="reliance_idv" data-ncb-input="reliance_ncb" data-cc-input="reliance_cc" data-cng-input="reliance_cng" data-zero-dep-input="reliance_zero_dep" role="tabpanel" aria-labelledby="reliance-tab">
                                    <p class="text-muted small mb-3">Fill in the Reliance quotation details.</p>
                                    <div class="mb-1">
                                        <label>IDV</label>
                                        <input type="number" name="reliance_idv" class="form-control" value="600000" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>NCB</label>
                                        <select name="reliance_ncb" class="form-control">
                                            <option value="0">0%</option>
                                            <option value="20">20%</option>
                                            <option value="25">25%</option>
                                            <option value="35">35%</option>
                                            <option value="45">45%</option>
                                            <option value="50">50%</option>
                                        </select>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input secondary" type="checkbox" name="reliance_cng" value="1">
                                        <label class="form-check-label">CNG Kit Installed</label>
                                    </div>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input secondary" type="checkbox" name="reliance_zero_dep" value="1">
                                        <label class="form-check-label">Zero Dep</label>
                                    </div>
                                    <div class="mt-3 p-3 border rounded bg-light">
                                        <div class="small text-muted">Estimated Final Premium</div>
                                        <div class="h5 mb-0 text-primary quotation-premium-value">₹ 0.00</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-rounded btn-primary" type="submit">Generate Quotation PDF</button>
                        </div>
                        </form>

                        <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const quotationForm = document.getElementById('quotationForm');
                            if (!quotationForm) {
                                return;
                            }

                            const hiddenFields = {
                                company: quotationForm.querySelector('input[name="company"]'),
                                idv: quotationForm.querySelector('input[name="idv"]'),
                                ncb: quotationForm.querySelector('input[name="ncb"]'),
                                cc: quotationForm.querySelector('input[name="cc"]'),
                                cng: quotationForm.querySelector('input[name="cng"]'),
                                zero_dep: quotationForm.querySelector('input[name="zero_dep"]')
                            };

                            const insurerConfig = {
                                SHRIRAM: {
                                    od_rates: {
                                        upto_1000: 3.284,
                                        '1001-1200': 3.448,
                                        '1201-1500': 3.500
                                    },
                                    cng_rates: {
                                        upto_1000: 0.164,
                                        '1001-1200': 0.172,
                                        '1201-1500': 0.180
                                    },
                                    tp_rates: {
                                        upto_1000: { basic_liability: 6040, per_passenger: 1162, cng_liability: 60, ll_driver: 50 },
                                        '1001-1200': { basic_liability: 7940, per_passenger: 978, cng_liability: 60, ll_driver: 50 },
                                        '1201-1500': { basic_liability: 9000, per_passenger: 1000, cng_liability: 60, ll_driver: 50 }
                                    },
                                    zero_dep_rates: {
                                        upto_1000: { age_0_1: 0.0055, age_1_2: 0.0060, age_2_3: 0.0070, age_3_4: 0.0080, age_4_5: 0.0090 },
                                        '1001-1500': { age_0_1: 0.0085, age_1_2: 0.0105, age_2_3: 0.0115, age_3_4: 0.0150, age_4_5: 0.0200 },
                                        '1501-2000': { age_0_1: 0.0050, age_1_2: 0.0075, age_2_3: 0.0080, age_3_4: 0.0120, age_4_5: 0.0180 }
                                    },
                                    od_discount: { detariff: 75 }
                                },
                                SBI: {
                                    od_rates: {
                                        upto_1000: 3.284,
                                        '1001-1200': 3.448,
                                        '1201-1500': 3.500
                                    },
                                    cng_rates: {
                                        upto_1000: 0.164,
                                        '1001-1200': 0.172,
                                        '1201-1500': 0.180
                                    },
                                    tp_rates: {
                                        upto_1000: { basic_liability: 6040, per_passenger: 1162, cng_liability: 60, ll_driver: 50 },
                                        '1001-1200': { basic_liability: 7940, per_passenger: 978, cng_liability: 60, ll_driver: 50 },
                                        '1201-1500': { basic_liability: 9000, per_passenger: 1000, cng_liability: 60, ll_driver: 50 }
                                    },
                                    zero_dep_rates: {
                                        upto_1000: { age_0_1: 0.0055, age_1_2: 0.0060, age_2_3: 0.0070, age_3_4: 0.0080, age_4_5: 0.0090 },
                                        '1001-1500': { age_0_1: 0.0085, age_1_2: 0.0105, age_2_3: 0.0115, age_3_4: 0.0150, age_4_5: 0.0200 },
                                        '1501-2000': { age_0_1: 0.0050, age_1_2: 0.0075, age_2_3: 0.0080, age_3_4: 0.0120, age_4_5: 0.0180 }
                                    },
                                    od_discount: { detariff: 75 }
                                },
                                RELIANCE: {
                                    od_rates: {
                                        upto_1000: 3.284,
                                        '1001-1200': 3.448,
                                        '1201-1500': 3.500
                                    },
                                    cng_rates: {
                                        upto_1000: 0.164,
                                        '1001-1200': 0.172,
                                        '1201-1500': 0.180
                                    },
                                    tp_rates: {
                                        upto_1000: { basic_liability: 6040, per_passenger: 1162, cng_liability: 60, ll_driver: 50 },
                                        '1001-1200': { basic_liability: 7940, per_passenger: 978, cng_liability: 60, ll_driver: 50 },
                                        '1201-1500': { basic_liability: 9000, per_passenger: 1000, cng_liability: 60, ll_driver: 50 }
                                    },
                                    zero_dep_rates: {
                                        upto_1000: { age_0_1: 0.0055, age_1_2: 0.0060, age_2_3: 0.0070, age_3_4: 0.0080, age_4_5: 0.0090 },
                                        '1001-1500': { age_0_1: 0.0085, age_1_2: 0.0105, age_2_3: 0.0115, age_3_4: 0.0150, age_4_5: 0.0200 },
                                        '1501-2000': { age_0_1: 0.0050, age_1_2: 0.0075, age_2_3: 0.0080, age_3_4: 0.0120, age_4_5: 0.0180 }
                                    },
                                    od_discount: { detariff: 75 }
                                }
                            };

                            const getCCBand = function (cc) {
                                if (cc <= 1000) {
                                    return 'upto_1000';
                                }
                                if (cc >= 1001 && cc <= 1200) {
                                    return '1001-1200';
                                }
                                if (cc >= 1201 && cc <= 1500) {
                                    return '1201-1500';
                                }
                                return null;
                            };

                            const getZeroDepBand = function (cc) {
                                if (cc <= 1000) {
                                    return 'upto_1000';
                                }
                                if (cc >= 1001 && cc <= 1500) {
                                    return '1001-1500';
                                }
                                if (cc >= 1501 && cc <= 2000) {
                                    return '1501-2000';
                                }
                                return null;
                            };

                            const getAgeBand = function (age) {
                                if (age <= 1) {
                                    return 'age_0_1';
                                }
                                if (age <= 2) {
                                    return 'age_1_2';
                                }
                                if (age <= 3) {
                                    return 'age_2_3';
                                }
                                if (age <= 4) {
                                    return 'age_3_4';
                                }
                                if (age <= 5) {
                                    return 'age_4_5';
                                }
                                return null;
                            };

                            const getPaneValue = function (pane, attribute) {
                                const input = pane.querySelector(`[name="${pane.dataset[attribute]}"]`);
                                return input ? input.value : '';
                            };

                            const getPaneCheckedState = function (pane, attribute) {
                                const input = pane.querySelector(`[name="${pane.dataset[attribute]}"]`);
                                return input && input.checked ? '1' : '0';
                            };

                            const formatCurrency = function (value) {
                                return '₹ ' + Number(value || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            };

                            const computePremium = function (pane) {
                                const company = pane.dataset.company || 'SHRIRAM';
                                const config = insurerConfig[company] || insurerConfig.SHRIRAM;
                                const idv = parseFloat(getPaneValue(pane, 'idvInput')) || 0;
                                const ncb = parseInt(getPaneValue(pane, 'ncbInput'), 10) || 0;
                                const cc = parseInt(quotationForm.querySelector('input[name="cc"]').value || 0, 10) || 0;
                                const cngInstalled = getPaneCheckedState(pane, 'cngInput') === '1';
                                const zeroDepSelected = getPaneCheckedState(pane, 'zeroDepInput') === '1';
                                const seatCapacity = parseInt(quotationForm.querySelector('input[name="seatCapacity"]').value || 5, 10) || 5;
                                const regDate = quotationForm.querySelector('input[name="regDate"]').value;
                                let age = 0;

                                if (regDate) {
                                    const regYear = new Date(regDate).getFullYear();
                                    if (!Number.isNaN(regYear)) {
                                        age = new Date().getFullYear() - regYear;
                                    }
                                }

                                const band = getCCBand(cc);
                                const odRate = band ? (config.od_rates[band] || 0) : 0;
                                const basicOD = (idv * odRate) / 100;
                                let cngKit = 0;
                                if (cngInstalled) {
                                    const cngRate = band ? (config.cng_rates[band] || 0) : 0;
                                    cngKit = (idv * cngRate / 100) + (cc <= 1000 ? 1 : 2);
                                }

                                const basicODPremium = basicOD + cngKit;
                                const odDiscountAmt = basicODPremium * (config.od_discount && config.od_discount.detariff ? config.od_discount.detariff : 0) / 100;
                                const basicAfterDiscount = basicODPremium - odDiscountAmt;
                                const ncbAmt = basicAfterDiscount * ncb / 100;
                                const ownDamagePremiumA = basicAfterDiscount - ncbAmt;

                                let liabilityB = 0;
                                const tpRate = band ? config.tp_rates[band] : null;
                                if (tpRate) {
                                    const basicLiability = tpRate.basic_liability || 0;
                                    const passengerCoverage = (tpRate.per_passenger || 0) * Math.max(seatCapacity - 1, 0);
                                    const cngLiability = cngInstalled ? (tpRate.cng_liability || 0) : 0;
                                    const llLiability = tpRate.ll_driver || 0;
                                    liabilityB = basicLiability + passengerCoverage + cngLiability + llLiability;
                                }

                                let zeroDep = 0;
                                if (zeroDepSelected) {
                                    const zeroBand = getZeroDepBand(cc);
                                    const ageBand = getAgeBand(age);
                                    const zeroDepRate = zeroBand && ageBand && config.zero_dep_rates && config.zero_dep_rates[zeroBand] ? config.zero_dep_rates[zeroBand][ageBand] : null;
                                    if (zeroDepRate) {
                                        zeroDep = idv * zeroDepRate;
                                    }
                                }

                                const totalPremiumWithoutGst = ownDamagePremiumA + liabilityB + zeroDep;
                                const sgst = totalPremiumWithoutGst * 9 / 100;
                                const cgst = totalPremiumWithoutGst * 9 / 100;
                                return totalPremiumWithoutGst + sgst + cgst;
                            };

                            const syncActivePane = function () {
                                const activePane = document.querySelector('#quotationCompanyTabsContent .tab-pane.active');
                                if (!activePane) {
                                    return;
                                }

                                if (hiddenFields.company) {
                                    hiddenFields.company.value = activePane.dataset.company || '';
                                }
                                if (hiddenFields.idv) {
                                    hiddenFields.idv.value = getPaneValue(activePane, 'idvInput');
                                }
                                if (hiddenFields.ncb) {
                                    hiddenFields.ncb.value = getPaneValue(activePane, 'ncbInput');
                                }
                                if (hiddenFields.cc) {
                                    hiddenFields.cc.value = quotationForm.querySelector('input[name="cc"]').value || hiddenFields.cc.value;
                                }
                                if (hiddenFields.cng) {
                                    hiddenFields.cng.value = getPaneCheckedState(activePane, 'cngInput');
                                }
                                if (hiddenFields.zero_dep) {
                                    hiddenFields.zero_dep.value = getPaneCheckedState(activePane, 'zeroDepInput');
                                }

                                const previewValue = activePane.querySelector('.quotation-premium-value');
                                if (previewValue) {
                                    previewValue.textContent = formatCurrency(computePremium(activePane));
                                }
                            };

                            document.querySelectorAll('#quotationCompanyTabs button[data-bs-toggle="tab"]').forEach(function (tab) {
                                tab.addEventListener('shown.bs.tab', syncActivePane);
                            });

                            quotationForm.querySelectorAll('input, select').forEach(function (field) {
                                field.addEventListener('input', syncActivePane);
                                field.addEventListener('change', syncActivePane);
                            });

                            quotationForm.addEventListener('submit', function () {
                                syncActivePane();
                            });

                            syncActivePane();
                        });
                        </script>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <?= $this->include('admin/script'); ?>
</body>

</html>