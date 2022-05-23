<?php

namespace SaliBhdr\ValidationRules\Tests\Integration;

use SaliBhdr\ValidationRules\Methods;
use SaliBhdr\ValidationRules\Tests\TestCase;
use SaliBhdr\ValidationRules\Facades\RulesBuilder;

class RulesBuilderTest extends TestCase
{
    public function testItBuildsRulesOfCreateOnPostRequest()
    {
        $this->app->make('request')->setMethod(Methods::POST);

        $rules = RulesBuilder::build()
                             ->any([
                                 "name"   => "string",
                                 "family" => "string",
                                 'role'   => 'string|max:255',
                                 "email"  => "email",
                                 "mobile" => ['numeric', "digits:11"],
                             ])
                             ->create([
                                 "name"     => "required",
                                 "family"   => "required",
                                 "email"    => "required|unique:admins",
                                 "mobile"   => "required|unique:admins",
                                 'password' => 'required',
                                 'role'     => 'required',
                             ])
                             ->update([
                                 "email"  => ['unique:admins,email,1'],
                                 "mobile" => ['unique:admins,mobile,1'],
                             ])
                             ->rules();

        $this->assertEquals([
            "name"     => ["string", "required"],
            "family"   => ["string", "required"],
            "role"     => ["string", "max:255", "required"],
            "email"    => ["email", "required", "unique:admins"],
            "mobile"   => ["numeric", "digits:11", "required", "unique:admins"],
            "password" => ["required"],
        ], $rules);
    }
}
