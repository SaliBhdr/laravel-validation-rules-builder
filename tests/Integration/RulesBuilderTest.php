<?php

namespace SaliBhdr\ValidationRules\Tests\Integration;

use SaliBhdr\ValidationRules\Facades\RulesBuilder;
use SaliBhdr\ValidationRules\Methods;
use SaliBhdr\ValidationRules\Tests\TestCase;

class RulesBuilderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->make('config')->set('rules.cache.enable', false);
    }

    public function testRuleBuilderBuildsRulesOfCreateOnPostRequest(): void
    {
        $this->app->make('request')->setMethod(Methods::POST);

        $rules = RulesBuilder::build()
            ->any([
                'name'   => 'string',
                'family' => 'string',
                'role'   => 'string|max:255',
                'email'  => 'email',
                'mobile' => ['numeric', 'digits:11'],
            ])
            ->create([
                'name'     => 'required',
                'family'   => 'required',
                'email'    => 'required|unique:admins',
                'mobile'   => 'required|unique:admins',
                'password' => 'required',
                'role'     => 'required',
            ])
            ->update([
                'email'  => ['unique:admins,email,1'],
                'mobile' => ['unique:admins,mobile,1'],
            ])
            ->rules();

        $this->assertEquals([
            'name'     => ['string', 'required'],
            'family'   => ['string', 'required'],
            'role'     => ['string', 'max:255', 'required'],
            'email'    => ['email', 'required', 'unique:admins'],
            'mobile'   => ['numeric', 'digits:11', 'required', 'unique:admins'],
            'password' => ['required'],
        ], $rules);
    }

    public function testRuleBuilderBuildsRulesOfUpdateOnPatchRequest(): void
    {
        $this->app->make('request')->setMethod(Methods::PATCH);

        $anyRules = [
            'name'   => 'string',
            'family' => 'string',
            'role'   => 'string|max:255',
            'email'  => 'email',
            'mobile' => ['numeric', 'digits:11'],
        ];

        $methodRules = [
            'email'  => ['unique:admins,email,1'],
            'mobile' => ['unique:admins,mobile,1'],
        ];

        $rules = RulesBuilder::build()
            ->any($anyRules)
            ->create([
                'name'     => 'required',
                'family'   => 'required',
                'email'    => 'required|unique:admins',
                'mobile'   => 'required|unique:admins',
                'password' => 'required',
                'role'     => 'required',
            ])
            ->update($methodRules)
            ->rules();

        $this->assertEquals([
            'name'   => ['string'],
            'family' => ['string'],
            'role'   => ['string', 'max:255'],
            'email'  => ['email', 'unique:admins,email,1'],
            'mobile' => ['numeric', 'digits:11', 'unique:admins,mobile,1'],
        ], $rules);
    }

    public function testRuleBuilderBuildsRulesOfUpdateOnPutRequest(): void
    {
        $this->app->make('request')->setMethod(Methods::PUT);

        $anyRules = [
            'name'   => 'string',
            'family' => 'string',
            'role'   => 'string|max:255',
            'email'  => 'email',
            'mobile' => ['numeric', 'digits:11'],
        ];

        $methodRules = [
            'email'  => ['unique:admins,email,1'],
            'mobile' => ['unique:admins,mobile,1'],
        ];

        $rules = RulesBuilder::build()
            ->any($anyRules)
            ->create([
                'name'     => 'required',
                'family'   => 'required',
                'email'    => 'required|unique:admins',
                'mobile'   => 'required|unique:admins',
                'password' => 'required',
                'role'     => 'required',
            ])
            ->update($methodRules)
            ->rules();

        $this->assertEquals([
            'name'   => ['string'],
            'family' => ['string'],
            'role'   => ['string', 'max:255'],
            'email'  => ['email', 'unique:admins,email,1'],
            'mobile' => ['numeric', 'digits:11', 'unique:admins,mobile,1'],
        ], $rules);
    }

    public function testRuleBuilderAddsMethodValidationIfTheValidationIsNotExistsInTheAnyMethod(): void
    {
        $this->app->make('request')->setMethod(Methods::POST);

        $anyRules = [
            'x' => ['numeric', 'digits:11'],
            'y' => ['string', 'max:255'],
        ];

        $methodRules = [
            'z' => 'string',
        ];

        $rules = RulesBuilder::build()
            ->any($anyRules)
            ->post($methodRules)
            ->rules();

        $this->assertEquals([
            'x' => ['numeric', 'digits:11'],
            'y' => ['string', 'max:255'],
            'z' => ['string'],
        ], $rules);
    }

    public function testRuleBuilderCanOverrideTheAnyMethodRules(): void
    {
        $this->app->make('request')->setMethod(Methods::POST);

        $anyRules = [
            'x' => ['numeric', 'digits:11'],
            'y' => ['string', 'max:255'],
        ];

        $methodRules = [
            'z' => 'string',
        ];

        $rules = RulesBuilder::build()
            ->any($anyRules)
            ->post($methodRules)
            ->rules(Methods::POST, true);

        $this->assertEquals([
            'z' => ['string'],
        ], $rules);
    }

    public function testRuleBuilderOnlyReturnsOnlyTheAnyRulesIfTheMethodIsNotAllowed(): void
    {
        $this->app->make('request')->setMethod('not-allowed-method');

        $rules =RulesBuilder::build()
                ->any([
                    'x' => ['numeric', 'digits:11'],
                    'y' => ['string', 'max:255'],
                ])
                ->post([
                    'z' => 'string',
                ])
                ->rules();

        $this->assertEquals([
            'x' => ['numeric', 'digits:11'],
            'y' => ['string', 'max:255'],
        ], $rules);
    }

    public function testRuleBuilderOnlyReturnsTheAnyRulesIfTheMethodIsTheAnyMethod(): void
    {
        $this->app->make('request')->setMethod(Methods::ANY);

        $rules =RulesBuilder::build()
                ->any([
                    'x' => ['numeric', 'digits:11'],
                    'y' => ['string', 'max:255'],
                ])
                ->post([
                    'z' => ['string'],
                ])
                ->rules();

        $this->assertEquals([
            'x' => ['numeric', 'digits:11'],
            'y' => ['string', 'max:255'],
        ], $rules);
    }

    public function testRuleBuilderCanReturnOtherRulesThanRequestMethod(): void
    {
        $this->app->make('request')->setMethod(Methods::DELETE);

        $rules =RulesBuilder::build()
                ->any([
                    'x' => ['numeric', 'digits:11'],
                    'y' => ['string', 'max:255'],
                ])
                ->post([
                    'z' => ['string'],
                ])
                ->get([
                    's' => ['string'],
                ])
                ->rules(Methods::GET);

        $this->assertEquals([
            'x' => ['numeric', 'digits:11'],
            'y' => ['string', 'max:255'],
            's' => ['string'],
        ], $rules);
    }

    public function testRuleBuilderCanReturnOtherRulesThanRequestMethodAndOverrideRuleManager(): void
    {
        $this->app->make('request')->setMethod(Methods::POST);

        $rules =RulesBuilder::build()
                ->any([
                    'x' => ['numeric', 'digits:11'],
                    'y' => ['string', 'max:255'],
                ])
                ->post([
                    'z' => 'string',
                ])
                ->get([
                    's' => 'string',
                ])
                ->rules(Methods::GET, true);

        $this->assertEquals([
            's' => ['string'],
        ], $rules);
    }

    public function testRuleBuilderCanBindOtherHttpMethodsToRule(): void
    {
        $this->app->make('request')->setMethod(Methods::POST);

        $builder = RulesBuilder::build()
                ->any([
                    'x' => ['numeric', 'digits:11'],
                    'y' => ['string', 'max:255'],
                ])
                ->post([
                    'z' => 'string',
                ], true, [Methods::DELETE])
                ->get([
                    's' => 'string',
                ]);

        $postRules = $builder->rules(Methods::POST);
        $deleteRules = $builder->rules(Methods::DELETE);

        $expected = [
            'x' => ['numeric', 'digits:11'],
            'y' => ['string', 'max:255'],
            'z' => ['string'],
        ];

        $this->assertEquals($expected, $postRules);
        $this->assertEquals($expected, $deleteRules);
    }

    public function testRuleBuilderCanMergeBoundRulesWithMethodRules(): void
    {
        $this->app->make('request')->setMethod(Methods::DELETE);

        $rules =RulesBuilder::build()
                ->any([
                    'x' => ['numeric', 'digits:11'],
                    'y' => ['string', 'max:255'],
                ])
                ->post([
                    'z' => ['string'],
                ], true, [Methods::DELETE])
                ->delete([
                    'x' => ['boolean'],
                    'z' => ['numeric'],
                    's' => ['string'],
                ])
                ->rules();

        $this->assertEquals([
            'x' => ['numeric', 'digits:11', 'boolean'],
            'y' => ['string', 'max:255'],
            'z' => ['string', 'numeric'],
            's' => ['string'],
        ], $rules);
    }
}
