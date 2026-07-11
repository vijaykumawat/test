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
        background-color: #F88379;
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
                        <div class="col-8">
                            <form action="<?php echo base_url();?>employee/save" method="post">
                                <!-- start Event Registration -->
                                <div
                                    class="card <?php if($actionTaken){ echo "blur";} else{ echo "";}?> <?php if($alreadySale==1){echo "alSale";} ?> shadow">
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
                                                <dd class="col-sm-4">-</dd>


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

                        <div class="col-4">
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

                            <div
                                class="card <?php if($actionTaken){ echo "blur";} else{ echo "";}?> <?php if($alreadySale==1){echo "alSale";} ?>">
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
            <div class="modal fade" id="quotationModal" tabindex="-1" aria-labelledby="quotationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <!-- xl for wide form -->
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="quotationModalLabel">Generate Quotation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <!-- Your form goes here -->
                            <form action="<?php echo base_url();?>Tcpdfexample/quote" method="post">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>IDV</label>
                                            <input type="text" name="idv" class="form-control" placeholder="Enter ..."
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>Cubic Capacity</label>
                                            <input type="text" name="cubicCapacity" class="form-control"
                                                placeholder="Enter ..." required>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>NCB</label>
                                            <input type="text" name="ncb" class="form-control" placeholder="Enter ..."
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>Net Premium</label>
                                            <input type="text" name="netPremium" class="form-control"
                                                placeholder="Enter ..." required>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label>Company</label>
                                            <select name="company" class="form-control">
                                                <option>SBI</option>
                                                <option>SHRIRAM</option>
                                                <option>RELIANCE</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <input type="checkbox" id="consumableCheckBox" name="consumableCheckBox">
                                            <label for="consumable">Consumable</label>
                                            <input type="text" name="consumable" class="form-control" placeholder="000">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <input type="checkbox" id="towingCheckBox" name="towingCheckBox">
                                            <label for="towing">Towing</label>
                                            <input type="text" name="towing" class="form-control" placeholder="000">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <input type="checkbox" id="returnToInvoiceCheckBox"
                                                name="returnToInvoiceCheckBox">
                                            <label for="returnToInvoice">Return To Invoice</label>
                                            <input type="text" name="returnToInvoice" class="form-control"
                                                placeholder="000">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <input type="checkbox" id="PAOwnerCheckBox" name="PAOwnerCheckBox">
                                            <label for="PAOwner">PA Owner 15 Lac</label>
                                            <input type="text" name="PAOwner" class="form-control" placeholder="000">
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-sm-3">
                                        <input type="hidden" id="recordId" name="recordId"
                                            value="<?php echo $recordId; ?>">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-download"></i> Download Quotation
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                data-bs-dismiss="modal">Close</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->include('admin/script'); ?>
</body>

</html>