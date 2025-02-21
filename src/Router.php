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

			return ['name' => $ex[0], 'args' => $ex];
		}

		public function validateEndpoint(string $uri, string $method): array
		{
			$exp = $this->parseEndpoint($uri);

			$status = StatusCodes::NOT_FOUND;
			$endPoint = NULL;
			$args = NULL;
            $pageChk = TRUE;

			foreach ($this->endPoints as $ep) {
				if (!array_key_exists($method, $ep)) {
					continue;
				}

				$n_ep = $ep[$method];
                
				if ($n_ep->name != $exp['name']) {
					continue;
				}

                if(sizeof($exp['args']) <= array_key_last($n_ep->path)) {
                    continue;
                }

                foreach($n_ep->path as $k=>$v) {
                    if($exp['args'][$k] == $v) {
                        continue;
                    }
                    $pageChk = FALSE;
                }
                
                if(!$pageChk) {
                    continue;
                }

                if(sizeof($n_ep->path) + sizeof($n_ep->args) != sizeof($exp['args'])) {
				    $status = StatusCodes::BAD_REQUEST;
                    continue;
                }

				$status = StatusCodes::OK;
				$endPoint = $n_ep;

                foreach($n_ep->path as $k=>$v) {
                    unset($exp['args'][$k]);
                }
                $args = $exp['args'];
			}
            
			return ['code' => $status, 'ep' => $endPoint, 'args' => $args];
		}
	}