<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Navigation Menu
    |--------------------------------------------------------------------------
    |
    | This array is for Navigation menus of the backend.  Just add/edit or
    | remove the elements from this array which will automatically change the
    | navigation.
    |
    */

    // SIDEBAR LAYOUT - MENU

    'sidebar' => [
        [
            'title' => 'データ登録',
            'link' => '#',
            'active' => 'data*',
            'icon' => 'icon-fa icon-fa-shopping-cart',
            'children' => [
                [
                    'title' => '商品URL登録(ヤフオク)',
                    'link' => '/data/urls',
                    'active' => 'data/urls',
                ],
                [
                    'title' => 'ASINコード登録(Amazon)',
                    'link' => '/data/asins',
                    'active' => 'data/asins',
                ],
            ]
        ],
        [
            'title' => '換率, 利益率設定',
            'link' => '/currencys',
            'active' => 'currencys',
            'icon' => 'icon-fa icon-fa-paypal',
        ],
        [
            'title' => 'CSV生成',
            'link' => '#',
            'active' => 'csv*',
            'icon' => 'icon-fa icon-fa-cloud-download',
            'children' => [
                [
                    'title' => 'ヤフオク',
                    'link' => '/csv/yahoo-auction',
                    'active' => 'csv/yahoo-auction'
                ],
                [
                    'title' => 'Amazon',
                    'link' => '/csv/amazon',
                    'active' => 'csv/amazon'
                ],
            ]
        ],
        [
            'title' => '自動取り下げ',
            'link' => '/auto',
            'active' => 'auto',
            'icon' => 'icon-fa icon-fa-briefcase',
        ]
    ],

    // HORIZONTAL MENU LAYOUT -  MENU

    'horizontal' => [
        
    ]
];
