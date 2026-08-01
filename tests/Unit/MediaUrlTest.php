<?php

namespace Tests\Unit;

use App\Support\MediaUrl;
use PHPUnit\Framework\TestCase;

class MediaUrlTest extends TestCase
{
    public function test_it_optimizes_cloudinary_images(): void
    {
        $url = 'https://res.cloudinary.com/demo/image/upload/v1/aanaya/cover.jpg';

        $optimized = MediaUrl::image($url, 720, 450, 'fill');

        $this->assertStringContainsString(
            '/image/upload/f_auto,q_auto:eco,dpr_auto,w_720,c_fill,h_450/',
            $optimized
        );
    }

    public function test_it_leaves_local_images_unchanged(): void
    {
        $url = '/images/about-image.png';

        $this->assertSame($url, MediaUrl::image($url, 720));
    }

    public function test_it_optimizes_cloudinary_videos(): void
    {
        $url = 'https://res.cloudinary.com/demo/video/upload/v1/aanaya/hero.mp4';

        $this->assertStringContainsString(
            '/video/upload/f_auto,q_auto:eco,w_1280,c_limit/',
            MediaUrl::video($url)
        );
    }
}
