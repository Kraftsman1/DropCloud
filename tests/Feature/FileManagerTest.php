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
}
