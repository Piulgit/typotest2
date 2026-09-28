<?php

defined('TYPO3') || die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

call_user_func(static function () {
    ExtensionManagementUtility::addTCAcolumns('tt_content', [
        'custom_classname' => [
            'exclude' => 1,
            'label' => 'Custom CSS Class',
            'config' => [
                'type' => 'input',
                'size' => 100,
                'eval' => 'trim',
                'placeholder' => 'Custom Classnames with spaces',
            ],
        ],
    ]);

    $GLOBALS['TCA']['tt_content']['palettes']['custom_classnames'] = [
        'label' => 'Set Custom Classname',
        'showitem' => 'custom_classname',
    ];

    ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--palette--;;custom_classnames',
        '',
        'after:linkToTop'
    );

    ExtensionManagementUtility::addTCAcolumns('tt_content', [
        'innerspace_before' => [
            'exclude' => true,
            'label' => 'Inner space before',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Default', 'value' => ''],
                    ['label' => 'Pt-0', 'value' => 'pt-0'],
                    ['label' => 'Pt-1', 'value' => 'pt-1'],
                    ['label' => 'Pt-2', 'value' => 'pt-2'],
                    ['label' => 'Pt-3', 'value' => 'pt-3'],
                    ['label' => 'Pt-4', 'value' => 'pt-4'],
                    ['label' => 'Pt-5', 'value' => 'pt-5'],
                ],
                'default' => '',
            ],
        ],
        'innerspace_after' => [
            'exclude' => true,
            'label' => 'Inner space after',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Default', 'value' => ''],
                    ['label' => 'Pb-0', 'value' => 'pb-0'],
                    ['label' => 'Pb-1', 'value' => 'pb-1'],
                    ['label' => 'Pb-2', 'value' => 'pb-2'],
                    ['label' => 'Pb-3', 'value' => 'pb-3'],
                    ['label' => 'Pb-4', 'value' => 'pb-4'],
                    ['label' => 'Pb-5', 'value' => 'pb-5'],
                ],
                'default' => '',
            ],
        ],
    ]);

    ExtensionManagementUtility::addTCAcolumns('tt_content', [
        'bgcolor' => [
            'exclude' => true,
            'label' => 'Background Color',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'Default', 'value' => '', 'icon' => 'white'],
                    ['label' => 'Dark Blue', 'value' => 'bg-primary', 'icon' => 'dark-blue'],
                    ['label' => 'Light Blue', 'value' => 'bg-blue-light', 'icon' => 'light-blue'],
                    ['label' => 'Yellow', 'value' => 'bg-secondary', 'icon' => 'yellow'],
                    ['label' => 'Light Grey', 'value' => 'bg-gray-light', 'icon' => 'light-grey'],
                    ['label' => 'Dark', 'value' => 'bg-dark', 'icon' => 'dark'],
                    ['label' => 'Black', 'value' => 'bg-black', 'icon' => 'black'],
                    ['label' => 'White', 'value' => 'bg-white', 'icon' => 'white'],
                    ['label' => 'Light', 'value' => 'bg-light', 'icon' => 'light'],
                ],
                'fieldWizard' => [
                    'selectIcons' => [
                        'disabled' => false,
                    ],
                ],
            ],
        ],
    ]);

    $GLOBALS['TCA']['tt_content']['palettes']['colors'] = [
        'label' => 'Colors',
        'showitem' => 'bgcolor',
    ];
});
