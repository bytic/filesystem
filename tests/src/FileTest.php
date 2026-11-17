<?php

namespace Nip\Filesystem\Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnableToMoveFile;
use Nip\Filesystem\File;

class FileTest extends AbstractTest
{
    private $filesystem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filesystem = new Filesystem(new LocalFilesystemAdapter(TEST_FIXTURE_PATH . '/storage/tmp'));
    }

    protected function tearDown(): void
    {
        $filesystem = new Filesystem(new LocalFilesystemAdapter(TEST_FIXTURE_PATH . '/storage'));
        $filesystem->deleteDirectory('tmp');
        parent::tearDown();
    }

    public function testFileOperationsWorkWithPlainFlysystemOperator()
    {
        $file = new File($this->filesystem, 'first.txt');
        self::assertFalse($file->exists());
        self::assertFalse($file->update('missing'));
        self::assertTrue($file->write('hello'));
        self::assertTrue($file->exists());
        self::assertTrue($file->isFile());
        self::assertSame(5, $file->getSize());
        self::assertSame('text/plain', $file->getMimetype());
        self::assertIsInt($file->getTimestamp());
        self::assertSame('file', $file->getMetadata()['type']);
        self::assertTrue($file->update('updated'));
        self::assertSame('updated', $file->read());

        $stream = fopen('php://temp', 'w+');
        fwrite($stream, 'streamed');
        self::assertTrue($file->putStream($stream));
        fclose($stream);
        self::assertSame('streamed', $file->read());

        $stream = fopen('php://temp', 'w+');
        fwrite($stream, 'put stream');
        self::assertTrue($file->put($stream));
        fclose($stream);
        self::assertSame('put stream', $file->read());

        $copy = $file->copy('copy.txt');
        self::assertSame('put stream', $copy->read());
        self::assertTrue($file->rename('renamed.txt'));
        self::assertSame('renamed.txt', $file->getPath());
        self::assertSame('renamed.txt', $file->getName());
        self::assertTrue($file->delete());
        self::assertFalse($file->exists());
        self::assertFalse($file->getMetadata());
    }

    public function testDirectoryExistsAndHasDirectoryMetadata()
    {
        $this->filesystem->createDirectory('directory');
        $file = new File($this->filesystem, 'directory');
        self::assertTrue($file->exists());
        self::assertTrue($file->isDir());
        self::assertSame('dir', $file->getMetadata()['type']);
    }

    public function testFailedRenameDoesNotChangePath()
    {
        $file = new File($this->filesystem, 'missing.txt');
        try {
            $file->rename('renamed.txt');
            self::fail('Expected rename to fail.');
        } catch (UnableToMoveFile $exception) {
            self::assertSame('missing.txt', $file->getPath());
        }
    }
}
