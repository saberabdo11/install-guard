<?php


namespace App\Classes;


class PurchaseValidator 
{
    private $codes =  [[
        'purchase_code' => '34e627f3-8b5c-47af-a5f6-4d1b2235ecb5',
        'username' => 'saberDeveloper'
    ], 
[
    'purchase_code' => 'CODE456',
    'username' => 'saber@123'
]
];


    public function validate($purchaseCode, $username)
    {

        $result = array_filter($this->codes, function ($entry)  use ($purchaseCode, $username) {
            return $entry['purchase_code'] === $purchaseCode && $entry['username'] === $username;
        });

        return !empty($result);


    }

}

