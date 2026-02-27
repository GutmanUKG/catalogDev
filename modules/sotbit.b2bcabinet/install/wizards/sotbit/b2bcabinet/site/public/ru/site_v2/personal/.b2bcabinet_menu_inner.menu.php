<?
$aMenuLinks = [
    [
        "Организации",
        "/personal/companies/index.php",
        [
            "/personal/companies/add.php",
            "/personal/companies/profile_detail.php",
            "/personal/companies/profile_list.php"
        ],
        [
            'ICON_CLASS' => 'ph-tree-structure'
        ],
        ""
    ],
    [
        "Сотрудники",
        "/personal/staff/index.php",
        [],
        [
            'ICON_CLASS' => 'ph-users-four'
        ],
        ""
    ],
    [
        "Личный счет",
        "/personal/account/index.php",
        [],
        [
            'ICON_CLASS' => 'ph-credit-card'
        ],
        ""
    ],
];

if (defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES != "Y"){
    unset($aMenuLinks[1]);
    $aMenuLinks[0] = [
        "Организации",
        "/personal/buyer/index.php",
        [
            "/personal/buyer/add.php",
            "/personal/buyer/profile_detail.php",
            "/personal/buyer/profile_list.php"
        ],
        [
            'ICON_CLASS' => 'ph-tree-structure'
        ],
        ""
    ];
}

?>