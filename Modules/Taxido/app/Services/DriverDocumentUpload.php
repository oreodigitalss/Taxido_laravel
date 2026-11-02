<?php

namespace Modules\Taxido\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

/** Only used by driver document endpoints; other media uploads are unchanged. */
class DriverDocumentUpload
{
    public const MAX_BYTES = 300 * 1024;

    public static function prepare(Request $request): Request
    {
        $files = $request->files->all();
        $documents = $files['documents'] ?? [];
        if (!is_array($documents)) {
            return $request;
        }

        $changed = false;
        foreach ($documents as $index => $document) {
            $file = is_array($document) ? ($document['file'] ?? null) : null;
            if (!$file instanceof SymfonyUploadedFile || !$file->isValid()) {
                continue;
            }

            $source = $request->input("documents.$index.source");
            // Older Android builds preserve the camera's generated filename in
            // multipart uploads. An explicit gallery/file source always wins.
            $camera = $source === 'camera' || ($source === null && preg_match(
                '/^rn_image_picker_lib_temp_[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.jpe?g$/i',
                $file->getClientOriginalName()
            ));

            if (!$camera || $file->getSize() <= self::MAX_BYTES) {
                continue;
            }
            // A filename never makes a PDF, fake image, or another format an image.
            $info = @getimagesize($file->getPathname());
            if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
                continue;
            }

            $documents[$index]['file'] = self::compress($file, $info);
            $changed = true;
        }

        if (!$changed) {
            return $request;
        }
        // Use a fresh request so Laravel's cached allFiles() cannot return the
        // original oversized upload. Preserve fields, routes, auth, and session.
        $prepared = Request::createFrom($request);
        $files['documents'] = $documents;
        $prepared->files->replace($files);
        return $prepared;
    }

    private static function compress(SymfonyUploadedFile $file, array $info): UploadedFile
    {
        if (!extension_loaded('gd')) {
            throw new Exception('Camera photo processing is unavailable. Please upload a file of 300 KB or smaller.', 422);
        }
        if ($info[0] * $info[1] > 24000000 || $file->getSize() > 32 * 1024 * 1024) {
            throw new Exception('Please retake this camera photo at a lower resolution.', 422);
        }

        $memoryLimit = ini_get('memory_limit');
        if ($memoryLimit !== false && $memoryLimit !== '-1') {
            $limit = (int) $memoryLimit;
            $unit = strtolower(substr($memoryLimit, -1));
            $limit *= ['k' => 1024, 'm' => 1048576, 'g' => 1073741824][$unit] ?? 1;
            if ($limit - memory_get_usage(true) < $info[0] * $info[1] * 5 + 16 * 1024 * 1024) {
                throw new Exception('Please retake this camera photo at a lower resolution.', 422);
            }
        }

        $image = $info[2] === IMAGETYPE_JPEG
            ? @imagecreatefromjpeg($file->getPathname())
            : @imagecreatefrompng($file->getPathname());
        if (!$image) {
            throw new Exception('Unable to process this camera photo. Please retake it.', 422);
        }

        $path = null;
        try {
            if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
                $exif = @exif_read_data($file->getPathname());
                $orientation = is_array($exif) ? (int) ($exif['Orientation'] ?? 1) : 1;
                $rotation = [3 => 180, 5 => 270, 6 => 270, 7 => 90, 8 => 90][$orientation] ?? 0;
                if ($rotation) {
                    $rotated = imagerotate($image, $rotation, 0);
                    if (!$rotated) {
                        throw new Exception('Unable to orient this camera photo. Please retake it.', 422);
                    }
                    imagedestroy($image);
                    $image = $rotated;
                }
                if (in_array($orientation, [2, 5, 7], true)) {
                    imageflip($image, IMG_FLIP_HORIZONTAL);
                } elseif ($orientation === 4) {
                    imageflip($image, IMG_FLIP_VERTICAL);
                }
            }

            $path = tempnam(sys_get_temp_dir(), 'rydap_camera_');
            if ($path === false) {
                $path = null;
                throw new Exception('Unable to prepare this camera photo. Please try again.', 422);
            }
            // Spatie may move the file during persistence. Remove only our own
            // temporary output if it still exists when this request ends.
            register_shutdown_function(static function () use ($path) {
                if (is_file($path)) {
                    @unlink($path);
                }
            });

            foreach ([2000, 1600, 1200, 1000] as $dimension) {
                $scale = min(1, $dimension / max(imagesx($image), imagesy($image)));
                $width = max(1, (int) round(imagesx($image) * $scale));
                $height = max(1, (int) round(imagesy($image) * $scale));
                $canvas = imagecreatetruecolor($width, $height);
                if (!$canvas) {
                    throw new Exception('Unable to prepare this camera photo. Please try again.', 422);
                }
                try {
                    imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
                    imagecopyresampled($canvas, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
                    foreach ([85, 70, 55] as $quality) {
                        if (!imagejpeg($canvas, $path, $quality)) {
                            throw new Exception('Unable to prepare this camera photo. Please try again.', 422);
                        }
                        clearstatcache(true, $path);
                        $bytes = filesize($path);
                        if ($bytes > 0 && $bytes <= self::MAX_BYTES) {
                            return new UploadedFile($path, pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.jpg', 'image/jpeg', null, true);
                        }
                    }
                } finally {
                    imagedestroy($canvas);
                }
            }
            throw new Exception('Unable to reduce this camera photo to 300 KB. Please retake it.', 422);
        } catch (\Throwable $error) {
            if ($path && is_file($path)) {
                @unlink($path);
            }
            throw $error;
        } finally {
            imagedestroy($image);
        }
    }
}
