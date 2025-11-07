# Log HTTP Requests - Developer Documentation

## Code Quality & Standards

This plugin follows [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/) and uses PHP CodeSniffer (PHPCS) for code quality checks.

### Installation

Install development dependencies using Composer:

```bash
composer install
```

### Running Code Standards Checks

Check code compliance:

```bash
composer run lint
```

or

```bash
vendor/bin/phpcs
```

### Auto-fixing Issues

Many coding standards issues can be automatically fixed:

```bash
composer run format
```

or

```bash
vendor/bin/phpcbf
```

### Configuration

The coding standards configuration is in `phpcs.xml.dist`. It includes:

- WordPress Core standards
- WordPress Extra standards (extended ruleset)
- WordPress Documentation standards
- PHP Compatibility checks (PHP 7.0+)
- Custom rules for the plugin

### Current Status

✅ **0 Errors**  
⚠️ **2 Warnings** (acceptable - related to `current_time()` usage)

The warnings are acceptable as they relate to WordPress-specific timestamp handling that works correctly in the plugin context.

## Development Workflow

1. Make your changes
2. Run `composer run lint` to check for issues
3. Run `composer run format` to auto-fix what can be fixed
4. Fix remaining issues manually if needed
5. Commit your changes

## Plugin Structure

```
log-http-requests/
├── assets/
│   ├── css/
│   │   └── admin.css
│   └── js/
│       └── admin.js
├── includes/
│   ├── class-main.php       # Main plugin class
│   ├── class-query.php      # Database queries
│   └── class-upgrade.php    # Database upgrades
├── templates/
│   └── page-settings.php    # Admin page template
├── log-http-requests.php    # Plugin bootstrap file
├── composer.json            # Composer configuration
├── phpcs.xml.dist          # PHPCS configuration
└── readme.txt              # WordPress.org readme
```

## Requirements

- **PHP:** 7.0 or higher
- **WordPress:** 5.0 or higher
- **Composer:** For development dependencies

## Contributing

When contributing to this plugin:

1. Ensure your code passes PHPCS checks
2. Follow WordPress Coding Standards
3. Add PHPDoc blocks for all functions and classes
4. Test your changes thoroughly
5. Update documentation as needed

