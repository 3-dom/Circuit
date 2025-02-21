<?php
	namespace ThreeDom\SageRoute;

	class EndPoint
	{
		public string $name, $method;
        public ?string $file, $input;
        public ?\Closure $callback;


		public string $type;
		public array $path = [];
		public array $args = [];

		public function __construct(string $name, string $method, array $schema)
		{
			$this->name = $name;
			$this->method = $method;

			if (!$schema) {
				return;
			}
            
            $this->validateTypes($schema);
		}

		public function tpl(?string $file = null, ?string $input = null, ?\Closure $callback = null): EndPoint
		{
            $this->file = $file;
            $this->input = $input;

            $this->type = $file != null ? 'file' : 'input';
            $this->callback = $callback;

			return $this;
		}

		public function validateTypes(array $params): array
		{
			$validated = [];

			foreach ($params as $k=>$v) {
				if ($v[1] != '{') {
					$this->path[$k] = $v;
					continue;
				}

				$allowable = substr($v, 0, 1);
				$value = substr($v, 2, strlen($v) - 3);
				$type = match ($allowable) {
					's' => 'string',
					'i' => 'integer',
					default => ''
				};
                
				$this->args[$value] = $type;
			}

			return $validated;
		}

        public function executeCallback(array $args) {
            return call_user_func($this->callback, ...$args);
        }
	}