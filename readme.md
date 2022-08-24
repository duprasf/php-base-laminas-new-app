# New Laminas Application

## Basic setup
Rename the main folder ```ExampleModule``` and ```src/ExampleModule``` to the name
of your application. The standard naming convention for a module name is to
start with an upper case letter and follow the camelCase format.

The last folder to be renamed is ```view/example-module``` this folder needs to be
renamed with the dash separated format of your application name.
Each uppercase letter becomes a dash followed by the letter, ex: ExampleModule
would become example-module and ExOfAModule would become ex-of-a-module).

Next you can do a search and replace within all the files for ```ExampleModule```
and replace it with your module name.

Place your module folder (ExampleModule, now renamed to your app name) in the
```/var/www/apps``` folder of the laminas container (with ```COPY```, in prod or
```-v``` in dev). If everything was done properly, your application should show up
in your browser.

## Development Environment
In development environment you can set the environment variable "PHP_DEV_ENV"
to 1, this will display the errors and disabled the config cache.
```
docker run -d --name laminas -p 80:80 \
    -e PHP_DEV_ENV=1 \
    jack.hc-sc.gc.ca/php/php-base-laminas:latest
```

## Extra modules
A number of modules were developed to help speed up the development of applications

### UserAuth
The UserAuth module is a basic authentication module. To enable it, you will
need to load the module and define a DB connection where the users will be stored.

To load the module, you will need to either modify the config/autoload/_modules.local.php
or create  symlink from module/UserAuth to apps/UserAuth.

You will also need to define a ```user-pdo``` service to connect to a DB. You can
also specify the password rules you want to implement on that particular server.

An example of such a service configuration would be
```php
<?php

use \UserAuth\Model\User;

return [
    'service_manager' => [
        'factories' => [
            // You can overwrite the User class with your own class
            // this could be to add your own functions/fields
            // User::class => Factory\UserFactory::class,
            'user-pdo'=>function($sm) {
                return new \PDO(
                    'mysql:host=host-db;dbname=users;',
                    'userauth',
                    'password',
                    array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
                );
            },
        ],
        'services'=>[
            // you can change the rules for the password
            'user-auth-password-rules'=>[
                'minSize'=>12,
                'atLeastOneLowerCase'=>true,
                'atLeastOneUpperCase'=>true,
                'atLeastOneNumber'=>true,
                'atLeastOneSpecialCharacters'=>'{}[]()\/\'"`~,;:.<>*^@$%+?&!=#_-', // make sure the "-" is the last character
                //'pattern'=>'([a-zA-Z0-9\{\}\[\]\(\)\/\\\'"`~,;:\.<>\*\^\-@\$%\+\?&!=#_]{12,})i',
            ],
            'user-auth-registration-allowed' => true,
            'user-auth-must-verify-email' => true,
            'user-auth-default-user-status' => User::STATUS_ACTIVE,
        ],
    ],
];
```

This service only provides registration, email validation, password reset and
credential validation. It does not provide any type of access control or
authorization to any part of any applications. The easiest way to use this service is to
use a descendant class in your project that extends \UserAuth\Model\User. See
an example below

```php
<?php
namespace ExampleModule\Model;

use \UserAuth\Model\User as ParentUser;
use \PDO as PDO;

class User extends ParentUser
{
    protected $db;
    public function setDb(PDO $db)
    {
        $this->db = $db;
        return $this;
    }
    public function getDb()
    {
        return $this->db;
    }

    public function authenticate(String $email, String $password) : self
    {
        parent::authenticate($email, $password);
        $userId = $this->getUserId();
        $this->loadUserById($userId);
        return $this;
    }

    protected function loadUserById(int $userId) : bool
    {
        if(parent::loadUserById($userId)) {
            $db = $this->getDb();
            $prepared = $db->prepare('SELECT accessLevel FROM `users` WHERE userId = ?');
            $prepared->execute(array($userId));
            $this->data = array_merge($this->data, $prepared->fetch(PDO::FETCH_ASSOC));
            return true;
        }
        return false;
    }

    public function getAccessLevel()
    {
        return $this->data['accessLevel'];
    }
}
```

### Stockpile
Stockpile was a content management system I created a while back. Once of the
functionality was to load file from filesystem. This is the functionality that
was implemented in this framework. It allows pages to be created very quickly
and it can be done by a different group of users.

To activate this feature, you only need to add this configuration in your autoload
with the path to where your pages will be stored.
```php
<?php
return [
    'view_manager'=>[
        'template_path_stack'=>[
            'FileSystemPage' => dirname(dirname(__DIR__)).'/site',
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

### Easy script
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

