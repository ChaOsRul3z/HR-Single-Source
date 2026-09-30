# HR Admin Portal - Document & Version Management

A centralized document and version management system built with **Laravel**, **Livewire**, and **Flux UI**, tailored for corporate HR environments (incorporating SD Worx branding). It enforces a **Single Source of Truth (SSOT)** workflow, automated versioning, secure file uploads, and duplicate file prevention via cryptographic hashing.

---

## Key Features

* **Single Source of Truth (SSOT)**: Keeps your document history clear while maintaining one active, authoritative version per document code.
* **Automatic Versioning**: Automatically detects existing document slugs and increments version numbers (e.g., `v1`, `v2`).
* **Duplicate File Detection**: Computes a SHA-256 hash (`file_hash`) of uploaded files to prevent identical documents from being re-uploaded.
* **Live Reactive Filters**: Filter documents instantly by department and status (Active vs. Archived) without full page reloads.
* **Modern UI & Design System**: Styled using Tailwind CSS and Flux UI components, incorporating corporate color accents (`#008963` and `#00216B`).

---

## Database Schema (`documents` table)

The system relies on a consolidated `documents` table containing file metadata and version control attributes:

* `id`: Primary key
* `title`: Readable display title
* `document_code`: Slugified unique identifier for version tracking
* `version`: Integer tracking the document iteration
* `status`: Enum/string (`active` or `archived`)
* `department`: Target department (e.g., HR, Legal, Finance)
* `is_restricted`: Boolean for restricted admin-only view
* `storage_path`: Local or cloud storage file path
* `original_name`: Original uploaded file name
* `size`: File size in bytes
* `mime_type`: File mime type (PDF, DOCX, XLSX, etc.)
* `file_hash`: SHA-256 hash for duplicate check
* `user_id`: Uploader foreign key
* `effective_date`: Date the document takes effect

---

## Tech Stack

* **Backend**: PHP 8.2+, Laravel Framework
* **Frontend**: Laravel Livewire (Volt / Single-file component structure)
* **UI Components**: Flux UI, Tailwind CSS

---

## Installation & Setup

1. **Clone the repository & install dependencies**:
```bash
composer install
npm install && npm run build

```


2. **Configure Environment**:
Ensure your `.env` file is set up with your database and application key:
```bash
cp .env.example .env
php artisan key:generate

```


3. **Run Migrations**:
Make sure your `documents` table migration includes all required fields (`file_hash`, `storage_path`, `version`, etc.):
```bash
php artisan migrate

```


4. **Link Storage**:
Enable public disk access for document downloads:
```bash
php artisan storage:link

```


5. **Start Development Server**:
```bash
php artisan serve

```



---

## Usage

* **Uploading Documents**: Click **Nieuwe Versie Uploaden** to open the upload modal. Drag and drop up to 10 files (PDF, DOCX, XLSX up to 10MB each). The system will screen for duplicates automatically.
* **Filtering**: Use the department and status dropdowns at the top of the console to narrow down the active repository.
* **Archiving**: Toggle document statuses directly from the data table action column.

---

### Need any additional adjustments to the documentation or setup steps?
