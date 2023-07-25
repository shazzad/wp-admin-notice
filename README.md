# WP Admin Notice

Helper class to display admin notices.

## Installation

```bash
composer require shazzad/wp-admin-notice
```

## Initialization

Initialize the service provider.

```php
\Shazzad\WpAdminNotice\Provider::setup();
```

## Adding notice.

Call the function `do_action` with appropriate parameter to add a notice.
This code must be placed before wp admin area start rendering content on screen.
Use `admin_notices`, `all_admin_notices` or `network_admin_notices` action hook to add notice.

```php
do_action(
	'swpan_notice',
	array(
		'success' => __('You setting is stored successfully.'),
		'id'      => 'plugin-setting',
	)
);

do_action(
	'swpan_notice',
	array(
		'error' => __('Sorry, we could not save your settings.'),
	)
);
```

## Adding user notice.

Use user notice when you perform some action and redirect the user to a page. The notice
will be stored for until it is displayed to the user.
Add a third parameter to the action hook to add a user notice.

```php
do_action(
	'swpan_notice',
	array(
		'message' => __('You setting is stored successfully.'),
		'type'    => 'success',
		'id'      => 'my-notice-id',
	),
	'user'
);

do_action(
	'swpan_notice',
	array(
		'message' => __('Sorry, we could not save your settings.'),
		'type'    => 'error',
		'id'      => 'my-notice-id',
	),
	'user'
);
```

### Requirements

* WordPress: 6.0.1
* PHP: 7.4
* Tested: 6.2.2
