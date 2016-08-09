<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('BaseAuthenticate', 'Controller/Component/Auth');

/**
 * Environment Based Authentication adapter for AuthComponent.
 *
 * Provides server environment authentication support for AuthComponent. Env Auth will
 * authenticate users against the configured userModel based on a trusted enviroment
 * variable provided by the server
 *
 * ### Using Basic auth
 *
 * In your controller's components array, add auth + the required settings.
 * ```
 *	public $components = array(
 *		'Auth' => array(
 *			'authenticate' => array('Env')
 *		)
 *	);
 * ```
 *
 * Available options include stripping a scope of the username, and forcing the
 * username to lowercase.
 *    FORCE_LOWERCASE is a boolean
 *    DROP_SCOPE will take a boolean, string, or array of strings
 *      true will remove all scopes
 *      a string or array will remove specific matched scopes
 * ```
 *	public $components = array(
 *		'Auth' => array(
 *			'authenticate' => array('EnvAuth.Env' => array(
 * 				'FORCE_LOWERCASE' => true,
				'DROP_SCOPE' => $scopes
 * 			)
 *		)
 *	);
 * ```
 *
 * You should also set `AuthComponent::$sessionKey = false;` in your AppController's
 * beforeFilter() to prevent CakePHP from sending a session cookie to the client.
 *
 * Since this Authentication is stateless you don't need a login() action
 * in your controller. The user credential will be checked on each request. If
 * valid credentials are not provided, an error will be raised
 *
 * You may also want to use `$this->Auth->unauthorizedRedirect = false;`.
 * By default, unauthorized users are redirected to the referrer URL,
 * `AuthComponent::$loginAction`, or '/'. If unauthorizedRedirect is set to
 * false, a ForbiddenException exception is thrown instead of redirecting.
 *
 * @package       Cake.Controller.Component.Auth
 * @since 2.0
 */
class EnvAuthenticate extends BaseAuthenticate {

/**
 * Constructor, completes configuration for basic authentication.
 *
 * @param ComponentCollection $collection The Component collection used on this request.
 * @param array $settings An array of settings.
 */
	public function __construct(ComponentCollection $collection, $settings) {
		parent::__construct($collection, $settings);
		if (empty($this->settings['VARIABLE_NAME'])) {
			$this->settings['VARIABLE_NAME'] = 'REMOTE_USER';
		}
	}

/**
 * Authenticate a user using Server enviroment. Will use the configured User model.
 *
 * @param CakeRequest $request The request to authenticate with.
 * @param CakeResponse $response The response to add headers to.
 * @return mixed Either false on failure, or an array of user data on success.
 */
	public function authenticate(CakeRequest $request, CakeResponse $response) {
		return $this->getUser($request);
	}

/**
 * Get a user based on information in the request. Used by cookie-less auth for stateless clients.
 *
 * @param CakeRequest $request Request object.
 * @return mixed Either false or an array of user information
 */
	public function getUser(CakeRequest $request) {
		$username = env($this->settings['VARIABLE_NAME']);
		if (isset($this->settings['FORCE_LOWERCASE']) && $this->settings['FORCE_LOWERCASE']) {
			$username = strtolower($username);
		}
		if (isset($this->settings['DROP_SCOPE'])) {
			if ($this->settings['DROP_SCOPE'] === true) {
				// Drop any scope
				$username = preg_replace('/@.*$/', '', $username);
			}
			$scopes = array();
			if (is_string($this->settings['DROP_SCOPE'])) {
				$scopes[] = $this->settings['DROP_SCOPE'];
			} elseif (is_array($this->settings['DROP_SCOPE'])) {
				$scopes = $this->settings['DROP_SCOPE'];
			}
			// Drop a specific scope
			foreach ($scopes as $scope) {
				if (strlen($username) >= strlen($scope) + 1 && strripos($username, '@'.$scope) === strlen($username) - strlen($scope) - 1) {
					$username = str_ireplace('@'.$scope, '', $username);
					break;
				}
			}
		}

		if (!is_string($username) || $username === '') {
			return false;
		}
		return $this->_findUser($username);
	}

/**
 * Handles an unauthenticated access attempt by throwing an exception
 *
 * @param CakeRequest $request A request object.
 * @param CakeResponse $response A response object.
 * @return void
 * @throws UnauthorizedException
 */
	public function unauthenticated(CakeRequest $request, CakeResponse $response) {
		$Exception = new UnauthorizedException();
		throw $Exception;
	}

}
