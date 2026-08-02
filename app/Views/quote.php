<?php

//============================
// CONFIGURATION
//============================

$config = [

    'gst' => 18,

    'od_rates' => [
        '0-1' => 3.20,
        '1-2' => 3.00,
        '2-3' => 2.80,
        '3-4' => 2.60,
        '4-5' => 2.40,
        '5-10'=>2.20
    ],

    'tp_rates' => [
        4 => 7200,
        5 => 7940,
        6 => 8600,
        7 => 9400,
        8 => 10200
    ],

    'addons' => [
        'zero_dep' => 2500,
        'rsa' => 500,
        'engine' => 1200
    ],

    'ncb' => [
        0=>0,
        20=>20,
        25=>25,
        35=>35,
        45=>45,
        50=>50
    ]
];

$result = null;

if(isset($_POST['calculate'])){

    $idv      = (float)$_POST['idv'];
    $age      = (int)$_POST['age'];
    $seat     = (int)$_POST['seat'];
    $ncb      = (int)$_POST['ncb'];

    $zeroDep  = isset($_POST['zero_dep']);
    $rsa      = isset($_POST['rsa']);
    $engine   = isset($_POST['engine']);

    // Find OD Rate
    foreach($config['od_rates'] as $range=>$rate){

        list($min,$max)=explode("-",$range);

        if($age >= $min && $age < $max){

            $odRate=$rate;
            break;
        }

    }

    $basicOD = ($idv*$odRate)/100;

    $ncbDiscount = ($basicOD*$config['ncb'][$ncb])/100;

    $netOD = $basicOD-$ncbDiscount;

    $tp = $config['tp_rates'][$seat];

    $addon=0;

    if($zeroDep)
        $addon += $config['addons']['zero_dep'];

    if($rsa)
        $addon += $config['addons']['rsa'];

    if($engine)
        $addon += $config['addons']['engine'];

    $subtotal = $netOD+$tp+$addon;

    $gst = ($subtotal*$config['gst'])/100;

    $final = $subtotal+$gst;

    $result=[
        'Basic OD'=>$basicOD,
        'NCB Discount'=>$ncbDiscount,
        'Net OD'=>$netOD,
        'TP Premium'=>$tp,
        'Addons'=>$addon,
        'Subtotal'=>$subtotal,
        'GST'=>$gst,
        'Final'=>$final
    ];

}

?>

<!doctype html>

<html>

<head>

<title>Insurance Quotation</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row">

<div class="col-md-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>PCV Taxi Insurance Calculator</h4>

</div>

<div class="card-body">

<form method="post">

<label>IDV</label>

<input type="number" name="idv" class="form-control mb-3" required>

<label>Vehicle Age</label>

<input type="number" name="age" class="form-control mb-3" required>

<label>Seating Capacity</label>

<select name="seat" class="form-control mb-3">

<option>4</option>

<option selected>5</option>

<option>6</option>

<option>7</option>

<option>8</option>

</select>

<label>NCB</label>

<select name="ncb" class="form-control mb-3">

<option>0</option>

<option>20</option>

<option>25</option>

<option>35</option>

<option>45</option>

<option>50</option>

</select>

<div class="form-check">

<input type="checkbox" name="zero_dep" class="form-check-input">

<label class="form-check-label">Zero Dep</label>

</div>

<div class="form-check">

<input type="checkbox" name="rsa" class="form-check-input">

<label class="form-check-label">RSA</label>

</div>

<div class="form-check mb-3">

<input type="checkbox" name="engine" class="form-check-input">

<label class="form-check-label">Engine Protect</label>

</div>

<button class="btn btn-success w-100" name="calculate">

Calculate Premium

</button>

</form>

</div>

</div>

</div>

<div class="col-md-6">

<?php if($result){ ?>

<div class="card shadow">

<div class="card-header bg-success text-white">

<h4>Quotation</h4>

</div>

<div class="card-body">

<table class="table table-bordered">

<?php

foreach($result as $k=>$v){

echo "<tr>";

echo "<td>$k</td>";

echo "<td>₹ ".number_format($v,2)."</td>";

echo "</tr>";

}

?>

</table>

</div>

</div>

<?php } ?>

</div>

</div>

</div>

</body>

</html>