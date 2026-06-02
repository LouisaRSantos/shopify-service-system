# AJAX Navigation Implementation

## What was added

- Added a new client-side script at `public/assets/js/ajax-navigation.js`.
- Included this script in `resources/views/layouts/app.blade.php`.
- Wrapped the main content area in `app.blade.php` with `<main id="ajax-content">` so AJAX responses can replace only the page content.
- Added `ajax-link` to the top navigation brand links in `resources/views/partials/navbar.blade.php`.
- Fixed `resources/views/customers/index.blade.php` so it extends the main layout and uses `@section('content')`.

## What the script does

- Intercepts clicks on links with the `.ajax-link` class.
- Uses `fetch()` to load the target page HTML without a full browser reload.
- Parses the returned HTML and extracts the `#ajax-content` section.
- Replaces the current page content with the newly loaded content.
- Updates `document.title` from the loaded page.
- Pushes new history state using `history.pushState()` so the browser back/forward buttons continue to work.
- Handles browser `popstate` events to load previously visited pages via AJAX.
- Falls back to a normal page navigation if the AJAX request fails.

## Notes

- This implementation is designed for same-origin links only.
- Only links marked with `.ajax-link` are intercepted.
- Pages loaded via AJAX must include the `#ajax-content` container in their final rendered HTML.
