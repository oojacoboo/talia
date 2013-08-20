<?php

/**
 * Talia
 *
 * An open source PHP 5.4 Micro-Framework
 *
 * Copyright (c) 2013, Three Leaf Creative.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// ------------------------------------------------------------------------

namespace Talia;

class Talia {

	const VERSION = '1.5.1'; // Current version of Talia

	private $methods = [
		'GET', 'POST', 'PUT', 'DELETE' // Allowed HTTP Methods
	];

	private $routes = [
		'GET' => [], 'POST' => [], 'PUT' => [], 'DELETE' => [] // Setting up the routes for each HTTP method
	];

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	Void
	 */

	public function __construct()
	{
		if (!version_compare(PHP_VERSION, '5.4', '>=')) {
		    throw new Talia_Exception('Talia requires PHP 5.4 or higher. Your version is ' . PHP_VERSION);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Version
	 * Returns the current version of Talia you are using
	 *
	 * @access	public
	 * @return	string
	 */

	public function Version()
	{
		return self::VERSION;
	}

	// --------------------------------------------------------------------

	/**
	 * GET
	 * Looks for HTTP GET requests
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	function
	 * @return	bool
	 */

	public function get($pattern, $callback = NULL)
	{
		return $this->route(['GET'], $pattern, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * POST
	 * Looks for HTTP POST requests
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	function
	 * @return	bool
	 */

	public function post($pattern, $callback = NULL)
	{
		return $this->route(['POST'], $pattern, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * PUT
	 * Looks for HTTP PUT requests
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	function
	 * @return	bool
	 */

	public function put($pattern, $callback = NULL)
	{
		return $this->route(['PUT'], $pattern, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * DELETE
	 * Looks for HTTP DELETE requests
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	function
	 * @return	bool
	 */

	public function delete($pattern, $callback = NULL)
	{
		return $this->route(['DELETE'], $pattern, $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Unmatched
	 * Invoked when no requests match what you supply
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	function
	 * @return	bool
	 */

	public function unmatched($callback = NULL)
	{
		return $this->route($this->methods, ':all', $callback);
	}

	// --------------------------------------------------------------------

	/**
	 * Route
	 * Processes all the HTTP Method requests
	 *
	 * @access	public
	 * @param	array
	 * @param	string
	 * @param	array
	 * @param	function
	 * @return	bool
	 */

	public function route($methods = [], $pattern, $callback = NULL)
	{
		$route = array('pattern' => $pattern, 'callback' => $callback);
		foreach ($methods as $method)
		{
			if (!in_array($method, $this->methods)) 
			{
				continue;
			}
			$this->routes[$method][] = $route;
		}
		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Run
	 * Takes all your method routes and processes them
	 *
	 * @access	public
	 * @return	data|bool
	 */

	public function Run()
	{
		$method = $this->request_method();
		$url = $this->request_url();
		$routes = $this->routes[$method];
		if (empty($routes))
		{
			return FALSE;
		}
		foreach ($routes as $route)
		{
			$args = $this->matches($route, $url);
			if ($args === FALSE)
			{
				continue;
			}
			return call_user_func_array($route['callback'], $args);
		}
		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Matches
	 * Matches routes with requests
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */

	private function matches($route, $url)
	{
		if ($route['pattern'] === '/')
		{
			return (($url === '/') ? [] : FALSE);
		}
		list($pattern, $capture) = $this->patterns($route);
		$count = preg_match($pattern, $url, $matches);
		if ($count === 0)
		{
			return FALSE;
		}
		$args = [];
		foreach ($capture as $offset)
		{
			$args[] = (isset($matches[$offset]) ? $matches[$offset] : NULL);
		}
		return $args;
	}

	// --------------------------------------------------------------------

	/**
	 * Patterns
	 * Looks for patterns in the routes
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */

	private function patterns($route)
	{
		$input = ltrim($route['pattern'], '/');
		$pattern = '';
		$parts = explode('/', $input);
		$optional = FALSE;
		$bracket_count = 0;
		$capture_ints = array();
		foreach ($parts as $part)
		{
			$catch_all = (substr($part, 0, 4) === ':all');
			$capture = (substr($part, 0, 1) === ':');
			$optional = ($optional or substr($part, -1) === '?');
			$capture_i = NULL;
			$part = strtr($part, array(
				':num' => ':[0-9]+',
				':alpha' => ':[a-z-_]+',
				':alnum' => ':[a-z0-9-_]+',
				':all' => ':.*'
			));
			if ($capture)
			{
				$capture_i = $bracket_count + 1;
				$part = '(' . substr($part, 1) . ')';
				$bracket_count++;
			}
			if ($optional)
			{
				if ($capture)
				{
					$capture_i++;
				}
				$part = '(/' . $part . ')?';
				$bracket_count++;
			}
			else
			{
				$part = '/' . $part;
			}
			$pattern .= $part;
			if ($capture_i)
			{
				$capture_ints[] = $capture_i;
			}
			if ($catch_all)
			{
				break;
			}
		}
		$pattern = ';^' . $pattern . '/?$;i';
		return array($pattern, $capture_ints);
	}

	// --------------------------------------------------------------------

	/**
	 * Request URL
	 * Returns the requested url
	 *
	 * @access	public
	 * @return	string
	 */

	private function request_url()
	{
		$url = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
		$url = explode('?', $url);
		return '/' . trim($url[0], '/');
	}

	// --------------------------------------------------------------------

	/**
	 * Request Method
	 * Determines the type of method used in the request
	 *
	 * @access	public
	 * @return	string
	 */

	private function request_method()
	{
		$method = (isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : NULL);
		$method = strtoupper($method);
		if (!in_array($method, $this->methods))
		{
			$method = 'GET';
		}
		return $method;
	}
}

/* END OF FILE */