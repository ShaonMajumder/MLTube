<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ThumbnailController extends Controller
{
    /**
     * ThumbnailController Class
     * 
     * This controller handles the processing and serving of image thumbnails,
     * with an option to cache images either in Redis or using the file system.
     * 
     * Performance Comparison: Redis vs. File System
     * 
     * 1. Redis (In-Memory Caching)
     * - **Speed**: Redis is an in-memory key-value store, meaning it serves data directly from RAM.
     *   Accessing data from memory is extremely fast compared to reading from disk.
     * - **Latency**: Redis has very low latency (typically sub-millisecond), making it ideal for 
     *   high-frequency access to small objects like image thumbnails.
     * - **CPU and I/O**: Redis uses CPU and RAM, reducing disk I/O load. For applications that 
     *   handle many simultaneous requests, Redis can avoid file system bottlenecks.
     * - **Scalability**: Redis is distributed and can scale horizontally, allowing it to handle 
     *   large traffic spikes better than a traditional file system.
     * - **Memory Usage**: Storing images in Redis can consume significant RAM, so it’s not ideal 
     *   for large images or massive image libraries unless you have substantial memory resources.
     *
     * 2. File System (Disk-Based Caching)
     * - **Speed**: Accessing files from disk is slower than accessing data from memory. Disk read 
     *   speeds depend on the type of storage (SSD is faster than HDD), but it typically has higher
     *   latency compared to Redis.
     * - **Latency**: Disk access times are in the range of milliseconds to tens of milliseconds, 
     *   which can be slower than Redis, especially when the file system is under heavy load.
     * - **Disk I/O**: High-frequency access to files can cause disk I/O bottlenecks, especially in 
     *   high-traffic environments, slowing down the application.
     * - **Storage Usage**: File systems are typically used to store large amounts of data 
     *   efficiently. While disk space is generally more abundant than memory, accessing images on
     *   disk is slower than serving them from memory.
     * - **Caching Mechanisms**: Operating systems can cache frequently accessed files in memory, 
     *   improving file system performance, but this is still slower than Redis.
     * 
     * Conclusion:
     * - Redis is faster than serving images from the file system, as it serves data directly from
     *   memory with minimal latency. It’s especially suitable for high-traffic applications where
     *   speed and low latency are critical.
     * - However, the file system is more cost-effective for large-scale image storage, as disk 
     *   space is cheaper and more abundant than RAM. For applications with fewer performance 
     *   demands, file system caching can be sufficient.
     * 
     * Best Use Case:
     * - If your application requires extremely fast access to frequently requested thumbnails 
     *   (e.g., for a homepage or video platform), Redis caching is the better option.
     * - If you’re dealing with a large library of images or don’t need sub-millisecond response 
     *   times, file system caching will be more cost-effective.
     * 
     * Combined Strategy:
     * - You could also combine both strategies: use Redis for hot (frequently accessed) images and
     *   file system caching for cold storage (less frequently accessed images).
     * 
     */
    
    const MAX_WIDTH = 1920;
    const MAX_HEIGHT = 1080;
    const DEFAULT_WIDTH = 100;
    const DEFAULT_HEIGHT = 100;
    const CACHED_IMGE_QUALITY = 80;
    const REDIS_CACHE_DURATION = 3600; // Cache duration in seconds (1 hour)

    /**
     * Allowed image sizes for resizing (width x height).
     *
     * Common image sizes:
     * - 150x100 (Small thumbnail)
     * - 300x200 (Medium thumbnail)
     * - 600x400 (Large thumbnail)
     *
     * Limiting the sizes helps improve performance by reducing disk space usage,
     * processing time, and ensuring consistent image dimensions across the site.
     *
     * @var array
     */
    protected $allowedSizes = [
        '150x100',  // Small thumbnail, commonly used for video previews
        '300x200',  // Medium thumbnail, suitable for larger grids or lists
        '600x400',  // Large thumbnail, often used for feature images or detailed previews
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    public function show($filename)
    {
        [$width,$height] = $this->getImageSize();
        // return $this->showfileCachedImage($filename, $width, $height); 
        return $this->showRedisCachedImage($filename, $width, $height);     
    }

    public function getImageSize(){
        $width = (int) request()->query('w', self::DEFAULT_WIDTH);
        $height = (int) request()->query('h', self::DEFAULT_HEIGHT);
        
        if ($width <= 0) {
            $width = self::DEFAULT_WIDTH;
        }
        if ($height <= 0) {
            $height = self::DEFAULT_HEIGHT;
        }

        $width = min($width, self::MAX_WIDTH);
        $height = min($height, self::MAX_HEIGHT);

        if (!$this->isValidSize($width, $height)) {
            $width = self::DEFAULT_WIDTH;
            $height = self::DEFAULT_HEIGHT;
        }

        return [$width,$height];
    }

    /**
     * Check if the requested size is valid.
     */
    protected function isValidSize($width, $height)
    {
        $size = "{$width}x{$height}";
        return in_array($size, $this->allowedSizes);
    }

    /**
     * Serve a resized or cached image.
     *
     * This method retrieves the requested image, resizes it according to
     * the given width and height, and serves it. If the requested dimensions
     * are not allowed, it defaults to the size 150x100. The resized image
     * is cached to improve performance on future requests.
     *
     * @param string $filename The name of the image file to resize.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function showfileCachedImage($filename, $width, $height, $enableCaching=true) : BinaryFileResponse
    {
        $defaultImagePath = storage_path('app/public/thumbnails/image-not-found.png');
        $path = storage_path("app/public/thumbnails/{$filename}");
        if (!file_exists($path)) {
            $path = $defaultImagePath;
            $filename = basename($defaultImagePath);
        }

        $cachePath = storage_path("app/public/thumbnails/cache/{$width}x{$height}/{$filename}");
        if ($enableCaching && file_exists($cachePath)) {
            return response()->file($cachePath);
        }

        $image = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio(); // Maintain aspect ratio
            $constraint->upsize(); // Prevent upsizing
        });

        if($enableCaching){
            if (!file_exists(dirname($cachePath))) {
                mkdir(dirname($cachePath), 0755, true);
            }
            $image->save($cachePath, self::CACHED_IMGE_QUALITY);
            return response()->file($cachePath); // reduces the time
        } else {
            return $image->response('png'); // on the fly if not saved
        }
        
    }

    /**
     * Serve a resized image cached in Redis.
     *
     * This method retrieves the requested image, resizes it according to
     * the given width and height, caches it in Redis, and serves it.
     *
     * @param string $filename The name of the image file to resize.
     * @param int $width The requested width of the image.
     * @param int $height The requested height of the image.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function showRedisCachedImage($filename, $width, $height)
    {
        $redisKey = "image_cache:{$width}x{$height}:{$filename}";
        $cachedImage = Redis::get($redisKey);
        if ($cachedImage) {
            return response($cachedImage)->header('Content-Type', 'image/png');
        }

        $defaultImagePath = storage_path('app/public/thumbnails/image-not-found.png');
        $path = storage_path("app/public/thumbnails/{$filename}");
        if (!file_exists($path)) {
            $path = $defaultImagePath;
            $filename = basename($defaultImagePath);
        }

        // Resize the image
        $image = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio(); // Maintain aspect ratio
            $constraint->upsize(); // Prevent upsizing
        });

        // Save image to a buffer
        $buffer = (string) $image->encode('png', self::CACHED_IMGE_QUALITY);

        // Store the image buffer in Redis
        Redis::setex($redisKey, self::REDIS_CACHE_DURATION, $buffer);

        // Return the image response
        return response($buffer)->header('Content-Type', 'image/png');
    }
}
