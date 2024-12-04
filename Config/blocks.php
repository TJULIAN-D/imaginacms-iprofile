<?php

$vAttributes = include(base_path() . '/Modules/Isite/Config/standardValuesForBlocksAttributes.php');

return [
  "userMenu" => [
    "title" => "Menu de Usuario",
    "systemName" => "iprofile::user-menu",
    "nameSpace" => "Modules\Iprofile\View\Components\UserMenu",
    "contentFields" => [
      "label" => [
        "name" => "label",
        "type" => "input",
        "colClass" => 'col-12',
        "isTranslatable" => true,
        "props" => [
          "label" => "Texto Label Para Boton (Login)"
        ]
      ],
    ],
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
                "typeContent" => [
                  "name" => "typeContent",
                  "value" => "0",
                  "type" => "select",
                  "props" => [
                    "label" => "Tipo de Contenido a Mostrar",
                    "options" => [
                      ["label" => "Label con icono", "value" => "0"],
                      ["label" => "Icon", "value" => "1"],
                      ["label" => "Label", "value" => "2"],
                    ]
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