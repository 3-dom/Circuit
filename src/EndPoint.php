<?php
	namespace ThreeDom\SageRoute;

	class EndPoint
	{
		public string $name, $method, $file, $input;
		public string $type, $callback, $event;
		public array $schema = [];
		public array $args;

		public function __construct(string $name, string $method, array $schema)
		{
			$this->name = $name;
			$this->method = $method;

			if (!$schema) {
				return;
			}
			$this->schema = $this->validateTypes($schema);
		}

		public function tpl(string $file = '', string $input = ''): EndPoint
		{
			if ($file) {
				$this->file = $file;
				$this->type = 'file';
				return $this;
			}
			$this->input = $input;
			$this->type = 'input';

			return $this;
		}

		public function event(string $event): EndPoint
		{
			$this->event = $event;
			$this->type = 'event';

			return $this;
		}

		public function callback(string $functionName, mixed ...$args): EndPoint
		{
			$this->callback = $functionName;
			$this->args = [...$args];

			return $this;
		}

		public function validateTypes(array $params): array
		{
			$validated = [];

			foreach ($params as $v) {
				if ($v[0] != '%') {
					$validated[$v] = 'mixed';
					continue;
				}

				$allowable = substr($v, 0, 2);
				$value = substr($v, 2);
				$type = match ($allowable) {
					'%s' => 'string',
					'%i' => 'integer',
					default => ''
				};

				$validated[$value] = $type;
			}
			return $validated;
		}
	}