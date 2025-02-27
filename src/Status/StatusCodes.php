<?php
	namespace ThreeDom\SageRoute\Status;

	enum StatusCodes: int
	{
		/* Informational */
		case CONTINUE = 100;
		case PROTOCOL_SWITCH = 101;
		case EARLY_HINTS = 103;

		/* Success */
		case OK = 200;
		case CREATED = 201;
		case ACCEPTED = 202;
		case NON_AUTHORITATIVE_INFORMATION = 203;
		case NO_CONTENT = 204;
		case RESET_CONTENT = 205;
		case PARTIAL_CONTENT = 206;
		case MULTI_STATUS = 207;
		case ALREADY_REPORTED = 208;
		case THIS_IS_FINE = 218;
		case IM_USED = 226;

		/* Redirection */
		case MULTIPLE_CHOICE = 300;
		case MOVED_PERMANENTLY = 301;
		case FOUND = 302;
		case SEE_OTHER = 303;
		case NOT_MODIFIED = 304;
		case USE_PROXY = 305;
		case TEMPORARY_REDIRECT = 307;
		case PERMANENT_REDIRECT = 308;

		/* Client */
		case BAD_REQUEST = 400;
		case UNAUTHORIZED = 401;
		case PAYMENT_REQUIRED = 402;
		case FORBIDDEN = 403;
		case NOT_FOUND = 404;
		case METHOD_NOT_ALLOWED = 405;
		case NOT_ACCEPTABLE = 406;
		case PROXY_AUTHENTICATION = 407;
		case REQUEST_TIMEOUT = 408;
		case CONFLICT = 409;
		case GONE = 410;
		case LENGTH_REQUIRED = 411;
		case PRECONDITION_FAILED = 412;
		case PAYLOAD_TOO_LARGE = 413;
		case URI_TOO_LONG = 414;
		case UNSUPPORTED_MEDIA = 415;
		case RANGE_NOT_SATISFIABLE = 416;
		case EXPECTATION_FAILED = 417;
		case IM_A_TEAPOT = 418;
		case PAGE_EXPIRED = 419;
		case METHOD_FAILURE = 420;
		case MISDIRECTED_REQUEST = 421;
		case UNPROCESSABLE_CONTENT = 422;
		case LOCKED = 423;
		case FAILED_DEPENDENCY = 424;
		case TOO_EARLY = 425;
		case UPGRADE_REQUIRED = 426;
		case PRECONDITION_REQUIRED = 428;
		case TOO_MANY_REQUESTS = 429;
		case SECURITY_REJECTION = 430;
		case HEADER_TOO_LARGE = 431;
		case LOGIN_TIMEOUT = 440;
		case NO_RESPONSE = 444;
		case RETRY_WITH = 449;
		case LEGAL_ISSUE = 451;
		case SSL_CERTIFICATE_ERROR = 495;
		case SSL_REQUIRED = 496;
		case HTTP_TO_HTTPS = 497;
		case INVALID_TOKEN = 498;
		case TOKEN_REQUIRED = 499;

		/* Server */
		case INTERVAL_SERVER_ERROR = 500;
		case NOT_IMPLEMENTED = 501;
		case BAD_GATEWAY = 502;
		case SERVICE_UNAVAILABLE = 503;
		case GATEWAY_TIMEOUT = 504;
		case HTTP_VERSION_UNSUPPORTED = 505;
		case VARIANT_NEGOTIATES = 506;
		case INSUFFICIENT_STORAGE = 507;
		case LOOP_DETECTED = 508;
		case BANDWIDTH_EXCEEDED = 509;
		case NOT_EXTENDED = 510;
		case AUTHENTICATION_REQUIRED = 511;
		case SITE_OVERLOADED = 529;
		case DNS_ORIGIN = 530;
		case TEMPORARILY_DISABLED = 540;
		case PROXY_READ_TIMEOUT = 598;
		case PROXY_NETWORK_TIMEOUT = 599;
	}