<?php namespace Config;

use CodeIgniter\Config\BaseConfig;

class Insurance extends BaseConfig
{
    public $insurers = [
        'SHRIRAM' => [
            'gst' => 18,
            'od_discount' => [
                'detariff' => 75,
                'special'  => 0.05
            ],
            'od_rates' => [
                'upto_1000'   => 3.284,   // for cars with CC ≤ 1000 (e.g., 998 cc)
                '1001-1200'   => 3.448,   // for cars with CC around 1197 cc
                '1201-1500'   => 3.500,   // example rate, adjust as per tariff
                '1501-2000'   => 3.750,   // example rate
                '2001-3000'   => 4.000    // example rate
            ],
            'cng_rates' => [
                'upto_1000'   => 0.164,   // for cars with CC ≤ 1000 (e.g., 998 cc) → ~0.164% of IDV
                '1001-1200'   => 0.172,   // for cars with CC around 1197 cc → ~0.172% of IDV
                '1201-1500'   => 0.180,   // example rate, adjust as per tariff
                '1501-2000'   => 0.190,   // example rate
                '2001-3000'   => 0.200    // example rate
            ],
            'tp_rates' => [
                'upto_1000' => [
                    'basic_liability'   => 6040,   // for cars with CC ≤ 1000
                    'per_passenger'     => 1162,   // passenger coverage per seat
                    'cng_liability'     => 60,     // if CNG/LPG kit installed
                    'll_driver'         => 50,     // if LL to paid driver opted
                    'pa_owner_driver'   => 315     // if PA cover opted
                ],
                '1001-1200' => [
                    'basic_liability'   => 7940,   // for cars with CC 1001–1500
                    'per_passenger'     => 978,    // passenger coverage per seat
                    'cng_liability'     => 60,
                    'll_driver'         => 50,
                    'pa_owner_driver'   => 315
                ],
                '1201-2000' => [
                    'basic_liability'   => 9000,   // example rate, adjust per tariff
                    'per_passenger'     => 1000,
                    'cng_liability'     => 60,
                    'll_driver'         => 50,
                    'pa_owner_driver'   => 315
                ]
            ],
            'zero_dep_rates' => [
                'upto_1000' => [   // cars with CC ≤ 1000
                    'age_0_1' => 0.0055,   // 0.55% of IDV
                    'age_1_2' => 0.0060,   // 0.60% of IDV
                    'age_2_3' => 0.0070,   // 0.70% of IDV
                    'age_3_4' => 0.0080,   // 0.80% of IDV
                    'age_4_5' => 0.0090    // 0.90% of IDV
                ],
                '1001-1500' => [   // cars with CC 1001–1500
                    'age_0_1' => 0.0085,   // 0.85% of IDV
                    'age_1_2' => 0.0105,   // 1.05% of IDV
                    'age_2_3' => 0.0115,   // 1.15% of IDV
                    'age_3_4' => 0.0150,   // 1.50% of IDV
                    'age_4_5' => 0.0200    // 2.00% of IDV
                ],
                '1501-2000' => [   // cars with CC 1501–2000
                    'age_0_1' => 0.0050,   // 0.50% of IDV
                    'age_1_2' => 0.0075,   // 0.75% of IDV
                    'age_2_3' => 0.0080,   // 0.80% of IDV
                    'age_3_4' => 0.0120,   // 1.20% of IDV
                    'age_4_5' => 0.0180    // 1.80% of IDV
                ]
            ],
            'liability' => [
                'paid_driver'        => 50,
                'pa_owner_driver'    => 315,
                'cng_liability'      => 60,
                'passenger_per_seat' => 978
            ],
            'addons' => [
                'consumable'     => 650,
                'towing'         => 100,
                'engine'         => 0,
                'return_invoice' => 0,
                'rsa'            => 800,
                'pa_owner'       => 315
            ]
            
        ],
        'SBI' => [
            'gst' => 18,
            'od_discount' => [
                'claim_yes' => 80,
                'claim_no'  => 85
            ],
            'od_rates' => [
                'upto_1000'   => 3.284,   // for cars with CC ≤ 1000 (e.g., 998 cc)
                '1001-1200'   => 3.448,   // for cars with CC around 1197 cc
                '1201-1500'   => 3.500,   // example rate, adjust as per tariff
                '1501-2000'   => 3.750,   // example rate
                '2001-3000'   => 4.000    // example rate
            ],
            'cng_rates' => [
                'upto_1000'   => 0.164,   // for cars with CC ≤ 1000 (e.g., 998 cc) → ~0.164% of IDV
                '1001-1200'   => 0.172,   // for cars with CC around 1197 cc → ~0.172% of IDV
                '1201-1500'   => 0.180,   // example rate, adjust as per tariff
                '1501-2000'   => 0.190,   // example rate
                '2001-3000'   => 0.200    // example rate
            ],
            'tp_rates' => [
                'upto_1000' => [
                    'basic_liability'   => 6040,   // for cars with CC ≤ 1000
                    'per_passenger'     => 1162,   // passenger coverage per seat
                    'cng_liability'     => 60,     // if CNG/LPG kit installed
                    'll_driver'         => 50,     // if LL to paid driver opted
                    'pa_owner_driver'   => 315     // if PA cover opted
                ],
                '1001-1200' => [
                    'basic_liability'   => 7940,   // for cars with CC 1001–1500
                    'per_passenger'     => 978,    // passenger coverage per seat
                    'cng_liability'     => 60,
                    'll_driver'         => 50,
                    'pa_owner_driver'   => 315
                ],
                '1201-2000' => [
                    'basic_liability'   => 9000,   // example rate, adjust per tariff
                    'per_passenger'     => 1000,
                    'cng_liability'     => 60,
                    'll_driver'         => 50,
                    'pa_owner_driver'   => 315
                ]
            ],
            'zero_dep_rates' => [
                    'age_0_1' => 0.0090,   // 0.9% of IDV
                    'age_1_2' => 0.0105,   // 1.05% of IDV
                    'age_2_3' => 0.0145,   // 1.45% of IDV
                    'age_3_4' => 0.0185,   // 1.85% of IDV
                    'age_4_5' => 0.0200    // 2.00% of IDV
            ],
            'liability' => [
                'paid_driver'        => 50,
                'pa_owner_driver'    => 315,
                'cng_liability'      => 60,
                'passenger_per_seat' => 978
            ],
            'addons' => [
                'consumable'     => 650,
                'towing'         => 100,
                'engine'         => 0,
                'return_invoice' => 0,
                'rsa'            => 800,
                'pa_owner'       => 315
            ]
        ],
        'RELIANCE' => [
                        'gst' => 18,
            'od_discount' => [
                'detariff' => 75,
                'special'  => 0.05
            ],
            'od_rates' => [
                'upto_1000'   => 3.284,   // for cars with CC ≤ 1000 (e.g., 998 cc)
                '1001-1200'   => 3.448,   // for cars with CC around 1197 cc
                '1201-1500'   => 3.500,   // example rate, adjust as per tariff
                '1501-2000'   => 3.750,   // example rate
                '2001-3000'   => 4.000    // example rate
            ],
            'cng_rates' => [
                'upto_1000'   => 0.164,   // for cars with CC ≤ 1000 (e.g., 998 cc) → ~0.164% of IDV
                '1001-1200'   => 0.172,   // for cars with CC around 1197 cc → ~0.172% of IDV
                '1201-1500'   => 0.180,   // example rate, adjust as per tariff
                '1501-2000'   => 0.190,   // example rate
                '2001-3000'   => 0.200    // example rate
            ],
            'tp_rates' => [
                'upto_1000' => [
                    'basic_liability'   => 6040,   // for cars with CC ≤ 1000
                    'per_passenger'     => 1162,   // passenger coverage per seat
                    'cng_liability'     => 60,     // if CNG/LPG kit installed
                    'll_driver'         => 50,     // if LL to paid driver opted
                    'pa_owner_driver'   => 315     // if PA cover opted
                ],
                '1001-1200' => [
                    'basic_liability'   => 7940,   // for cars with CC 1001–1500
                    'per_passenger'     => 978,    // passenger coverage per seat
                    'cng_liability'     => 60,
                    'll_driver'         => 50,
                    'pa_owner_driver'   => 315
                ],
                '1201-2000' => [
                    'basic_liability'   => 9000,   // example rate, adjust per tariff
                    'per_passenger'     => 1000,
                    'cng_liability'     => 60,
                    'll_driver'         => 50,
                    'pa_owner_driver'   => 315
                ]
            ],
            'zero_dep_rates' => [
                'upto_1000' => [   // cars with CC ≤ 1000
                    'age_0_1' => 0.0055,   // 0.55% of IDV
                    'age_1_2' => 0.0060,   // 0.60% of IDV
                    'age_2_3' => 0.0070,   // 0.70% of IDV
                    'age_3_4' => 0.0080,   // 0.80% of IDV
                    'age_4_5' => 0.0090    // 0.90% of IDV
                ],
                '1001-1500' => [   // cars with CC 1001–1500
                    'age_0_1' => 0.0085,   // 0.85% of IDV
                    'age_1_2' => 0.0105,   // 1.05% of IDV
                    'age_2_3' => 0.0115,   // 1.15% of IDV
                    'age_3_4' => 0.0150,   // 1.50% of IDV
                    'age_4_5' => 0.0200    // 2.00% of IDV
                ],
                '1501-2000' => [   // cars with CC 1501–2000
                    'age_0_1' => 0.0050,   // 0.50% of IDV
                    'age_1_2' => 0.0075,   // 0.75% of IDV
                    'age_2_3' => 0.0080,   // 0.80% of IDV
                    'age_3_4' => 0.0120,   // 1.20% of IDV
                    'age_4_5' => 0.0180    // 1.80% of IDV
                ]
            ],
            'liability' => [
                'paid_driver'        => 50,
                'pa_owner_driver'    => 315,
                'cng_liability'      => 60,
                'passenger_per_seat' => 978
            ],
            'addons' => [
                'consumable'     => 650,
                'towing'         => 100,
                'engine'         => 0,
                'return_invoice' => 0,
                'rsa'            => 800,
                'pa_owner'       => 315
            ]
        ]
    ];
}
