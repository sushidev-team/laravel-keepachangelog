# Config

Export the package config file by running the following command:

```bash
php artisan vendor:publish --tag=keepachangelog
```

```php
'types' => [
     'added', 
     'changed', 
     'deprecated', 
     'removed', 
     'fixed', 
     'security'
 ],
 'repositories' => [
     'default' =>  [
         'path' => base_path('') // directory for the CHANGELOG.md file
     ]
 ]
```

You can multiple repositories by extending the "repositories" section. Default by default is the laravel application you are working on.

This package also follows the intend to provide a smooth way to update your package CHANGELOG.md file by addign it to this array.