<?php declare(strict_types=1);

namespace ExtraBlocks;

return [
    'block_layouts' => [
        'invokables' => [
            'navigationButtons' => Site\BlockLayout\NavigationButtons::class,
            'ColorOrImageHeader' => Site\BlockLayout\ColorOrImageHeader::class,
            'ImageButtons' => Site\BlockLayout\ImageButtons::class,
            'itemSetGrid'        => Site\BlockLayout\ItemSetGrid::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
                'text_domain' => null, // Use null to use the default domain
            ],
        ],
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                'ExtraBlocks' => __DIR__ . '/../asset'
            ]
        ]
    ]
];