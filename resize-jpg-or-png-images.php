<?php

declare(strict_types=1);

const EXTENSIONS = [
    'jpg',
    'jpeg',
    'png'
];

$inputPath = './images/mypng.png'; // OR './images/myjpg.jpg' if you want ;-)

$outputPath = getOutputPath(
    inputPath: $inputPath
);

$outputImageWidth = 600;

throwErrorIfImageCannotBeResized(
    outputImageWidth: $outputImageWidth,
    inputPath: $inputPath
);

if (
    str_ends_with(
        haystack: $inputPath,
        needle: 'jpg'
    ) === true
    ||
    str_ends_with(
        haystack: $inputPath,
        needle: 'jpeg'
    ) === true
) {
    resizeJPGImage(
        width: $outputImageWidth,
        path: $inputPath,
        outputPath: $outputPath
    );
} else {
    resizePNGImage(
        width: $outputImageWidth,
        path: $inputPath,
        outputPath: $outputPath
    );
}

/**
 * @author Kev
 * @param string $inputPath The path of the image to resize.
 * @return string The path of the resized image.
 * @throws LogicException If the image extension is not supported.
 */
function getOutputPath(string $inputPath): string
{
    [
        'dirname' => $directory,
        'extension' => $extension,
        'filename' => $filename
    ] = pathinfo(
        path: $inputPath,
    );

    if (
        in_array(
            needle: $extension,
            haystack: EXTENSIONS,
            strict: true
        ) === false
    ) {
        throw new LogicException(
            message: 'Only jpg/jpeg/png files are supported.'
        );
    }

    return "{$directory}/{$filename}-resized.{$extension}";
}

/**
 * @author Kev
 * @param int $outputImageWidth The desired width for the resized image.
 * @param string $inputPath The path of the image to resize.
 * @return void
 * @throws LogicException If output image width is greather than input image.
 */
function throwErrorIfImageCannotBeResized(
    int    $outputImageWidth,
    string $inputPath
): void
{
    $inputImageWidth = getImageWidth($inputPath);

    if ($inputImageWidth < $outputImageWidth) {
        throw new LogicException(
            message: 'Output image width is greather than input image width... this may cause image distortion.'
        );
    }
}

/**
 * @author Kev
 * @param string $inputPath The path of the image to resize.
 * @return int The width of the image to resize.
 * @throws LogicException If getimagesize() fails.
 */
function getImageWidth(string $inputPath): int
{
    $dimensions = getimagesize($inputPath);

    if ($dimensions === false) {
        throw new LogicException(
            message: 'getimagesize() failed.'
        );
    }

    return $dimensions[0];
}

/**
 * @author Kev
 * @param int $width The desired width for the resized image
 * @param string $path The path of the image to resize.
 * @param string $outputPath The path of the resized image.
 * @return void
 * @throws LogicException If imagecreatefromjpeg() or imagescale() or imagejpeg() fails.
 */
function resizeJPGImage(
    int    $width,
    string $path,
    string $outputPath
): void
{
    $imageToResizeGDObjectOrFalse = imagecreatefromjpeg(
        filename: $path
    );
    if ($imageToResizeGDObjectOrFalse === false) {
        throw new LogicException(
            message: 'imagecreatefromjpeg() failed.'
        );
    }

    $imageResizedGDObjectOrFalse = imagescale(
        image: $imageToResizeGDObjectOrFalse,
        width: $width
    );

    if ($imageResizedGDObjectOrFalse === false) {
        throw new LogicException(
            message: 'imagescale() failed.'
        );
    }

    $isImageResized = imagejpeg(
        image: $imageResizedGDObjectOrFalse,
        file: $outputPath,
        quality: 90
    );

    if ($isImageResized === false) {
        throw new LogicException(
            message: 'imagejpeg() failed.'
        );
    }
}

/**
 * @author Kev
 * @param int $width The desired width for the resized image
 * @param string $path The path of the image to resize.
 * @param string $outputPath The path of the resized image.
 * @return void
 * @throws LogicException If imagecreatefrompng() or imagescale() or imagealphablending() or imagesavealpha() or imagepng() fails.
 */
function resizePNGImage(
    int    $width,
    string $path,
    string $outputPath
): void
{
    $imageGDObject = imagecreatefrompng(
        filename: $path
    );

    $imageResizedGDObjectOrFalse = imagescale(
        image: $imageGDObject,
        width: $width
    );

    if ($imageResizedGDObjectOrFalse === false) {
        throw new LogicException(
            message: 'imagescale() failed.'
        );
    }

    $isSuccess = imagealphablending(
        image: $imageResizedGDObjectOrFalse,
        enable: false
    );

    if ($isSuccess === false) {
        throw new LogicException(
            message: 'imagealphablending() failed.'
        );
    }

    $isSuccess = imagesavealpha(
        image: $imageResizedGDObjectOrFalse,
        enable: true
    );

    if ($isSuccess === false) {
        throw new LogicException(
            message: 'imagesavealpha() failed.'
        );
    }

    $isSuccess = imagepng(
        image: $imageResizedGDObjectOrFalse,
        file: $outputPath,
    );

    if ($isSuccess === false) {
        throw new LogicException(
            message: 'imagepng() failed.'
        );
    }
}
