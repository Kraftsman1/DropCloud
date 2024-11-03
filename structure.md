laravel-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── FileController.php
│   │   │   ├── FolderController.php
│   │   │   └── StorageProviderController.php
│   │   ├── Requests/
│   │   │   ├── FileUploadRequest.php
│   │   │   ├── FolderCreateRequest.php
│   │   │   └── StorageProviderRequest.php
│   │   └── Resources/
│   │       ├── FileResource.php
│   │       └── FolderResource.php
│   ├── Models/
│   │   ├── File.php
│   │   ├── Folder.php
│   │   └── StorageProvider.php
│   ├── Services/
│   │   ├── FileManagerService.php
│   │   └── StorageProviderService.php
│   ├── Events/
│   │   ├── FileUploaded.php
│   │   └── FileDeleted.php
│   ├── Listeners/
│   │   ├── ProcessUploadedFile.php
│   │   └── CleanupDeletedFile.php
│   └── Jobs/
│       ├── GenerateFileThumbnail.php
│       └── SyncFiles.php
├── database/
│   └── migrations/
│       ├── 2024_01_01_create_storage_providers_table.php
│       ├── 2024_01_02_create_folders_table.php
│       └── 2024_01_03_create_files_table.php
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   ├── FileManager/
│   │   │   │   ├── BreadcrumbNav.vue
│   │   │   │   ├── ContextMenu.vue
│   │   │   │   ├── FileGrid.vue
│   │   │   │   ├── FileList.vue
│   │   │   │   ├── FilePreview.vue
│   │   │   │   ├── FileUploader.vue
│   │   │   │   ├── FolderTree.vue
│   │   │   │   └── SearchBar.vue
│   │   │   └── StorageProvider/
│   │   │       ├── ProviderForm.vue
│   │   │       └── ProviderSelector.vue
│   │   ├── Pages/
│   │   │   ├── FileManager/
│   │   │   │   ├── Index.vue
│   │   │   │   ├── Show.vue
│   │   │   │   └── Shared.vue
│   │   │   └── StorageProvider/
│   │   │       ├── Create.vue
│   │   │       ├── Edit.vue
│   │   │       └── Index.vue
│   │   ├── Layouts/
│   │   │   └── FileManagerLayout.vue
│   │   ├── Composables/
│   │   │   ├── useFileManager.js
│   │   │   ├── useFileUpload.js
│   │   │   └── useStorageProvider.js
│   │   └── Types/
│   │       ├── file.d.ts
│   │       └── storage-provider.d.ts
│   └── css/
│       └── file-manager.css
├── routes/
│   └── web.php
└── tests/
    ├── Feature/
    │   ├── FileManagementTest.php
    │   └── StorageProviderTest.php
    └── Unit/
        ├── FileManagerServiceTest.php
        └── StorageProviderServiceTest.php