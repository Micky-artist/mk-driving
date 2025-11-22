<?php

return [
    'uploads' => [
        'path' => 'uploads',
        'disk' => 'public',
        'allowed_mimes' => [
            'jpeg', 'jpg', 'png', 'gif', 'svg', 'csv', 'pdf', 'doc', 'docx', 'xls', 'xlsx'
        ],
        'max_size' => 5120, // 5MB in KB
        'image_validation' => [
            'mimes' => 'jpeg,png,jpg,gif,svg',
            'max' => 5120, // 5MB
        ],
        'document_validation' => [
            'mimes' => 'pdf,doc,docx,xls,xlsx,csv',
            'max' => 10240, // 10MB
        ],
    ],
];
