<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use Illuminate\Support\Facades\File;
use Mockery;

class FileManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up a local temporary disk for testing
        Storage::fake('test_disk');
    }

    /** @test */
    public function it_returns_empty_arrays_when_directory_is_empty()
    {
        $fileManager = new \App\Services\FileManagerService(); // Replace with actual service path

        $response = $fileManager->listContents('', false);

        $this->assertTrue($response['success']);
        $this->assertEmpty($response['data']['files']);
        $this->assertEmpty($response['data']['folders']);
    }

    /** @test */
    public function it_lists_files_and_folders_with_correct_metadata()
    {
        // Set up files and folders in the test disk
        Storage::disk('test_disk')->put('folder1/file1.txt', 'File content');
        Storage::disk('test_disk')->makeDirectory('folder2');

        $fileManager = new \App\Services\FileManagerService(); // Replace with actual service path

        $response = $fileManager->listContents('', true);

        $this->assertTrue($response['success']);
        
        // Assert file metadata
        $files = $response['data']['files'];
        $this->assertCount(1, $files);
        $this->assertEquals('folder1/file1.txt', $files[0]['path']);
        $this->assertEquals('file', $files[0]['type']);
        $this->assertEquals('text/plain', $files[0]['mime_type']);
        $this->assertEquals('txt', $files[0]['format']);
        $this->assertEquals(12, $files[0]['size']); // "File content" has 12 bytes

        // Assert folder metadata
        $folders = $response['data']['folders'];
        $this->assertCount(1, $folders);
        $this->assertEquals('folder2', $folders[0]['path']);
        $this->assertEquals('dir', $folders[0]['type']);
    }

    /** @test */
    public function it_falls_back_to_file_extension_when_mime_type_is_unavailable()
    {
        Storage::disk('test_disk')->put('unknown_type/file.unknown', 'Some content');

        $fileManager = new \App\Services\FileManagerService(); // Replace with actual service path

        $response = $fileManager->listContents('unknown_type', false);

        $this->assertTrue($response['success']);

        $files = $response['data']['files'];
        $this->assertCount(1, $files);
        $this->assertEquals('unknown_type/file.unknown', $files[0]['path']);
        $this->assertEquals('unknown', $files[0]['mime_type']);
        $this->assertEquals('unknown', $files[0]['format']);
    }

    /** @test */
    public function it_handles_exception_gracefully()
    {
        // Mock the FileSystem to throw an exception
        $mockFilesystem = Mockery::mock(\App\Services\FileManagerService::class);
        $mockFilesystem->shouldReceive('listContents')->andThrow(new FilesystemException('Mocked exception'));

        $this->expectException(\RuntimeException::class);
        $mockFilesystem->listContents('');
    }

    public function it_uploads_a_file_successfully()
    {
        // Create a fake file to upload
        $file = UploadedFile::fake()->create('example.txt', 100); // 100 KB file

        // Define the upload path
        $filePath = 'uploads/' . $file->hashName();

        // Store the file
        Storage::disk('test_disk')->putFileAs('uploads', $file, $file->hashName());

        // Assert the file exists on the disk
        Storage::disk('test_disk')->assertExists($filePath);

        // Optional: Verify file metadata
        $storedFile = Storage::disk('test_disk')->getMetadata($filePath);
        $this->assertEquals('example.txt', $file->getClientOriginalName());
        $this->assertEquals('text/plain', $storedFile['mimetype']);
        $this->assertEquals(100 * 1024, $storedFile['size']); // 100 KB in bytes
    }
}
