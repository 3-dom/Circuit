<?php
	namespace ThreeDom\SageRoute;

	use ThreeDom\SageRoute\Template\Lexer;
	use ThreeDom\SageRoute\Template\Parser;

	class Template
	{
		public Lexer $lex;
		public Parser $parse;
		public string $file, $text, $raw;
		public array $context = [];
		public array $tokens;

		public function __construct(string $file = '', string $input = '')
		{
			$this->file = $file;
			$this->text = $input;

			$this->lex = new Lexer();
			$this->parse = new Parser($this->context);
		}

		public function lex(string $type, string $f): void
		{
            if($type == 'file')
            {
                $this->lex->setFile($f);
            }
            else
            {
                $this->lex->setInput($f);
            }

			$this->lex->buildLexer();

			$this->tokens = $this->lex->tokens;
			$this->parse->setInput($this->tokens);
		}

		public function setContext(array $c): void
		{
			$this->context = $c;
		}

		public function updateContext(array $context, string $key): void
		{
			foreach ($context as $k => $v)
				$this->context[$key][$k] = $v;
		}

		public function clean(): void
		{
			$this->setContext([]);
			$this->parse->output = '';
		}

		public function render(): void
		{
			$this->parse->parseTokens();
			print_r($this->parse->output);
			$this->parse->output = '';
		}
	}