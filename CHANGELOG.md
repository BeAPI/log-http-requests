# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.5.0] - 2025-11-07

### Added
- **Search functionality** to filter HTTP requests by URL with real-time search
- **Backtrace information** for each HTTP request to identify request origins
- PHP CodeSniffer (PHPCS) configuration for WordPress Coding Standards compliance
- Composer support for development dependencies
- Comprehensive PHPDoc documentation throughout the codebase
- Developer documentation (CONTRIBUTING.md)
- Asset versioning for better cache management
- Database upgrade mechanism for seamless schema updates
- Search box with "Clear" button in admin toolbar
- Support for Enter key to trigger search

### Changed
- Refactored main plugin class into separate file (`includes/class-main.php`)
- Improved code organization and structure
- Enhanced all classes with proper PHPDoc blocks
- Updated all inline comments to follow WordPress standards
- Improved admin interface layout with flexbox toolbar

### Enhanced
- Full compliance with WordPress Coding Standards (WPCS)
- Security improvements with proper nonce verification
- Better data sanitization and escaping
- Improved code maintainability
- Modal interface now displays backtrace information
- Search results pagination works seamlessly

### Fixed
- Code formatting issues
- Missing function visibility declarations
- Improved security for AJAX requests
- Database column addition during upgrades
- HTML rendering in admin interface (template literal issue)
- Backtrace now properly converts array to string for display

## [1.4.1] - Previous Release

### Fixed
- PHP 8 deprecation notices

## [1.4] - Previous Release

### Added
- Extra AJAX role validation (props pluginvulnerabilities.com)

## [1.3.2] - Previous Release

### Security
- Escaped URL field to prevent possible XSS (props Bishop Fox)

## [1.3.1] - Previous Release

### Changed
- Ensured compatibility with WP 5.8

## [1.3] - Previous Release

### Changed
- Minor PHP cleanup
- Ensured compatibility with WP 5.7

## [1.2] - Previous Release

### Added
- Moved "Log HTTP Requests" to the Tools menu (props @aaemnnosttv)
- Added "Status" column to show HTTP response code (props @danielbachhuber)
- Added prev/next browsing to the detail modal (props @marcissimus)
- Added keyboard support (up, down, esc) to the detail modal (props @marcissimus)
- Added raw timestamp to "Date Added" column on hover
- Added hook documentation to readme

## [1.1] - Previous Release

### Added
- `lhr_log_data` hook to customize logged data
- `lhr_expiration_days` hook

## [1.0.4] - Previous Release

### Changed
- Minor styling tweak

## [1.0.3] - Previous Release

### Changed
- Better visibility for long URLs

## [1.0.2] - Previous Release

### Changed
- Minor design tweaks
- Replaced `json_encode` with `wp_send_json`

## [1.0.1] - Previous Release

### Changed
- Tested compatibility against WP 4.9.4

## [1.0.0] - Initial Release

### Added
- Initial plugin release
- HTTP request logging functionality
- Admin interface for viewing logs
- Database table for storing requests

