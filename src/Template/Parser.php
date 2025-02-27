<?php
	namespace ThreeDom\SageRoute\Template;

	use ThreeDom\SageRoute\Template;
	use ThreeDom\SageRoute\Template\Lexer\LexValues;

	class Parser
	{

		public string $output = '';
		public array $tokens;
		public ?array $context;

		public function __construct(?array &$context)
		{
			$this->context = &$context;
		}

		public function setInput(array $tokens): void
		{
			$this->tokens = $tokens;
		}

		public function setContext(array &$context): void
		{
			$this->context = &$context;
		}

		public function updateContext(mixed $context, string $key): void
		{
			foreach ($context as $k => $v)
				$this->context[$key][$k] = $v;
		}

		public function parseTokens(int $start = 0, int $end = -1): void
		{
			$next_token = -1;

			$len = $this->getLength($start, $end);
			$render_tokens = array_slice($this->tokens, $start, $len, TRUE);

			foreach ($render_tokens as $index => $token) {
				$next_token = match ($token->type) {
					LexValues::CONDITION_START => $this->condition($token->data, $index),
					LexValues::LOOP_START => $this->loop($token->data, $index),
					default => $next_token
				};

				if ($index < $next_token)
					continue;

				$this->appendOutput($token->type, $token->data);
			}
		}

		public function appendOutput(LexValues $type, string $data): void
		{
			$this->output .= match ($type) {
				LexValues::TEXT => $data,
				LexValues::TEMPLATE => $this->recursiveRender($data),
				LexValues::IDENTIFIER => $this->identify($data),
				default => ''
			};
		}

		public function getLength(int $start, int $end): int
		{
			if ($end === -1)
				return sizeof($this->tokens) - $start;

			return $end - $start;
		}

		public function condition(string $token, int $offset): int
		{
			return $offset;
		}

		public function loop(string $token, int $offset): int
		{
			$args = preg_split('/ +/', $token);
			$items = $this->identify($args[1]);

			$s = $offset + 1;
			$n = $s;

			while (TRUE) {
				$t = $this->tokens[$n];

				if ($t->type == LexValues::LOOP_END)
					break;

				$n++;
			}

			foreach ($items as $i) {
				$this->updateContext($i, $args[3]);
				$this->parseTokens($s, $n);
			}

			return $n + 1;
		}

		public function recursiveRender(string $input): string
		{
			$t = new Template();

			$t->lex('file', substr($input, 1));

			$t->setContext($this->context);
			$t->parse->parseTokens();

			return $t->parse->output;
		}

		public function identify(string $input): string|array
		{
			$split = explode('.', $input);
			$scope = $this->context[array_shift($split)];

			foreach ($split as $s) {
				$search = $s;
				if (!$scope)
					break;

				if (!array_key_exists($search, $scope)) {
					$scope = NULL;
					break;
				}

				$scope = $scope[$search];
			}

			return $scope ?? '';
		}
	}