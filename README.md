# Environmental Authentication for CakePHP 2.x

This Auth plugin provides Authentication based on an environment variable external to the application (e.g. Apache's REMOTE_USER).

## Source

Authored by Clinton Graham for the [University of Pittsburgh](http://www.pitt.edu).  Based on CakePHP's BasicAuthenticate.php.

## License
[MIT License](http://www.opensource.org/licenses/mit-license.php)

## Using
To install, clone or submodule or place a copy of this repository in your app/Plugins directory, e.g. app/Plugins/EnvAuth.  Load the plugin in your app/Config/bootstrap.php, e.g. `CakePlugin::load('EnvAuth');`.  Reference the plugin from your Controller, e.g. `public $components = array('Auth' => array('unauthorizedRedirect' => false, 'authenticate' => array('EnvAuth.Env')));` in app/Controller/AppController.php.

## Configuration
The plugin inherits the configuration options of BaseAuthenticate, and adds an option `VARIABLE_NAME` which can be used to configure the server environment variable which will represent the username in your User model.

