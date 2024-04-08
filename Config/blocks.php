<?php

$vAttributes = include(base_path() . '/Modules/Isite/Config/standardValuesForBlocksAttributes.php');

return [
  "userMenu" => [
    "title" => "Menu de Usuario",
    "systemName" => "iprofile::user-menu",
    "nameSpace" => "Modules\Iprofile\View\Components\UserMenu",
    "attributes" => [
        "general" => [
            "title" => "General",
            "fields" => [
                "layout" => [
                    "name" => "layout",
                    "value" => "user-menu-layout-1",
                    "type" => "select",
                    "props" => [
                        "label" => "Layout",
                        "options" => [
                            ["label" => "Layout 1", "value" => "user-menu-layout-1"],
                            ["label" => "Layout 2", "value" => "user-menu-layout-2"],
                        ]
                    ]
                ],
                "showLabel" => [
                    "name" => "showLabel",
                    "value" => "0",
                    "type" => "select",
                    "props" => [
                        "label" => "showButton",
                        "options" => $vAttributes["validation"]
                    ]
                ],
                "classUser" => [
                    "name" => "classUser",
                    "columns" => "col-12",
                    "type" => "input",
                    "props" => [
                        "label" => "Clases",
                    ]
                ],
                "styleUser" => [
                    "name" => "styleUser",
                    "type" => "input",
                    "columns" => "col-12",
                    "props" => [
                        "label" => "Estilos",
                        'type' => 'textarea',
                        'rows' => 5,
                    ],
                ],
            ]
        ],
    ]
  ],
];
