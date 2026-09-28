<?php

namespace Takevalue\Sitepackage\ViewHelpers;

use TYPO3\CMS\Core\Imaging\ImageManipulation\CropVariantCollection;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentValueException;
use TYPO3\CMS\Extbase\Service\ImageService;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Exception;

/**
 * Class ImageViewHelper
 *
 * Example:
 * <uvsh:responsiveImage image="{image}"
 *            class="w-full h-full object-cover"
 *            webp="true|false"
 *            title="{image.title}"
 *            alt="{image.alternative}"
 *            responsiveSizes="{xs: 551, sm: 516, md: 696, lg: 624, xl: 744, xxl: 864}"/>
 */
class ResponsiveImageViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * Bootstrap Breakpoints
     *
     * colWidth: Is the width of a single column at this breakpoint (col-1)
     *
     * If you make changes, the order has to be biggest to smallest to work correctly
     */
    protected const BREAKPOINTS = [
        'xxl' => ['breakpoint' => 1400],
        'xl'  => ['breakpoint' => 1280],
        'lg'  => ['breakpoint' => 1024],
        'md'  => ['breakpoint' => 768],
        'sm'  => ['breakpoint' => 640],
        'xs' => ['breakpoint' => 0],
    ];

    /**
     * @var string
     */
    protected $tagName = 'picture';

    public function __construct(protected ImageService $imageService)
    {
        parent::__construct();
    }

    /**
     * Initialize arguments.
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('alt', 'string', 'Specifies an alternate text for an image', false, '');
        $this->registerArgument(
            'treatIdAsReference',
            'bool',
            'given src argument is a sys_file_reference record',
            false,
            false
        );
        $this->registerArgument('image', 'mixed', 'a FAL object or image path', true);
        $this->registerArgument(
            'aspectRatio',
            'string',
            'e.g. 16:9, when you set an aspect ratio then this setting has priority over all other cropping settings'
        );
        $this->registerArgument(
            'crop',
            'string|bool',
            'overrule cropping of image (setting to FALSE disables the cropping set in FileReference)'
        );
        $this->registerArgument(
            'cropVariant',
            'string',
            'select a cropping variant, in case multiple croppings have been specified or stored in FileReference',
            false,
            'default'
        );
        $this->registerArgument('fileExtension', 'string', 'Custom file extension to use');
        $this->registerArgument('absolute', 'bool', 'Force absolute URL', false, false);

        $this->registerArgument('responsiveSizes', 'array', 'How wide should a image be at which breakpoint', true);
        $this->registerArgument('webp', 'bool', 'Provide .webp versions of the images', false, true);
    }

    /**
     * Resizes a given image (if required) and renders the respective img tag
     *
     * @see https://docs.typo3.org/typo3cms/TyposcriptReference/ContentObjects/Image/
     *
     * @return string Rendered tag
     * @throws InvalidArgumentValueException
     * @throws Exception
     */
    public function render(): string
    {
        $src = '';
        $file = null;
        if ($this->arguments['image'] instanceof FileInterface || $this->arguments['image'] instanceof FileReference) {
            $file = $this->arguments['image'];
        } elseif (is_string($this->arguments['image']) && strlen($this->arguments['image']) > 3) {
            $src = $this->arguments['image'];
        } else {
            throw new Exception(
                'You must either specify a string src, a FileReference or a File object. '
                . $this->arguments['image'] . ' given',
                1708036734
            );
        }

        // Check if all 5 breakpoints are set
        $responsiveSizesSettings = $this->arguments['responsiveSizes'];
        if (
            !(array_key_exists('xs', $responsiveSizesSettings) && $responsiveSizesSettings['xs']) ||
            !(array_key_exists('sm', $responsiveSizesSettings) && $responsiveSizesSettings['sm']) ||
            !(array_key_exists('md', $responsiveSizesSettings) && $responsiveSizesSettings['md']) ||
            !(array_key_exists('lg', $responsiveSizesSettings) && $responsiveSizesSettings['lg']) ||
            !(array_key_exists('xl', $responsiveSizesSettings) && $responsiveSizesSettings['xl']) ||
            !(array_key_exists('xxl', $responsiveSizesSettings) && $responsiveSizesSettings['xxl'])
        ) {
            throw new InvalidArgumentValueException(
                'You must set an array in ' .
                'responsiveSizesSettings="{xs: 545, sm: 510, md: 345, lg: 290, xl: 350, xxl: 410}" ' .
                'that contains key and value for each breakpoint',
                1637335641
            );
        }

        try {
            $image = $this->imageService->getImage(
                $src,
                $file,
                (bool)$this->arguments['treatIdAsReference']
            );
        } catch (\UnexpectedValueException $unexpectedValueException) {
            // thrown if a file has been replaced with a folder
            throw new Exception($unexpectedValueException->getMessage(), 1509741912, $unexpectedValueException);
        }

        if ($this->arguments['alt']) {
            $alt = $this->arguments['alt'];
        } else {
            $alt = strlen((string)$image->getProperty('alternative')) !== 0 ? $image->getProperty('alternative') : '';
        }

        if ($this->arguments['title']) {
            $title = $this->arguments['title'];
        } else {
            $title = (string)($image->hasProperty('title') ? $image->getProperty('title') : '');
        }

        $pictureTagContent = $this->getPictureContent($image, $alt, $title);

        $this->tag->forceClosingTag(true);
        $this->tag->setContent($pictureTagContent);

        return $this->tag->render();
    }

    /**
     * Render the HTML needed inside the <picture> Tag
     *
     * Multiple <source>
     * Single <img>
     *
     * @param FileInterface $image
     * @param string $alt
     * @param string $title
     * @return string
     * @throws InvalidArgumentValueException
     */
    protected function getPictureContent(FileInterface $image, string $alt, string $title = ''): string
    {
        $responsiveSizesSettings = $this->arguments['responsiveSizes'];
        $breakpoints             = self::BREAKPOINTS;

        $maxWidthTotal = 0;
        foreach (array_keys($breakpoints) as $breakpoint) {
            $size = $responsiveSizesSettings[$breakpoint];

            $maxWidth = $size;

            $breakpoints[$breakpoint]['image']     = $this->processImage($image, $maxWidth);
            $breakpoints[$breakpoint]['imageWebp'] = $this->processImage($image, $maxWidth, 'webp');
            $maxWidthTotal = max($maxWidth, $maxWidthTotal);
        }

        // <source> Tags
        $sourceTags = '';
        foreach ($breakpoints as $breakpoint) {
            $sourceTags .= $this->getSoucreTags($breakpoint);
        }

        // Fallback <img> Tag
        $fallbackImage    = $this->processImage($image, $maxWidthTotal);
        $fallbackImageTag = $this->getFallbackImg(
            $fallbackImage['src'],
            $fallbackImage['width'],
            $fallbackImage['height'],
            $alt,
            $this->arguments['id'],
            $this->arguments['class'],
            $title
        );

        return $sourceTags . $fallbackImageTag;
    }

    /**
     * @param FileInterface $image
     * @param int $width
     * @param ?string $fileExtension
     * @return array<string, mixed>
     * @throws InvalidArgumentValueException
     */
    protected function processImage(FileInterface $image, int $width, ?string $fileExtension = null): array
    {
        $crop = $image->getProperty('crop');
        $array = json_decode((string)$crop);

        // If an aspectRatio is given and no 16:9 cropping is set, use that aspectRatio to calculate the height
        if ($this->hasArgument('aspectRatio') && $array?->default->selectedRatio != '16:9') {
            $height = $this->getHeightFromWidthByAspectRatio($width);
            $processingInstructions = [
                'width'  => $width . 'c',
                'height' => $height . 'c',
            ];
        } else {
            // else use the value from the crop argument of the viewhelper
            $cropString = $this->arguments['crop'];
            if ($cropString === null && $image->hasProperty('crop') && $image->getProperty('crop')) {
                // if no crop argument is set, try to get cropping settings from file record
                $cropString = $image->getProperty('crop');
            }

            $cropVariantCollection  = CropVariantCollection::create((string)$cropString);
            $cropVariant            = $this->arguments['cropVariant'] ?: 'default';
            $cropArea               = $cropVariantCollection->getCropArea($cropVariant);
            $processingInstructions = [
                'maxWidth' => $width,
                'crop'     => $cropArea->isEmpty() ? null : $cropArea->makeAbsoluteBasedOnFile($image),
            ];
        }

        if ($fileExtension) {
            $processingInstructions['fileExtension'] = $fileExtension;
        }

        $processedImage = $this->imageService->applyProcessingInstructions($image, $processingInstructions);
        $imageUri       = $this->imageService->getImageUri($processedImage, $this->arguments['absolute']);
        $width          = $processedImage->getProperty('width');
        $height         = $processedImage->getProperty('height');

        return ['src' => $imageUri, 'width' => $width, 'height' => $height];
    }

    /**
     * Get the <source> Tags for a single breakpoint.
     *
     * @param array<string,int,array<int,string>> $breakpoint
     * @return ?string
     */
    protected function getSoucreTags(array $breakpoint): ?string
    {
        if (!$breakpoint['image'] || !$breakpoint['imageWebp']) {
            return '';
        }

        $content = '';
        if ($this->arguments['webp']) {
            $template = '<source media="(min-width: %spx)" sizes="%spx" srcset="%s" type="image/webp">';
            $content  .= sprintf(
                $template,
                $breakpoint['breakpoint'],
                $breakpoint['imageWebp']['width'],
                $breakpoint['imageWebp']['src']
            );
        }

        $template = '<source media="(min-width: %spx)" sizes="%spx" srcset="%s">';

        return $content . sprintf(
            $template,
            $breakpoint['breakpoint'],
            $breakpoint['image']['width'],
            $breakpoint['image']['src']
        );
    }

    /**
     * Render the <img> Tag for the fallback Image
     *
     * @param string $src
     * @param int $width
     * @param int $height
     * @param string $alt
     * @param ?string $id
     * @param ?string $class
     * @param ?string $title
     * @return string
     */
    protected function getFallbackImg(
        string $src,
        int $width,
        int $height,
        string $alt,
        ?string $id = '',
        ?string $class = '',
        ?string $title = ''
    ): string {
        $id       = $id ? ' id="' . $id . '"' : '';
        $class    = $class ? ' class="' . $class . '"' : '';
        $title    = $title ? ' title="' . $title . '"' : '';
        $template = '<img src="%s" alt="%s" width="%s" height="%s"%s%s%s>';
        return sprintf(
            $template,
            $src,
            $alt,
            $width,
            $height,
            $id,
            $class,
            $title
        );
    }

    /**
     * @throws InvalidArgumentValueException
     */
    private function getHeightFromWidthByAspectRatio(int $width): int
    {
        [$ratioWidth, $ratioHeight] = explode(':', (string)$this->arguments['aspectRatio']);

        if (! (is_numeric($ratioHeight) && $ratioHeight > 0 && is_numeric($ratioWidth) && $ratioWidth > 0)) {
            throw new InvalidArgumentValueException(
                'Invalide Aspect Ratio in the <nito:responsiveImage> viewhelper. Working example: 16:9',
                1658608239
            );
        }

        return (int)round($width / $ratioWidth * $ratioHeight);
    }
}
