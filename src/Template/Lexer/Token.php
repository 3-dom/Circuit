<?php

	namespace ThreeDom\Circuit\Template\Lexer;

	class Token
	{
		public LexValues $type;

		public string $data;

		public function __construct(LexValues $type, string $data)
		{
			$this->type = $type;
			$this->data = $data;
		}
	}