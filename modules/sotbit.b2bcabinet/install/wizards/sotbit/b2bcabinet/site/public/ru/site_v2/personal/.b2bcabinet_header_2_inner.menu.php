<?
$aMenuLinks = [
    'ORGANIZATION' => [
        "Организации",
        "/personal/companies/index.php",
        [
            "/personal/companies/add.php",
            "/personal/companies/profile_detail.php",
            "/personal/companies/profile_list.php"
        ],
        [
            'ICON_CLASS' => 'icon-collaboration'
        ],
        ""
    ],
    'STAFF' => [
        "Сотрудники",
        "/personal/staff/index.php",
        [],
        [
            'ICON_CLASS' => 'icon-person'
        ],
        ""
    ],
    'SCORE' => [
        "Личный счет",
        "/personal/account/index.php",
        [],
        [
            'ICON_CLASS' => 'icon-credit-card'
        ],
        ""
    ],
];

if (defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES != "Y"){
    unset($aMenuLinks['STAFF']);
    $aMenuLinks['ORGANIZATION'] = [
        "Организации",
        "/personal/buyer/index.php",
        [
            "/personal/buyer/add.php",
            "/personal/buyer/profile_detail.php",
            "/personal/buyer/profile_list.php"
        ],
        [
            'ICON_CLASS' => 'icon-collaboration'
        ],
        ""
    ];
}

?>