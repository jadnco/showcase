# Showcase

A small web app to display your projects.

After cloning this repo to your local machine, navigate to `includes/init.php` and make sure to update the following constants with relevant data:

```php
// Get ROOT_PATH by calling __DIR__ (without /public)
define("ROOT_PATH", "/Applications/XAMPP/xamppfiles/htdocs/showcase");
define("INC_PATH", ROOT_PATH . "/includes/");

// Base url (public folder); eg. http://example.com/public
define("BASE_URL", "http://localhost/showcase/public");

// Add the home url; eg. http://example.com
define("HOME", "http://localhost/showcase");
```

You then must edit line 95 in `public/assets/js/admin.js`:

```javascript
var base_url = "http://localhost/showcase/public";
```

All user and project data is stored within `JSON` files, which are automatically created as you create your login. These files are given a `CHMOD` flag of `0600` for security measures.

*Vector icons from [Graphicburger](http://graphicburger.com)*
