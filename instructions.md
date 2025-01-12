# WordPress Book Ratings Plugin Development Instructions

## 1. Plugin Setup
1. Create the main plugin file with required headers
2. Add activation/deactivation hooks
3. Create a basic plugin structure following WordPress standards

## 2. Custom Post Type Setup
1. Register 'book' custom post type
2. Add 'ISBN' to allowed custom fields
3. Ensure the custom field key is registered correctly for REST API access

## 3. Block Template Setup
1. Create a `block-templates` directory in the plugin
2. Create `single-book.html` template file
3. Register the template for the 'book' post type
4. Add necessary template parts:
   - Title
   - Content
   - ISBN paragraph block with block bindings
   - Rating display area
   - Rating input area (for logged-in users)

## 4. Ratings System Backend
1. Create a custom database table for ratings with columns:
   - ID (primary key)
   - book_id
   - user_id
   - rating (1-5)
   - timestamp
2. Register REST API endpoints:
   - GET endpoint to fetch average rating for a book
   - GET endpoint to check if user can rate
   - POST endpoint to submit a rating
3. Add proper capability checks for the endpoints

## 5. Ratings System Frontend
1. Create JavaScript module for ratings functionality
2. Implement apiFetch for:
   - Fetching current average rating
   - Submitting new ratings
3. Create star rating UI components:
   - Static stars for average rating display
   - Interactive stars for rating input
4. Add conditional rendering based on user login status

## 6. Permissions and Security
1. Define custom capabilities for:
   - Creating books
   - Rating books
2. Add nonce verification for REST API requests
3. Add proper sanitization and validation for:
   - ISBN input
   - Rating submissions

## 7. Testing Plan
1. Test book creation and ISBN storage
2. Test template loading
3. Test rating submission:
   - As logged-in user
   - As logged-out user
4. Test average rating calculation
5. Test UI rendering in different scenarios

## 8. Documentation
1. Add inline code documentation
2. Create README.md with:
   - Installation instructions
   - Usage instructions
   - Requirements
   - Screenshots 
