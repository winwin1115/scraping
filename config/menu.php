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
            'title' => '商品ページURL登録',
            'link' => '/urls',
            'active' => 'urls',
            'icon' => 'icon-fa icon-fa-shopping-cart',
        ],
        [
            'title' => '換率, 利益率設定',
            'link' => '/currencys',
            'active' => 'currencys',
            'icon' => 'icon-fa icon-fa-paypal',
        ],
        [
            'title' => 'CSV生成',
            'link' => '/csv',
            'active' => 'csv',
            'icon' => 'icon-fa icon-fa-cloud-download',
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
