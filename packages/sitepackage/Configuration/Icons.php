<?php

defined('TYPO3') || die();

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'white' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:sitepackage/Resources/Public/Icons/BgColor/white.svg',
    ],
    'dark-blue' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:sitepackage/Resources/Public/Icons/BgColor/dark-blue.svg',
    ],
    'light-blue' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:sitepackage/Resources/Public/Icons/BgColor/light-blue.svg',
    ],
    'yellow' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:sitepackage/Resources/Public/Icons/BgColor/yellow.svg',
    ],
    'light-grey' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:sitepackage/Resources/Public/Icons/BgColor/light-grey.svg',
    ],
    'dark' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:sitepackage/Resources/Public/Icons/BgColor/dark.svg',
    ],
    'black' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:sitepackage/Resources/Public/Icons/BgColor/black.svg',
    ],
    'light' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:sitepackage/Resources/Public/Icons/BgColor/light.svg',
    ],
];
