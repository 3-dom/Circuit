<?php
	namespace ThreeDom\SageRoute;

	use ThreeDom\SageRoute\Status\StatusCodes;

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
			$name = $ex[0];

			$args = [];

			if (sizeof($ex) > 1) {
				array_shift($ex);
				$args = $ex;
			}
            
			return ['name' => $name, 'args' => $args];
		}

		public function validateEndpoint(string $uri, string $method): array
		{
			$exp = $this->parseEndpoint($uri);

			$status = StatusCodes::NOT_FOUND;
			$endPoint = NULL;
			$args = NULL;

			foreach ($this->endPoints as $ep) {
				if (!array_key_exists($method, $ep)) {
					continue;
				}

				$n_ep = $ep[$method];
				if ($n_ep->name != $exp['name']) {
					continue;
				}

				if (sizeof($exp['args']) !== sizeof($n_ep->schema)) {
					$status = StatusCodes::BAD_REQUEST;
					continue;
				}

				$status = StatusCodes::OK;
				$endPoint = $n_ep;
				break;
			}

			return ['code' => $status, 'ep' => $endPoint, 'args' => $exp['args']];
		}
	}