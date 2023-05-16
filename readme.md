# New Laminas Application

## Basic setup
Rename the main folder ```ExampleModule``` and ```src/ExampleModule``` to the name
of your application. The standard naming convention for a module name is to
start with an upper case letter and follow the camelCase format.

The last folder to be renamed is ```view/example-module``` this folder needs to be
renamed with the dash separated format of your application name.
Each uppercase letter becomes a dash followed by the letter, ex: ExampleModule
would become example-module and ExOfAModule would become ex-of-a-module.

Next you can do a search and replace within all the files for ```ExampleModule```
and replace it with your module name.

Place your module folder (ExampleModule, now renamed to your app name) in the
```/var/www/apps``` folder of the laminas container (with ```COPY``` in prod or
as a volume during development). If everything was done properly, your application
should show up in your browser.

Remember to change the route in ```/config/module.config.php``` the route for the
ExampleModule is currently ```/en/my-app```

## User and API
I included a much bigger example in the module ```ExampleModuleWithUserAndApi```.
As the name suggest, I added an example of API and using the UserAuth module.
Both are described in more details below. you can test this module on ```/en/my-app-with-user```

## Development Environment
In development environment you can set the environment variable "PHP_DEV_ENV"
to 1, this will display the errors and disabled the config cache.
```
docker run -d --name laminas -p 80:80 \
    -e PHP_DEV_ENV=1 \
    jack.hc-sc.gc.ca/php/php-base-laminas:latest
```

## Full documentation
You can find the full documentation of each module/class from this framework and
example modules in .phpdoc/index.html

## Extra modules
A number of modules were developed to help speed up the development of applications

### UserAuth
The UserAuth module is a basic authentication module. It is enabled by default, you can
use it by extending the stardard classes or using them as is.

#### Using a Active Directory
The class ```UserAuth\Model\LdapUser``` provide a way to authenticate a user using Active Directory.
Which means the user can use their same username/password used on Windows. For an example,
you can look at ```ExampleModuleWithUserAndApi\Model\UserLdap```

#### Using a Database
You can also use ```UserAuth\Model\DbUser``` which use a DB with credentials to validate the identity
of the user. In that case you can use the "parentDb" or build your decendent class to overwrite a few
methods (```authenticate()```, ```register()```, ```resetPassword()```, etc.) to set your own DB schema.

To use the "parentDb" flow, you will need to define a service called ```user-parent-db``` which will be
an instance of PDO. The Schema needs be as follows:

```sql
CREATE TABLE `user` (
  `userId` int(10) UNSIGNED NOT NULL,
  `email` varchar(200) NOT NULL,
  `emailVerified` tinyint(2) UNSIGNED NOT NULL,
  `password` varchar(250) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `user`
  MODIFY `userId` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

CREATE TABLE `userToken` (
  `token` varchar(40) NOT NULL,
  `userId` int(10) UNSIGNED NOT NULL,
  `type` enum('confirmEmail','resetPassword') NOT NULL,
  `dateCreated` datetime NOT NULL DEFAULT current_timestamp(),
  `expiredAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `userToken`
  ADD PRIMARY KEY (`token`),
  ADD KEY `userId` (`userId`);

