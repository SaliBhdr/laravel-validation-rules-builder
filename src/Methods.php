<?php

namespace SaliBhdr\ValidationRules;

final class Methods
{
    public const ANY = 'any';
    //------ Http Methods ------//
    public const GET = 'get';
    public const HEAD = 'head';
    public const POST = 'post';
    public const PUT = 'put';
    public const DELETE = 'delete';
    public const CONNECT = 'connect';
    public const OPTIONS = 'options';
    public const PATCH = 'patch';
    public const PURGE = 'purge';
    public const TRACE = 'trace';

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
