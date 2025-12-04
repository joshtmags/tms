# Translation Management Service (TMS)

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-DC382D?style=for-the-badge&logo=redis&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2CA5E0?style=for-the-badge&logo=docker&logoColor=white)
![GitHub Actions](https://img.shields.io/badge/GitHub_Actions-2088FF?style=for-the-badge&logo=github-actions&logoColor=white)

[![Tests](https://github.com/joshtmags/tms/actions/workflows/tests.yml/badge.svg)](https://github.com/joshtmags/tms/actions)
[![PHPStan](https://img.shields.io/badge/PHPStan-Level%208-brightgreen.svg?style=flat)](https://phpstan.org/)

A high-performance, scalable Translation Management Service built with Laravel. This API-driven service provides comprehensive translation management with multi-language support, tagging, and efficient export capabilities for frontend applications.

## 🚀 Features

- **Multi-language Support**: Store translations for multiple locales (en, fr, es, de) with easy expansion
- **Smart Tagging**: Organize translations with contextual tags (web, mobile, desktop)
- **High Performance**: Optimized for 100k+ records with sub-200ms response times
- **RESTful API**: Comprehensive CRUD operations with search and filtering
- **Frontend Export**: JSON export endpoints optimized for React.js/Vue.js applications
- **Token-based Authentication**: Secure API access using Laravel Sanctum
- **Rate Limiting**: Built-in protection against API abuse
- **Comprehensive Testing**: 95%+ test coverage with performance benchmarks
- **Docker Support**: Complete containerized development environment
- **OpenAPI Documentation**: Interactive API documentation with Swagger UI
- **CDN Ready**: Configurable CDN support for static assets

## 🏗️ System Architecture

### Database Schema Design
```
languages
├── id
├── code (en, fr, es, de)
└── name

translation_groups
├── id
├── key (unique)
└── description

translations
├── id
├── translation_group_id (FK)
├── language_id (FK)
└── value

translation_tags
├── id
└── name

translation_group_tag (pivot)
├── translation_group_id (FK)
└── translation_tag_id (FK)
```

### Key Design Decisions

1. **Normalized Schema**: Separate tables for keys, translations, and tags to avoid duplication
2. **Performance Optimization**: Pre-computed JSON exports for sub-500ms response times
3. **Scalable Structure**: Supports 100k+ translation records efficiently
4. **Flexible Tagging**: Many-to-many relationship for contextual organization
5. **Cache Strategy**: Redis-based caching with intelligent invalidation

### API Performance Targets

- **CRUD Endpoints**: < 200ms response time
- **Export Endpoints**: < 500ms with 100k+ records
- **Search & Filter**: Optimized queries with proper indexing

## 🛠️ Installation

### Prerequisites

- PHP 8.2+
- MySQL 8.0+
- Redis 7.0+
- Composer

### Local Development Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/joshtmags/tms.git
   cd tms
2. **Install PHP dependencies
   ```bash
   composer install
3. Setup environment configuration
   ```bash
   cp .env.example .env
   php artisan key:generate
4. Configure environment variables
   ```bash
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tms
   DB_USERNAME=laravel
   DB_PASSWORD=
5. Run database migrations and seeders
   ```bash
   php artisan migrate
   php artisan db:seed
6. Generate Swagger documentation
   ```bash
   composer docs-generate
7. Start the development server
   ```bash
   php artisan key:generate

## 📄 API Documentation
  ```bash
  http://localhost/api/documentation
  ```

## 🔄 Continuous Integration

This project uses GitHub Actions for automated testing on every push and pull request.

### Automated Testing Pipeline

The GitHub Actions workflow (`/.github/workflows/tests.yml`) automatically runs:

- **PHPUnit Tests**: Complete test suite with 95%+ code coverage
- **Database Testing**: SQLite in-memory database for fast testing
- **PHP Syntax Check**: Code quality and syntax validation

### Workflow Status

| Branch | Status | Coverage |
|--------|--------|----------|
| Main | [![Main Branch](https://github.com/joshtmags/tms/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/joshtmags/tms/actions/workflows/tests.yml) | 95%+ |
| Develop | [![Develop Branch](https://github.com/joshtmags/tms/actions/workflows/tests.yml/badge.svg?branch=develop)](https://github.com/joshtmags/tms/actions/workflows/tests.yml) | 95%+ |

### View Test Results

- **Latest Test Run**: [View Actions Tab](https://github.com/joshtmags/tms/actions)
- **Workflow File**: [tests.yml](/.github/workflows/tests.yml)

The CI pipeline ensures code quality and prevents regressions by running the complete test suite on every change.

## 🧪 Local Environment Testing
  **Run all tests
  ```bash
  composer test
