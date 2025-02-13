<?php
	namespace ThreeDom\SageRoute\Template\Lexer;

	enum LexValues
	{
		case TEXT;
		case TEMPLATE;
		case IDENTIFIER;
		case CONDITION_START;
		case CONDITION_END;
		case LOOP_START;
		case LOOP_END;
	}