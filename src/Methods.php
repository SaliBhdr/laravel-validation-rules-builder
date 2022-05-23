<?php

namespace SaliBhdr\ValidationRules;

use Illuminate\Http\Request;

final class Methods
{
    public const ANY = 'ANY';
    //------ Http Methods ------//
    public const GET = Request::METHOD_GET;
    public const HEAD = Request::METHOD_HEAD;
    public const POST = Request::METHOD_POST;
    public const PUT = Request::METHOD_PUT;
    public const DELETE = Request::METHOD_DELETE;
    public const CONNECT = Request::METHOD_CONNECT;
    public const OPTIONS = Request::METHOD_OPTIONS;
    public const PATCH = Request::METHOD_PATCH;
    public const PURGE = Request::METHOD_PURGE;
    public const TRACE = Request::METHOD_TRACE;

    public static function toArray(): array
    {
        return [
            self::ANY,
            self::GET,
            self::HEAD,
            self::POST,
            self::PUT,
            self::DELETE,
            self::CONNECT,
            self::OPTIONS,
            self::PATCH,
            self::PURGE,
            self::TRACE,
        ];
    }
}
