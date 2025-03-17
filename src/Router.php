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
			$this->endPoints[$uri][$method] = $ep;

			return $ep;
		}

		public function parseEndpoint(string $uri): array
		{
			$ex = explode('/', str_replace('%20', ' ', rtrim($uri, '/')));
			array_shift($ex);

			return ['name' => $ex[0], 'args' => $ex];
		}

		public function validateEndpoint(string $uri, string $method): array
		{
			$exp = $this->parseEndpoint($uri);

			$status = StatusCodes::NOT_FOUND;
			$endPoint = NULL;
			$args = NULL;

			foreach($this->endPoints as $ep)
			{
				if(!array_key_exists($method, $ep))
					continue;

				$n_ep = $ep[$method];
				if($n_ep->name != $exp['name'])
					continue;

				if(sizeof($exp['args']) <= array_key_last($n_ep->path))
					continue;

				$pathCheck = $this->pathEqualityCheck($n_ep->path, $exp['args']);
				if(!$pathCheck)
					continue;

				if(sizeof($n_ep->path) + sizeof($n_ep->args) != sizeof($exp['args']))
				{
					$status = StatusCodes::BAD_REQUEST;
					continue;
				}

				$this->correctArgs($n_ep->path, $exp['args']);

				$typeCheck = $this->argTypeCheck($n_ep->args, $exp['args']);
				if(!$typeCheck)
				{
					$status = StatusCodes::NOT_ACCEPTABLE;
					continue;
				}

				$status = StatusCodes::OK;
				$endPoint = $n_ep;
				$args = $exp['args'];

				break;
			}

			return ['code' => $status, 'ep' => $endPoint, 'args' => $args];
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