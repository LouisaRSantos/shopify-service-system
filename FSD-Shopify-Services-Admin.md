# Shopify Services Admin

## Document Version
- Version: 1.0
- Date: 2026-06-11
- Author: Project Team

---

## 1. Overview

**Project Name:** Shopify Services Admin

**Goal:**
Provide an admin panel for managing Shopify-integrated customer operations, system configuration, and application logs from a Laravel-based web application.

**Summary:**
Shopify Services Admin is a Laravel application that supports authenticated admin users in viewing dashboard metrics, managing Shopify customer operations, importing and exporting customer data, updating application configuration settings, and reviewing audit and system logs. The backend integrates directly with Shopify APIs using the configured store URL, API version, and access token.

---

## 2. Scope

### In scope
- Admin authentication and session management.
- Role-based access control for admin-only sections.
- Dashboard summary data via API.
- Customer management endpoints, including create, import, export, and search operations.
- Shopify API integration for customer creation, invite sending, bulk export, search, and counts.
- Configuration management with view and update operations.
- System configuration change logging.
- Log pages for customer activity, export history, and system logs.
- Single-page application style rendering for AJAX requests and full page loads.

### Out of scope
- Shopify app installation flow or OAuth authorization.
- Full Shopify storefront management.
- Advanced analytics beyond current summary endpoints.
- Synchronous background job execution for export processing (unless already implemented externally).
- Multi-tenant support.

---

## 3. Stakeholders

- Admin Users: manage configuration, view logs, and operate Shopify customer-related tasks.
- System Administrators: maintain environment configuration and Shopify credentials.
- Product Owner / Business Owner: ensure Shopify customer sync and configuration functionality.
- Developers: extend and maintain the Laravel application.

---

## 4. Glossary

- **Shopify Service**: backend class responsible for Shopify API integration.
- **System Settings**: application configuration values stored in the local database.
- **Configuration Log**: audit entries tracking changes to system settings.
- **Admin Only**: pages and APIs accessible only to authenticated administrators.
- **Bulk Export**: Shopify GraphQL bulk customer export operation.

---

## 5. User Roles and Permissions

### Roles
- `Admin`: full access to dashboard, customer pages, configuration, and logs.
- `Authenticated Web User`: access to basic authenticated pages such as dashboard and customers list.

### Permissions
- Authenticated routes are protected by `auth.web` middleware.
- Admin-only routes are protected by `admin.only` middleware.
- Configuration and logs pages are only available to admin users.

---

## 6. Functional Requirements

### 6.1 Authentication and Session
- `GET /login` renders the login page.
- `POST /login` authenticates users and establishes a session.
- `GET /logout` ends the session.
- `middleware('no.cache')` ensures no cached sensitive data on login or authenticated pages.

### 6.2 Dashboard
- `GET /` returns the dashboard view.
- `GET /api/dashboard/summary` returns dashboard summary data.
- Dashboard should support AJAX loading of content.

### 6.3 Customer Management
- `GET /customers` returns the customer list view.
- `GET /customers/create` shows customer creation form.
- `POST /customers/store` submits a new customer creation request.
- `GET /customers/import` shows the import page.
- `GET /customers/import/template` downloads the customer import template.
- `POST /customers/import/process` processes imported customer data.
- `GET /customers/export` shows the export page.
- `POST /customers/export/start` initiates export processing.
- `GET /customers/export/status` checks export completion status.
- `GET /customers/export/download` downloads exported customer data.

### 6.4 Shopify Integration
- Backend uses `App\Services\ShopifyService`.
- Uses `config/shopify.php` values: `shop_url`, `api_version`, `access_token`.

Supported Shopify operations:
- `testConnection()`: GET `shop.json`.
- `createCustomer(first, last, email)` and `createCustomerFromArray(array)`.
- `sendInvite(customerId)`.
- `graphql(query)` for generic GraphQL operations.
- `startCustomerBulkExport(queryFilter)` to launch bulk export operations.
- `getBulkExportStatus()` to query bulk export status.
- `getCustomersByIds(ids)` to fetch customers by Shopify IDs.
- `searchCustomerByEmail(email)` to search by email.
- `searchCustomers(query, limit)` to search customers by Shopify query string.
- `countCustomersByState(state)` to count customers by state using paginated search.
- `countCustomers(query)` to retrieve Shopify customer count.
- `getCustomers(params)` to list Shopify customers with query params.

### 6.5 Configuration Management
- `GET /api/configuration` returns all system settings.
- `POST /configuration/update` updates one or more configuration settings.
- Validation rules:
  - `settings` must be an array.
  - each setting item must include `name` and `value`.
- Changes are recorded in `system_config_logs`.
- Cache is invalidated for changed setting keys.

### 6.6 Logs Management
- `GET /logs/customer-activity` and `GET /api/logs/customer-activity` support customer activity logs.
- `GET /logs/export-history` and `GET /api/logs/export-history` support export history logs.
- `GET /logs/system-logs` and `GET /api/logs/system-logs` support system logs.
- Logs pages are admin-only.
 - `GET /logs/api-usage` and `GET /api/logs/api-usage` support API usage logs (new).
 - API usage logs capture requests made to internal and external APIs for auditing and performance analysis.

---

## 7. Data Model

