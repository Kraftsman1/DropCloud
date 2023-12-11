<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<!-- <p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p> -->

# DropCloud

## About DropCloud

This project is a Dropbox-like application built with Laravel and supports connecting to various external storage providers like AWS S3, Google Cloud Storage, Azure Blob Storage, and even self-hosted cloud servers.

## Features

- File Management: Create, upload, download, rename, move, and delete files and folders.
- Synchronization: Keep files across devices updated in real-time.
- Sharing: Share files and folders with other users with different access levels (read-only, edit, etc.).
- Search: Find files quickly and easily.
- Security: Securely store and access files with encryption and authentication.
- External Storage Providers: Connect to popular cloud storage services and self-hosted servers.

## Technologies

- Backend: Laravel
- Storage Libraries: aws-sdk, google-cloud-storage, azure-storage-blob, flysystem
- Database: PostgreSQL
- File Synchronization: dropbox-sdk or nextcloud-client
- Security: Encryption, OAuth2 authentication
- User Interface: Blade templates, Laravel Jetstream (optional), Vue.js or React.js (optional)

## Development Roadmap

- MVP: Focus on core functionalities like file management, storage integration, and basic sharing.
- Gradual Rollout: Add advanced features like collaboration, offline access, and file previews in subsequent updates.
- Testing and Security: Rigorously test the app for functionality, performance, and security vulnerabilities.

## Resources

- **[Laravel Jetstream](https://jetstream.laravel.com/)**
- **[Laravel File Storage](https://laravel.com/docs/10.x/filesystem)**
- **[Spatie Laravel Media Library](https://spatie.be/docs/laravel-medialibrary/v10/introduction)**
- **[Flysystem](https://packagist.org/packages/league/flysystem)**
- **[DC Blog - Build a Mini Dropbox Clone with Laravel Jetstream](https://codecourse.com/courses/create-a-mini-dropbox-clone-with-laravel-jetstream)**
- **[Robson Sousa - Dropbox with Laravel](https://www.tutsmake.com/laravel-8-backup-store-on-dropbox-tutorial/)**

## Getting Started

1. Clone the repository: ```git clone``` + repo link
2. Install dependencies: ```composer install```
3. Configure environment variables: ```.env```
4. Run migrations: ```php artisan migrate```
5. Start the application: ```php artisan serve```

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
