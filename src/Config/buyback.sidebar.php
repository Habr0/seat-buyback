<?php

return [
    'buyback' => [
        'name' => 'Buyback',
        'icon' => 'fas fa-shopping-cart',
        'route_segment' => 'buyback',
        'permission' => 'buyback.appraisals',
        'entries' => [
            [
                'name' => 'Appraisal',
                'icon' => 'fas fa-search-dollar',
                'route' => 'buyback.appraisal_index',
                'permission' => 'buyback.appraisals'
            ],
            [
                'name' => 'Contracts',
                'icon' => 'fas fa-file-signature',
                'route' => 'buyback.contracts_index',
                'permission' => 'buyback.contracts'
            ],
            [
                'name' => 'Admin',
                'icon' => 'fas fa-tools',
                'route' => 'buyback.admin_index',
                'permission' => 'buyback.admin'
            ],
        ],
    ],
];
