# Task: Create Contact Module

This task details the steps required to create a new module named `Contact` in the **be-inicms** backend. The module will store consumer messages containing their `name`, `email`, and `message`.

## Tasks

- [x] **1. Scaffolding & Configuration**
  - [x] Generate the new module using Artisan:
    ```bash
    php artisan module:make Contact
    ```
  - [x] Register the module's PSR-4 namespace in the root `composer.json` autoload mapping:
    ```json
    "Modules\\Contact\\": "Modules/Contact/app/"
    ```
  - [x] Run `composer dump-autoload` to update composer classmap cache.

- [x] **2. Model & Migration**
  - [x] Generate the model and migration for `ContactMessage`:
    ```bash
    php artisan module:make-model ContactMessage Contact -m
    ```
  - [x] Modify the generated migration file (e.g., `create_contact_messages_table.php`) with the following fields:
    - `id` (primary key)
    - `name` (string)
    - `email` (string)
    - `message` (text)
    - `is_read` (boolean, default: `false`)
    - `is_active` (boolean, default: `true`, aligning with standard trait filters)
    - `timestamps` (`created_at` and `updated_at`)
    - `softDeletes` (`deleted_at`)
  - [x] Implement the `ContactMessage` model (`Modules/Contact/app/Models/ContactMessage.php`):
    - Use traits: `HasFactory`, `SoftDeletes`
    - Define `$fillable` fields: `['name', 'email', 'message', 'is_read', 'is_active']`
    - Define `$casts`: `['is_read' => 'boolean', 'is_active' => 'boolean']`
    - Ensure a factory is created or stubbed for testing.

- [x] **3. Service Layer**
  - [x] Generate the service class:
    ```bash
    php artisan module:make-service ContactService Contact
    ```
  - [x] Implement business logic in `ContactService` (`Modules/Contact/app/Services/ContactService.php`):
    - Inject or reference the `ContactMessage` model.
    - Implement `findById(string $id, bool $withTrashed = false): ContactMessage`.
    - Implement `index(array $params)` utilizing the `HandlesIndexQuery` trait:
      - Search fields: `['name', 'email', 'message']`.
      - Custom filters: allow filtering by `is_read` (read/unread) if needed.
    - Implement `store(array $data): ContactMessage` for saving public submissions.
    - Implement `delete(ContactMessage $contactMessage): bool`.
    - Implement `toggleStatus(ContactMessage $contactMessage): ContactMessage`.
    - Implement `toggleReadStatus(ContactMessage $contactMessage): ContactMessage` (toggles the `is_read` state).
    - Implement `restore(string $id): ContactMessage`.
    - Implement `forceDelete(string $id): ContactMessage`.
    - Implement `handleBulkOperation(array $ids, string $operation): array` to handle bulk deletes, status toggles, reading, restoring, and force deleting.

- [x] **4. Form Requests & Validation**
  - [x] Create `StoreContactRequest` (`Modules/Contact/app/Http/Requests/StoreContactRequest.php`):
    - Validate fields:
      - `name`: `required|string|max:255`
      - `email`: `required|email|max:255`
      - `message`: `required|string|min:5`
  - [x] Create `IndexContactRequest` (`Modules/Contact/app/Http/Requests/IndexContactRequest.php`):
    - Validate parameters for pagination, sorting, search, and status.

- [x] **5. Transformers (API Resource)**
  - [x] Create `ContactMessageResource` (`Modules/Contact/app/Transformers/ContactMessageResource.php`):
    - Standardize the response payload:
      ```php
      return [
          'id' => $this->id,
          'name' => $this->name,
          'email' => $this->email,
          'message' => $this->message,
          'is_read' => $this->is_read,
          'is_active' => $this->is_active,
          'created_at' => $this->created_at,
          'updated_at' => $this->updated_at,
          'deleted_at' => $this->deleted_at,
      ];
      ```

- [x] **6. Controller & Trait Integration**
  - [x] Create the Controller `ContactMessageController` (`Modules/Contact/app/Http/Controllers/ContactMessageController.php`):
    - Extend `App\Http\Controllers\Controller`.
    - Use trait: `App\Traits\HandlesBulkAndSoftDeletes`.
    - Implement abstract methods: `getService()`, `getResourceClass()`, and `getModelName()`.
    - Explicitly write controller routes:
      - `index` (protected)
      - `store` (public)
      - `show` (protected)
      - `destroy` (protected)
      - `toggleStatus` (protected)
      - `toggleRead` (protected, custom method)

- [x] **7. Route Registration**
  - [x] Map routes in `Modules/Contact/routes/api.php`:
    - **Public Route**:
      - `POST /contact-messages` -> `store` (open to all website visitors)
    - **Protected Routes** (wrapped under `auth:sanctum` middleware):
      - Bulk routes: `bulk/delete`, `bulk/restore`, `bulk/toggle-status`, `bulk/toggle-read`
      - Single restore route: `PATCH /contact-messages/{id}/restore`
      - Single toggle-status route: `PATCH /contact-messages/{contactMessage}/toggle-status`
      - Single toggle-read route: `PATCH /contact-messages/{contactMessage}/toggle-read`
      - CRUD Resource routes using `Route::apiResource('contact-messages', ContactMessageController::class)` (excluding `store`).

- [x] **8. Feature Tests (Pest)**
  - [x] Create the feature test file (`Modules/Contact/tests/Feature/ContactMessageApiTest.php`):
    - [x] Verify guests can submit a message successfully.
    - [x] Verify guests cannot access the listing, detail, toggle, restore, delete, or bulk endpoints.
    - [x] Verify authorized administrators can view the list of messages, search, sort, and paginate.
    - [x] Verify authorized administrators can toggle read/unread status.
    - [x] Verify soft delete, restore, force delete, and bulk operations work successfully.

- [x] **9. Run & Verify**
  - [x] Execute migrations:
    ```bash
    php artisan migrate
    ```
  - [x] Run the feature tests to confirm all functionality is working:
    ```bash
    php artisan test --filter=ContactMessageApiTest
    ```
  - [x] Verify that Scramble API documentation automatically indexes the new endpoints.
