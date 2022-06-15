# New Laminas Application

## Basic setup
Rename the main folder ```ExampleModule``` and ```src/ExampleModule``` to the name
of your application. The standard naming convention for a module name is to
start with an upper case letter and follow the camelCase format.

The last folder to be renamed is ```view/example-module``` this folder needs to be
renamed with the with the dash separated format of your application name.
Each uppercase letter becomes a dash followed by the letter, ex: ExampleModule
would become example-module and ExOfAModule would become ex-of-a-module).

Next you can do a search and replace within all the files for ```ExampleModule```
and replace it with your module name.

Place your module folder (ExampleModule, now renamed to your app name) in the
```/var/www/apps``` folder of the laminas container (with ```COPY```, in prod or
```-v``` in dev). If everything was done properly, your application should show up
in your browser.

## GC Notify
GC Notify is easily implement in the new application since the building blocks
are already added to the php-base-laminas image. All that is required is
to add the configuration. You can find the configuration example in the folder
/ExampleModule/config/autoload/gc-notify.local.php. The code looks like this
```php
<?php
namespace ExampleModule;

return [
    'gc-notify-config'=>[
        __NAMESPACE__=>[
            'appName'=>__NAMESPACE__,
            'apikey'=>'API key provided by GC Notify',
            'templates'=>[
                'email1'=>'templateKeyFromGCNotify',
                'email2'=>'templateKeyFromGCNotify',
                'email3'=>'templateKeyFromGCNotify',
            ],
        ],
    ],
];
```
By default, the GcNotify object is passed to the IndexController, if you have
the configuration set, your values will be passed to the GcNotify object.

## Public Assets (images, css, js)
By default, the configuration is already set to allow js, css, jpg, jpeg, png,
gif and svg to be served from the ```/public/``` folder in the module. If an
asset has the same name as set by another module, only one will be served
(dertermined by the loading order of the modules). You can prefix the path of
the asset with the name of the module, for example you can use
```/img/cute-kitten-playing.jpg``` or ```/example-module/img/cute-kitten-playing.jpg```.
If you want to add/remove file type that the server can serve, you can modify the
```[your-module]/config/autoload/public-asset.global.php```
