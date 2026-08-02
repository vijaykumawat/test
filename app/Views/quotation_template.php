<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Motor Insurance Quotation</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #000; padding: 5px; }
        .section-title { background: #f0f0f0; font-weight: bold; text-align: center; }
        .right { text-align: right; }
        .header { width: 100%; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="header" style="text-align:center; margin-bottom:20px;">
    <?php if ($company == 'SHRIRAM'): ?>
        <img src="<?= base_url('assets/images/logos/shriramlogo.jpg') ?>" alt="Shriram Logo" style="height:60px; display:block; margin:0 auto;">
    <?php elseif ($company == 'SBI'): ?>
        <img src="<?= base_url('assets/images/logos/sbilogo.jpg') ?>" alt="SBI Logo" style="height:60px; display:block; margin:0 auto;">
    <?php elseif ($company == 'RELIANCE'): ?>
        <img src="<?= base_url('assets/images/logos/reliancelogo.jpg') ?>" alt="Reliance Logo" style="height:60px; display:block; margin:0 auto;">
    <?php endif; ?>

    <h2 style="margin:10px 0;">Motor Insurance Quotation</h2>
    <p style="margin:0;">Quotation No: <?= $quoteNo ?> | Date: <?= date('d-m-Y') ?></p>
</div>

<p>Dear <?= $ownerName ?>,</p>
<p>Thank you for giving us an opportunity to provide you a quote. The premium computation for your vehicle is given below:</p>

<table>
    <tr><td colspan="4" class="section-title">Premium Break-Up</td></tr>
    <tr><td>Quote No:</td><td><?= $quoteNo ?></td><td>Registration No:</td><td><?= $regNumber ?></td></tr>
    <tr><td>Model:</td><td><?= substr(preg_replace('/\s*Code-.*$/', '', $vehicleModel), 0, 34) ?></td><td>Email:</td><td><?= esc($email) ?></td></tr>
    <tr><td>Total IDV:</td><td><?= $idv ?></td><td>Mobile:</td><td><?= $mobile ?></td></tr>
    <tr><td>Policy Type:</td><td><?= $policyType ?></td><td>Manufacturing Year:</td><td><?= $manufacturingYear ?></td></tr>
    <tr><td>Proposed OD Policy Start:</td><td><?= esc($odStart) ?></td><td>Proposed OD Policy End:</td><td><?= esc($odEnd) ?></td></tr>
    <tr><td>Proposed TP Policy Start:</td><td><?= esc($tpStart) ?></td><td>Proposed TP Policy End:</td><td><?= esc($tpEnd) ?></td></tr>
</table>

<h3>A. Own Damage / B. Liability</h3>
<table>
    <tr>
        <td>Basic OD Premium</td><td class="right"><?= number_format($basicOD,2) ?></td>
        <td>Basic TP Premium</td><td class="right"><?= number_format($totalTP,2) ?></td>
    </tr>
    <tr>
        <td>InBuilt CNG/LPG/LNG Cover</td><td class="right"><?= number_format($cngKit ?? 0,2) ?></td>
        <td>InBuilt CNG/LPG/LNG Cover</td><td class="right"><?= number_format($cngLiability ?? 0,2) ?></td>
    </tr>
    <tr>
        <td><strong>Add on Covers</strong></td><td class="right"></td>
        <td>GR36A-PA FOR OWNER DRIVER</td><td class="right">0.00</td>
    </tr>
    
    <tr>
        <td>Consumable</td><td class="right">0.00</td>
        <td>Legal Liability Coverages For Paid Driver</td><td class="right"><?= number_format($legalLiability,2) ?></td>
    </tr>
    <tr>
        <td>Towing</td><td class="right">0.00</td>
        <td></td><td class="right"></td>
    </tr>
    <tr>
        <td>Nil Depreciation Cover</td><td class="right"><?= number_format($nilDep ?? 0,2) ?></td>
        <td></td><td class="right"></td>
    </tr>
    <tr>
        <td><strong>Discount</strong></td><td class="right"></td>
        <td></td><td class="right"></td>
    </tr>
    <tr>
        <td>De-Tariff Discount</td><td class="right">-<?= number_format($odDiscountAmt,2) ?></td>
        <td></td><td></td>
    </tr>
    <tr>
        <td>NCB Discount(<?= $ncb ?>%)</td><td class="right">-<?= number_format($ncbDiscount,2) ?></td>
        <td></td><td></td>
    </tr>
    <tr>
        <td><b>Total Own Damage Premium</b></td><td class="right"><b><?= number_format($ownDamagePremiumA,2) ?></b></td>
        <td><b>Total Liability Premium</b></td><td class="right"><b><?= number_format($libilityB,2) ?></b></td>
    </tr>
</table>

<table>
    <tr><td>Total Premium without GST</td><td class="right"><?= number_format($totalPremium,2) ?></td></tr>
    <tr><td>SGST (9%)</td><td class="right"><?= number_format($sgst,2) ?></td></tr>
    <tr><td>CGST (9%)</td><td class="right"><?= number_format($cgst,2) ?></td></tr>
    <tr><td><b>Final Premium</b></td><td class="right"><b><?= number_format($finalPremium,2) ?></b></td></tr>
</table>

<p><i>Disclaimer: Insurance is a subject matter of solicitation. This quotation is valid for 5 days. Premium rates are subject to change due to tax law changes, break-in inspection, or policy period changes. </i></p>

<div class="" style="display:flex; align-items:center; margin-bottom:20px;">
    <img src="<?= base_url('assets/images/logos/gblogo.jpeg') ?>" 
         alt="GB Logo" 
         style="height:40px; width:100px; margin-right:10px;"> 
    <div style="line-height:1.4;">
        <p>
            <strong>Employee :</strong> <?= $employeeName ?>
            <br>
            <strong>Contact No. :</strong> <?= $employeeMobile ?>
        </p>
        
    </div>
</div>


</body>
</html>
