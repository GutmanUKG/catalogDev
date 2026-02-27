<?
$aMenuLinks = [
    [
        "Организации",
        "/b2bcabinet/personal/companies/index.php",
        [
            "/b2bcabinet/personal/companies/add.php",
            "/b2bcabinet/personal/companies/profile_detail.php",
            "/b2bcabinet/personal/companies/profile_list.php"
        ],
        [
            'ICON_CLASS' => 'ph-tree-structure'
        ],
        ""
    ],
    [
        "Сотрудники",
        "/b2bcabinet/personal/staff/index.php",
        [],
        [
            'ICON_CLASS' => 'ph-users-four'
        ],
        ""
    ],
    [
        "Личный счет",
        "/b2bcabinet/personal/account/index.php",
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
        "/b2bcabinet/personal/buyer/index.php",
        [
            "/b2bcabinet/personal/buyer/add.php",
            "/b2bcabinet/personal/buyer/profile_detail.php",
            "/b2bcabinet/personal/buyer/profile_list.php"
        ],
        [
            'ICON_CLASS' => 'ph-tree-structure'
        ],
        ""
    ];
}

?>