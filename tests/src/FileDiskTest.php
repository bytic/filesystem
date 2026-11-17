<?php

namespace Nip\Filesystem\Tests;

use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnableToWriteFile;
use Nip\Filesystem\FileDisk;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

    public function testPutFileAsWritesUploadedFileAsStream()
    {
        $source = TEST_FIXTURE_PATH . '/storage/tmp/source.txt';
        $this->fileDisk->write('source.txt', 'uploaded');
        chmod($source, 0444);
        $file = new UploadedFile($source, 'source.txt', 'text/plain', null, true);

        self::assertSame('uploads/renamed.txt', $this->fileDisk->putFileAs('uploads', $file, 'renamed.txt'));
        self::assertSame('uploaded', $this->fileDisk->read('uploads/renamed.txt'));
    }

    public function testPutFileAsClosesStreamWhenWriteFails()
    {
        $this->fileDisk->write('source.txt', 'uploaded');
        $source = TEST_FIXTURE_PATH . '/storage/tmp/source.txt';
        $file = new UploadedFile($source, 'source.txt', 'text/plain', null, true);
        $disk = new class(new LocalFilesystemAdapter(TEST_FIXTURE_PATH . '/storage/tmp')) extends FileDisk {
            public $stream;

            public function writeStream(string $location, $contents, array $config = []): void
            {
                $this->stream = $contents;
                throw UnableToWriteFile::atLocation($location);
            }
        };

        try {
            $disk->putFileAs('', $file, 'target.txt');
            self::fail('Expected write to fail.');
        } catch (UnableToWriteFile $exception) {
            self::assertFalse(is_resource($disk->stream));
        }
    }

    public function testPutAcceptsStream()
    {
        $stream = fopen('php://temp', 'w+');
        fwrite($stream, 'stream content');
        $this->fileDisk->put('stream.txt', $stream);
        self::assertSame('stream content', $this->fileDisk->read('stream.txt'));
        self::assertTrue(is_resource($stream));
        fclose($stream);
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