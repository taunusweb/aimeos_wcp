<?php

return [
    'common' => [
        'media' => [
            'standard' => [
                'options' => [
                    'image' => [
                        'name' => 'Imagick',
                        'watermark' => dirname(__DIR__, 3) . '/Public/wasserzeichen.png',
                    ],
                ],
                'previews' => [[
                    'maxwidth' => 240,
                    'maxheight' => 180,
                    'force-size' => false,
                ], [
                    'maxwidth' => 650,
                    'maxheight' => 488,
                    'force-size' => false,
                ], [
                    'maxwidth' => 1300,
                    'maxheight' => 976,
                    'force-size' => false,
                ]],
            ],
        ],
    ],
];
