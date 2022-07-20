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

## Extract translation strings into a .po file
You can easily extract all the strings that can be translated from your module
using the TranslationExtractor module. This operation is performed from inside
the container (docker exec ...). The module is used in the CLI by calling
the vendor/bin/laminas executable. Executing this command wihout any args will
give you a list of available command. One of them will be the ```translation:extract```
command. This requires two parameters, first is the input folder to scan, second
is the output file name.
```
(in /var/www)$ vendor/bin/laminas translation:extract apps/MyApp output.po
```
This will create an /var/www/output.po file with all the strings found in
/var/www/apps/MyApp.

This searches for translated route parts and the use of ```->translate()``` in views
and controller.

### Easy script
I created an easy to use script to extract translation when using containers.
This script will extract the translation from the folder and copy the file
outside the container in one comand.
```
./translation-extract <ContainerName> <PathToScan> <OutputFile.po>
```
Since the php-base-laminas container base working directory is /var/www, you can
use apps/YourAppName as the path to scan.
The output is local path, not in the container (so ```output.po```to receive the
file in the current dir or ```~/my-app/language``` to get the file in your home
directory).
```
./translation-extract AppContainer apps/AppName output.po
```

