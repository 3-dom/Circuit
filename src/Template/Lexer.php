<?php

	namespace ThreeDom\Circuit\Template;

	use ThreeDom\Circuit\Template\Lexer\LexValues;
	use ThreeDom\Circuit\Template\Lexer\Token;

	class Lexer
	{
		public array $input;

		public array $tokens;

		public function setFile(string $file): void
		{
			$raw = file_get_contents($file);

			$this->input = preg_split('/(?={{)|(?<=}})/', $raw);
		}

		public function setInput(string $input): void
		{
			$this->input = preg_split('/(?={{)|(?<=}})/', $input);
		}

		public function buildLexer(): void
		{
			$this->tokens = [];

			foreach($this->input as $t)
			{
				$tokens = [];
				$chk = preg_match_all('/({{)(.*)(}})/', $t, $tokens);

				if($chk === 0)
				{
					$this->addToken(LexValues::TEXT, $t);
					continue;
				}

				$token_data = $tokens[2][0];

				if(str_starts_with($token_data, '`'))
				{
					$this->addToken(LexValues::TEMPLATE, $token_data);
					continue;
				}

				$this->addToken(
					match (explode(' ', $token_data)[0])
					{
						'if' => LexValues::CONDITION_START,
						'/if' => LexValues::CONDITION_END,
						'for' => LexValues::LOOP_START,
						'/for' => LexValues::LOOP_END,
						default => LexValues::IDENTIFIER,
					},
					$token_data
				);
			}
		}

		# We store tokens as objects instead of array because it saves roughly (not joking I did the benchmarks)
		# 0.00000016 seconds per every 10 operations.
		public function addToken(LexValues $type, string $data): void
		{
			$this->tokens[] = new Token($type, $data);
		}
	}