COMMIT;
```

A log feature is also built in with the UserAuth and would require a service
called ```user-log-pdo``` (yes, you can use the same PDO connection and set a service alias).
The schema for the user log, should be:

```sql
CREATE TABLE `userAudit` (
  `id` int(10) UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `userId` int(10) UNSIGNED DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `ip` varchar(45) NOT NULL,
  `useragent` text NOT NULL,
  `type` enum('usr_login','usr_logout','usr_login_failed','usr_reset_pwd_request','usr_reset_pwd_handled','usr_confirm_email_handled','usr_register','usr_register_failed','usr_change_password') NOT NULL,
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `userAudit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

ALTER TABLE `userAudit`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

COMMIT;
```

### Stockpile
Stockpile was a content management system I created a while back. One of the
functionality was to load file from filesystem. This is the functionality that
was implemented in this framework. It allows pages to be created very quickly,
it can be done by a different group of users and provide pages in the framework
using the WET/CDTS. Stockpile can also be used as a URL shortener/redirect with
the use of a DB.

To activate this feature, you only need to add this configuration in your autoload
with the path to where your pages will be stored.
```php
<?php
namespace Stockpile;

/**
* Stockpile is a file parser that was created to speed up the
* conversion of an existing web site to WET. Stockpile can also
* be used as a URL shortener/redirect with the use of a DB.
*
* The path where the file-system-route can find its files is
* called 'FileSystemPage' and found in
* 'view_manager'=>[
*   'template_path_stack'=>[
*       'FileSystemPage' => "path/",
*   ],
* ]
*/
return [
    'router' => [
        'routes' => [
            // To enable the "file system page" keep this route
            'file-system-page'=>[
                'type'=>Route\FileSystemRoute::class,
                'options'=>[
                    'regex'=>'/(?P<lang>en|fr)(?P<path>/.*)?$',
                    'spec'=>'/%lang%/%path%',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'file-system-page',
                    ],
                    'constraints'=>[
                        'path'=>'^[\w\d/-]*$',
                        'lang'=>'en|fr',
                    ],
                ],
            ],
            // to enable the URL shortner/redirect, keep this route (and the next)
            // a service called 'stockpilePdoMovedPages' will be required
            'moved-page'=>[
                'type'=>Route\MovedPageRoute::class,
                'options'=>[
                    'regex'=>'/(?P<path>.*)$',
                    'spec'=>'/%path%',
                    'defaults'=>[
                        'controller'=>Controller\IndexController::class,
                        'action'=>'moved-page',
                    ],
                ],
            ],
            // This route would allow admin to maintain the DB
            // a service called 'stockpilePdoMovedPages' will be required
            // the 'route' can be changed ({stockpile-moved-pages}) as long
            // as the route name stays the same. The code will use that name to build URLs
            'moved-pages-admin'=>[
                'type'=>'Segment',
                'options'=>[
                    'route'=>'/:locale/{stockpile-moved-pages}',
                    'defaults'=>[
                        'controller'=>Controller\AdminController::class,
                        'action'=>'moved-pages-admin',
                    ],
                    'constraints'=>[
                        'locale'=>'en|fr',
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'stockpilePdoMovedPages' => function($sm) {
                return new \PDO(
                    'mysql:host=localhost;dbname=dbName;',
                    'username',
                    'password',
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
                );
            },
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            // This is where you would put the file-system-page files
            'FileSystemPage' => dirname(__DIR__) . '/site',
        ],
    ],
];
```

The pages can use ```$this->metadata``` to set the metadata for each page.
This can be 'title', 'description', 'issued', 'modified', 'extra-css', 'extra-js',
'titleH1', 'creator' and 'breadcrumbs'

* title, this is the title of the page. It will be displayed in the header/title
and the H1 on the page.
* description, the description of your page in the header of the page.
* issued, the issued date. If omitted, the current day is used.
* modified, the modified date of the page. If omitted, the issued date is used.
* creator, the creator/owner of the page. Default is 'Government of Canada, Health Canada'.
* breadcrumbs, the breadcrumbs for this page. Format needs to be (name|url)(name|url).
* titleH1, if you want different texts in the header/title and the H1. This should
only be used when your title requires HTML tage (ex: abbr).
* extra-css, this allow the page to have its own css style sheet.
Format: /css/style1.css|/css/style2.css
* extra-js, this allow the page to have its own javascript file loaded.
Format: /js/js1.js|/js/js2.js

```php
<?php
$this->metadata['title']='Stockpile Test Successful';
$this->metadata['issued']='2022-08-02';
$this->metadata['modified']='2022-08-13';
?>
Content of the stockpile test page
```

### GC Notify
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
the configuration set, your values will be passed to the GcNotify object. This
is done by the IndexController, but of course if you change this factory, the
GcNotify might not be sent to the controller.

To use the GcNotify, just use something like the code below
```php
$result = $notify->sendEmail(
    'hc.imsd.web-dsgi.sc@canada.ca', // recipient of the email
    $gc-notify-email-template, // template ID from GC Notify
    array('varInTemplate'=>'value'), // needs to have ALL variable from the template
    $config['gc-notify-error-api'] // API Key if different than the default one set by default
);

if(!$result) {
    $error = json_decode($notify->lastPage, true);
    print 'The error message is: '.$error['errors'][0]['message'];
    print 'The last status from GcNotify was : '.$notify->lastStatus;

}
```

### Public Assets (images, css, js)
By default, the configuration is already set to allow js, css, jpg, jpeg, png,
gif and svg to be served from the ```/public/``` folder in the module. If an
asset has the same name as set by another module, only one will be served
(determined by the loading order of the modules). You can prefix the path of
the asset with the name of the module, for example you can use
```/img/cute-kitten-playing.jpg``` or ```/example-module/img/cute-kitten-playing.jpg```.
If you want to add/remove file type that the server can serve, you can modify the
```[your-module]/config/autoload/public-asset.global.php```

## Extract translation strings into a .po file
You can easily extract all the strings that can be translated from your module
using the TranslationExtractor module. This operation is performed from inside
the container (docker exec ...). The module is used in the CLI by calling
the vendor/bin/laminas executable. Executing this command without any args will
give you a list of available command. One of them will be the ```translation:extract```
command. This requires two parameters, first is the input folder to scan, second
is the output file name.
```
(in /var/www)$ vendor/bin/laminas translation:extract apps/MyApp output.po
```
This will create an /var/www/output.po file with all the strings found in
/var/www/apps/MyApp.

This searches for translated route parts and the use of ```->translate()``` in
any php (controllers, models, factories, etc.) and phtml (views) files.

### Extract Translation
I created an easy-to-use script to extract translation when using containers.
This script will extract the translation from the folder and copy the file
outside the container in one command.
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

### PostCSS and JavaScript processing with Gulp
A gulp script runs on the Laminas container that will process PostCSS and merge/compress JavaScript.

#### PostCSS
The entry file is set as ```/apps/*/source/postcss/main.pcss``` (see https://github.hc-sc.gc.ca/hs/php-base-laminas/blob/master/code/gulpfile.js).
This will compile any file you @import in the main and output in ```/apps/*/public/css```.
The script also watches for any change in ```/apps/*/source/postcss/*.pcss``` and execute
the process when changes are detected.

The list of plugins loaded and processed by PostCSS are:
- postcss-nested
    - Allows you to nest your code, this is the most useful feature of postcss in my opinion. You can write something like this:
```css
.phone {
    &_title {
        width: 500px;
        @media (max-width: 500px) {
            width: auto;
        }
        body.is_dark & {
            color: white;
        }
    }
    img {
        display: block;
    }
}
```
This would become:
```css
.phone_title {
    width: 500px;
}
@media (max-width: 500px) {
    .phone_title {
        width: auto;
    }
}
body.is_dark .phone_title {
    color: white;
}
.phone img {
    display: block;
}
```
- postcss-color-function
- postcss-color-mod-function
    - Allows you to use colors and color modification like this:
```css
.whatever {
    color: color(red a(10%));
    color: color(red lightness(50%)); /* == color(red l(50%)); */
    color: color(hsla(125, 50%, 50%, .4) saturation(+ 10%) w(- 20%));
    color: color-mod(yellow blend(red 50%));
    color: hsl(30 100% 50% / 100%);
}
```
- postcss-simple-vars
    - Allows you to PostCSS variable (you can also use CSS variable)
```css
$dir:    top;
$blue:   #056ef0;
$column: 200px;

.menu_link {
  background: $blue;
  width: $column;
}
.menu {
  width: calc(4 * $column);
  margin-$(dir): 10px;
}
```
- postcss-custom-media
    - Allows you to define some custom media size and use variable in your CSS
```css
@custom-media --small-viewport (max-width: 30em);

@media (--small-viewport) {
    /* styles for small viewport */
}
```
- postcss-each
    - Allows you to loop through content to create multiple group of similar rulesets
```css
@each $icon in foo, bar, baz {
  .icon-$(icon) {
    background: url('icons/$(icon).png');
  }
}
```
Would become
```css
.icon-foo {
  background: url('icons/foo.png');
}

.icon-bar {
  background: url('icons/bar.png');
}

.icon-baz {
  background: url('icons/baz.png');
}
```
- postcss-each-variables
    - Allows you to loop using variables
```css
:root {
    --breakpoints: (
        sm: 576px,
        md: 768px,
        lg: 992px,
        xl: 1200px
    );
}

@each $key, $value in var(--breakpoints) {
    .container-$(key) {
        max-width: $(value);
    }
}
```
- postcss-for
    - Allows you to loop using numbers
```css
@for $i from 1 to 3 {
    .b-$i { width: $(i)px; }
}
```
You can also use the "by" keyword to change the step increment
```css
@for $i from 1 to 5 by 2 {
    .b-$i { width: $(i)px; }
}
```

#### Javascript
The gulp script will also take any JavaScript files located in ```/apps/*/source/js/```, combine them
into ```/apps/*/public/js/script.js``` and ```/apps/*/public/js/script.min.js```
