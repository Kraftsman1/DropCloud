<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Folder;

use Inertia\Inertia;

class FileManagerController extends Controller 
{
    public function index()
    {
        $folders = Folder::all()->map(function ($folder) {
            return [
                'id' => $folder->id,
                'name' => $folder->name,
                'icon' => $this->getFolderIcon($folder->type),
                'fileCount' => $folder->files->count(),
                'size' => $this->formatSize($folder->size),
            ];
        });

        $files = File::all()->map(function ($file) {
            return [
                'id' => $file->id,
                'name' => $file->name,
                'type' => $file->type,
                'icon' => $this->getFileIcon($file->type),
                'preview' => $file->preview_url,
                'size' => $this->formatSize($file->size),
            ];
        });

        return Inertia::render('FileManager', [
            'folders' => $folders,
            'files' => $files,
        ]);
    }

    private function getFolderIcon($type)
    {
        // Map folder types to icon components
    }

    private function getFileIcon($type)
    {
        // Map file types to icon components
    }

    private function formatSize($size)
    {
        // Format file size
    }

}