<?php

namespace Nip\Filesystem\Tests;

use League\Flysystem\Local\LocalFilesystemAdapter;
use Nip\Filesystem\FileDisk;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class FileDiskTest
 * @package Nip\Filesystem\Tests
 */
class FileDiskTest extends AbstractTest
{
    private $fileDisk;

    public function testResponse()
    {
        $this->fileDisk->write('file.txt', 'Hello World');
        $response = $this->fileDisk->response('file.txt');

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        static::assertInstanceOf(StreamedResponse::class, $response);
        static::assertEquals('Hello World', $content);
        static::assertEquals('inline; filename=file.txt', $response->headers->get('content-disposition'));
    }

    public function testDownload()
    {
        $this->fileDisk->write('file.txt', 'Hello World');
        $response = $this->fileDisk->download('file.txt', 'hello.txt');
        static::assertInstanceOf(StreamedResponse::class, $response);
        static::assertEquals('attachment; filename=hello.txt', $response->headers->get('content-disposition'));
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $filesystem = new FileDisk(new LocalFilesystemAdapter(TEST_FIXTURE_PATH . '/storage'));
        $filesystem->deleteDirectory('tmp');
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileDisk = new FileDisk(new LocalFilesystemAdapter(TEST_FIXTURE_PATH . '/storage/tmp'));
    }
}