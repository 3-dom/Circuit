<?php
	namespace ThreeDom\Circuit;

	use ThreeDom\Circuit\Status\StatusCodes;

	class Router
	{
		public array $endPoints = [];

		public function get(string $uri): EndPoint
		{
			return $this->addEndpoint($uri, 'GET');
		}

		public function post(string $uri): EndPoint
		{
			return $this->addEndpoint($uri, 'POST');
		}

		public function put(string $uri): EndPoint
		{
			return $this->addEndpoint($uri, 'PUT');
		}

		public function delete(string $uri): EndPoint
		{
			return $this->addEndpoint($uri, 'DELETE');
		}

		public function addEndpoint(string $uri, string $method): EndPoint
		{
			$exp = $this->parseEndpoint($uri);

			$ep = new EndPoint($exp['name'], $method, $exp['args']);

			$relativePath = str_replace($ep->name, '', $uri);
			$relativePath = preg_replace('/^\/\//', '/', $relativePath);

			$this
				->endPoints
			[$ep->name]
			[$method]
			[$relativePath]
				= $ep;

			return $ep;
		}

		public function parseEndpoint(string $uri): array
		{
			$ex = explode('/', str_replace('%20', ' ', rtrim($uri, '/')));
			array_shift($ex);

			$key = array_key_last($ex);
			$val = $ex[$key];
			if(!str_contains($val, '?'))
				return ['name' => $ex[0], 'args' => $ex, 'sup' => []];

			$ex[$key] = preg_replace('/\?.*/', '', $val);
			$arg_arr = $this->getUserSuppliedArgs($val);

			return ['name' => $ex[0], 'args' => $ex, 'sup' => $arg_arr];
		}

		public function getUserSuppliedArgs(string $val): array
		{
			$uri_args = preg_replace('/^.*?\?/', '', $val);
			$arg_str_arr = explode('&', $uri_args);

			$arg_arr = [];
			foreach($arg_str_arr as $arg)
			{
				if(!$arg)
					continue;
				if(!str_contains($arg, '='))
					continue;

				$arg_split = explode('=', $arg);
				if(count($arg_split) <= 1)
					continue;

				$arg_arr[$arg_split[0]] = $arg_split[1];
			}

			return $arg_arr;
		}

		public function validateEndpoint(string $uri, string $method): array
		{
			$exp = $this->parseEndpoint($uri);

			$status = StatusCodes::NOT_FOUND;
			$endPoint = NULL;
			$args = NULL;

			$endPoint = $this->endPoints[$exp['name']] ?? NULL;

			if(!$endPoint)
				return ['code' => $status, 'ep' => $endPoint, 'args' => $args];

			if(!array_key_exists($method, $endPoint))
				return ['code' => StatusCodes::METHOD_NOT_ALLOWED, 'ep' => $endPoint, 'args' => $args];

			$relativePoints = $endPoint[$method];

			foreach($relativePoints as $ep)
			{
				if(sizeof($exp['args']) <= array_key_last($ep->path))
					continue;

				$pathCheck = $this->pathEqualityCheck($ep->path, $exp['args']);
				if(!$pathCheck)
					continue;

				if(sizeof($ep->path) + sizeof($ep->args) != sizeof($exp['args']))
				{
					$status = StatusCodes::BAD_REQUEST;
					continue;
				}

				$this->correctArgs($ep->path, $exp['args']);

				$typeCheck = $this->argTypeCheck($ep->args, $exp['args']);
				if(!$typeCheck)
				{
					$status = StatusCodes::NOT_ACCEPTABLE;
					continue;
				}

				$status = StatusCodes::OK;
				$endPoint = $ep;
				$args = $exp['args'];

				break;
			}

			return ['code' => $status, 'ep' => $endPoint, 'args' => $args, 'userArgs' => $exp['sup']];
		}

		private function pathEqualityCheck(array $expected, array $given): bool
		{
			foreach($expected as $k => $v)
			{
				if($given[$k] == $v)
					continue;

				return FALSE;
			}

			return TRUE;
		}

		private function argTypeCheck(array $expected, array $given): bool
		{
			$i = 0;
			foreach($expected as $v)
			{
				$arg = $given[$i];
				if(is_numeric($arg))
					$arg = (int) $arg;

				$argType = gettype($arg);
				$i++;

				if($v == $argType)
					continue;

				return FALSE;
			}

			return TRUE;
		}

		private function correctArgs(array $path, array &$args): void
		{
			foreach($path as $k => $v)
				unset($args[$k]);

			$args = array_values($args);
		}
	}