### 7.1 SystemSetting
- Table: `system_settings`
- Fields:
  - `id`
  - `setting_key` (string)
  - `setting_value` (string / text)
  - `description` (string)
- Purpose: store configurable application settings.

### 7.2 SystemConfigLog
- Table: `system_config_logs`
- Fields:
  - `id`
  - `setting_key`
  - `old_value`
  - `new_value`
  - `changed_by` (user id)
  - `changed_at` (timestamp)
- Purpose: audit changes to system settings.

### 7.3 User
- Standard Laravel `users` table with fields such as `id`, `name`, `email`, `password`, `remember_token`.
- Purpose: authenticate and identify users.

### 7.4 Shopify Customer Data
- Not persisted locally by default; retrieved and managed through Shopify API.
- Local operations may include import/export workflows and temporary data handling.

### 7.5 APIUsageLog
- Table: `api_usage_logs`
- Fields:
  - `id`
  - `endpoint` (string) — the internal or external endpoint path called (e.g. `/api/customers`, `https://.../customers.json`).
  - `method` (string) — HTTP method (GET, POST, PUT, DELETE).
  - `request_summary` (text) — concise summary of request payload (PII redacted).
  - `response_status` (integer) — HTTP status code returned.
  - `response_time_ms` (integer) — elapsed time in milliseconds.
  - `user_id` (nullable integer) — authenticated user who triggered the request.
  - `ip_address` (string) — source IP of the request.
  - `created_at` (timestamp)
- Purpose: audit API calls initiated by the admin application, monitor performance, and detect anomalous usage.


---

## 8. API Endpoints

### Authentication
- `GET /login`
- `POST /login`
- `GET /logout`

### Dashboard
- `GET /api/dashboard/summary`

### Customer
- `GET /customers`
- `GET /customers/create`
- `POST /customers/store`
- `GET /customers/import`
- `GET /customers/import/template`
- `POST /customers/import/process`
- `GET /customers/export`
- `POST /customers/export/start`
- `GET /customers/export/status`
- `GET /customers/export/download`

### Configuration
- `GET /api/configuration`
- `POST /configuration/update`

### Logs
- `GET /logs/customer-activity`
- `GET /api/logs/customer-activity`
- `GET /logs/export-history`
- `GET /api/logs/export-history`
- `GET /logs/system-logs`
- `GET /api/logs/system-logs`
- `GET /logs/api-usage`
- `GET /api/logs/api-usage`

---

## 9. User Interface and Workflows

### 9.1 Login
- Display login form.
- On successful login, redirect to dashboard.
- Show errors for invalid credentials.

### 9.2 Dashboard
- Display summary cards and statistics.
- AJAX load content for responsive experience.
- Provide navigation to customers, configuration, and logs.

### 9.3 Customers
- List existing customers and allow filtering/search.
- Create new Shopify customers via form.
- Import customers from CSV or spreadsheet template.
- Initiate customer export and monitor status.

### 9.4 Configuration
- Display all available system settings.
- Allow inline editing or batch update of settings.
- Submit updates through API.
- Show confirmation and updated audit entries.

### 9.5 Logs
- Provide pages for customer activity, export history, and system logs.
- Include date filtering and paginated results if available.
- Only accessible to admin users.

---

## 10. Non-functional Requirements

- Platform: Laravel PHP application.
- Browser support: modern browsers with AJAX and JSON support.
- Performance: low-latency dashboard and configuration load.
- Availability: admin features available for authenticated users.
- Security: session protection, middleware enforcement, validation on POST requests.
- Maintainability: modular service classes and controller separation.

---

## 11. Security and Compliance

- All authenticated pages protected by `auth.web` middleware.
- Admin-only functionality protected by `admin.only` middleware.
- All update endpoints validate incoming request payloads.
- No caching of authenticated pages via `no.cache` middleware.
- Shopify credentials kept in environment variables and loaded from `config/shopify.php`.
- Sensitive response data must not be exposed to unauthorized users.

---

## 12. Integration and Environment

### Shopify Integration
- Shopify config values are stored in `.env` and referenced in `config/shopify.php`:
  - `SHOPIFY_STORE_URL`
  - `SHOPIFY_API_VERSION`
  - `SHOPIFY_ACCESS_TOKEN`

- The `ShopifyService` uses the Laravel HTTP client to call Shopify REST and GraphQL endpoints.
- GraphQL bulk export operations use `bulkOperationRunQuery` and `currentBulkOperation`.

### Local Environment
- Application runs on the Laravel stack.
- Uses database storage for system settings, logs, sessions, and any local user/auth data.

---

## 13. Assumptions and Constraints

- Shopify integration assumes valid store URL and access token.
- Customer import/export workflows rely on correct CSV or spreadsheet data formats.
- System settings are predefined in the database before updates.
- Log pages depend on available log data and may require separate log ingestion logic if not implemented.
- The app is a single-tenant admin interface, not a public Shopify app or storefront.

---

## 14. Future Enhancements

- Add Shopify OAuth / app authorization flow.
- Implement background queue workers for export processing.
- Add detailed customer search and filter UI.
- Expand audit logs for import/export actions.
- Add role management and fine-grained permissions.
- Add automated tests for API endpoints and services.

---

## 15. Approval

- Product Owner:
- Technical Lead:
- Date:
