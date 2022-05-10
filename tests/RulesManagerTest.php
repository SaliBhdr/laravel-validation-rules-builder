<?php

namespace SaliBhdr\ValidationRules\Tests;

use Mockery;
use Mockery\MockInterface;
use Illuminate\Http\Request;
use Mockery\LegacyMockInterface;
use Illuminate\Foundation\Http\FormRequest;
use SaliBhdr\ValidationRules\Methods;
use SaliBhdr\ValidationRules\Contracts\CacheContract;
use SaliBhdr\ValidationRules\Contracts\RulesBagContract;
use SaliBhdr\ValidationRules\RulesManager;

class RulesManagerTest extends TestCase
{
    /**
     * @var LegacyMockInterface|MockInterface|Request|null $requestMock
     */
    protected $requestMock;

    /**
     * @var LegacyMockInterface|MockInterface|FormRequest|null $formRequestMock
     */
    protected $formRequestMock;

    /**
     * @var LegacyMockInterface|MockInterface|RulesBagContract|null $rulesBagMock
     */
    protected $rulesBagMock;

    /**
     * @var LegacyMockInterface|MockInterface|CacheContract|null $cacheMock
     */
    protected $cacheMock;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->requestMock = Mockery::mock(Request::class);
        $this->formRequestMock = Mockery::mock(FormRequest::class);
        $this->rulesBagMock = Mockery::mock(RulesBagContract::class);
        $this->cacheMock = Mockery::mock(CacheContract::class);
    }

    /**
     * @return void
     */
    public function testRuleManagerCanBeInstantiated(): void
    {
        $this->rulesManagerInstance(Methods::POST);
    }

    /**
     * @return void
     */
    public function testRuleManagerShouldBuildRulesOfCreateOnPostRequest(): void
    {
        $method = Methods::POST;

        $anyRules = [
            "name"   => "string",
            "family" => "string",
            'role'   => 'string|max:255',
            "email"  => "email",
            "mobile" => ['numeric', "digits:11"],
        ];

        $methodRules = [
            "name"     => "required",
            "family"   => "required",
            "email"    => "required|unique:admins",
            "mobile"   => "required|unique:admins",
            'password' => 'required',
            'role'     => 'required',
        ];

        $this->healthyAssert($method, $anyRules, $methodRules);
        $this->rulesBagMock->shouldReceive('create')->once()->andReturnSelf();
        $this->rulesBagMock->shouldReceive('update')->once()->andReturnSelf();

        $rules = $this->rulesManagerInstance($method)
                      ->any($anyRules)
                      ->create($methodRules)
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

    /**
     * @return void
     */
    public function testRuleManagerShouldBuildRulesOfUpdateOnPatchRequest(): void
    {
        $method = Methods::PATCH;

        $anyRules = [
            "name"   => "string",
            "family" => "string",
            'role'   => 'string|max:255',
            "email"  => "email",
            "mobile" => ['numeric', "digits:11"],
        ];

        $methodRules = [
            "email"  => ['unique:admins,email,1'],
            "mobile" => ['unique:admins,mobile,1'],
        ];

        $this->healthyAssert($method, $anyRules, $methodRules);
        $this->rulesBagMock->shouldReceive('create')->once()->andReturnSelf();
        $this->rulesBagMock->shouldReceive('update')->once()->andReturnSelf();

        $rules = $this->rulesManagerInstance($method)
                      ->any($anyRules)
                      ->create([
                          "name"     => "required",
                          "family"   => "required",
                          "email"    => "required|unique:admins",
                          "mobile"   => "required|unique:admins",
                          'password' => 'required',
                          'role'     => 'required',
                      ])
                      ->update($methodRules)
                      ->rules();

        $this->assertEquals([
            "name"   => ["string"],
            "family" => ["string"],
            "role"   => ["string", "max:255"],
            "email"  => ["email", "unique:admins,email,1"],
            "mobile" => ["numeric", "digits:11", "unique:admins,mobile,1"],
        ], $rules);
    }

    /**
     * @return void
     */
    public function testRuleManagerBuildRulesOfUpdateOnPutRequest(): void
    {
        $method = Methods::PUT;

        $anyRules = [
            "name"   => "string",
            "family" => "string",
            'role'   => 'string|max:255',
            "email"  => "email",
            "mobile" => ['numeric', "digits:11"],
        ];

        $methodRules = [
            "email"  => ['unique:admins,email,1'],
            "mobile" => ['unique:admins,mobile,1'],
        ];

        $this->healthyAssert($method, $anyRules, $methodRules);
        $this->rulesBagMock->shouldReceive('create')->once()->andReturnSelf();
        $this->rulesBagMock->shouldReceive('update')->once()->andReturnSelf();

        $rules = $this->rulesManagerInstance($method)
                      ->any($anyRules)
                      ->create([
                          "name"     => "required",
                          "family"   => "required",
                          "email"    => "required|unique:admins",
                          "mobile"   => "required|unique:admins",
                          'password' => 'required',
                          'role'     => 'required',
                      ])
                      ->update($methodRules)
                      ->rules();

        $this->assertEquals([
            "name"   => ["string"],
            "family" => ["string"],
            "role"   => ["string", "max:255"],
            "email"  => ["email", "unique:admins,email,1"],
            "mobile" => ["numeric", "digits:11", "unique:admins,mobile,1"],
        ], $rules);
    }

    /**
     * @return void
     */
    public function testRuleManagerAddsMethodValidationIfTheValidationIsNotExistsInAnyMethod(): void
    {
        $method = Methods::POST;

        $anyRules = [
            'x' => 'numeric|digits:11',
            'y' => 'string|max:255',
        ];

        $methodRules = [
            'z' => 'string',
        ];

        $this->healthyAssert($method, $anyRules, $methodRules);
        $this->rulesBagMock->shouldReceive('post')->once()->andReturnSelf();

        $rules = $this->rulesManagerInstance($method)
                      ->any($anyRules)
                      ->post($methodRules)
                      ->rules();

        $this->assertEquals([
            'x' => ['numeric', 'digits:11'],
            'y' => ['string', 'max:255'],
            'z' => ['string'],
        ], $rules);
    }

    /**
     * @return void
     */
    public function testRuleManagerCanOverrideAnyRules(): void
    {
        $method = Methods::POST;

        $anyRules = [
            'x' => 'numeric|digits:11',
            'y' => 'string|max:255',
        ];

        $methodRules = [
            'z' => 'string',
        ];

        $this->healthyAssertWithOverride($method, $anyRules, $methodRules);
        $this->rulesBagMock->shouldReceive('post')->once()->andReturnSelf();

        $rules = $this->rulesManagerInstance($method)
                      ->any($anyRules)
                      ->post($methodRules, [], true)
                      ->rules();

        $this->assertEquals([
            'z' => ['string'],
        ], $rules);
    }

    /**
     * @return void
     */
    public function testRuleManagerOnlyReturnsOnlyAnyRulesIfTheMethodIsNotAllowed(): void
    {
        $rules = $this->rulesManagerInstance('not-allowed-method')
                      ->any([
                          'x' => 'numeric|digits:11',
                          'y' => 'string|max:255',
                      ])
                      ->post([
                          'z' => 'string',
                      ])
                      ->rules();

        $this->assertEquals([
            'x' => 'numeric|digits:11',
            'y' => 'string|max:255',
        ], $rules);
    }

    /**
     * @return void
     */
    public function testRuleManagerOnlyReturnsOnlyAnyRulesIfTheMethodIsAny(): void
    {
        $rules = $this->rulesManagerInstance(Methods::ANY)
                      ->any([
                          'x' => 'numeric|digits:11',
                          'y' => 'string|max:255',
                      ])
                      ->post([
                          'z' => 'string',
                      ])
                      ->rules();

        $this->assertEquals([
            'x' => 'numeric|digits:11',
            'y' => 'string|max:255',
        ], $rules);
    }

    /**
     * @return void
     */
    public function testRuleManagerCanReturnOtherRulesThanRequestMethod(): void
    {
        $rules = $this->rulesManagerInstance(Methods::DELETE)
                      ->any([
                          'x' => 'numeric|digits:11',
                          'y' => 'string|max:255',
                      ])
                      ->post([
                          'z' => 'string',
                      ])
                      ->get([
                          's' => 'string',
                      ])
                      ->rules(Methods::GET);

        $this->assertEquals([
            'x' => ['numeric', 'digits:11'],
            'y' => ['string', 'max:255'],
            's' => ['string'],
        ], $rules);
    }

    /**
     * @return void
     */
    public function testRuleManagerCanReturnOtherRulesThanRequestMethodAndOverrideRuleManager(): void
    {
        $rules = $this->rulesManagerInstance(Methods::POST)
                      ->any([
                          'x' => 'numeric|digits:11',
                          'y' => 'string|max:255',
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

    /**
     * @return void
     */
    public function testRuleManagerCanBindOtherHttpMethodsToRule(): void
    {
        $builder = $this->rulesManagerInstance(Methods::POST)
                        ->any([
                            'x' => 'numeric|digits:11',
                            'y' => 'string|max:255',
                        ])
                        ->post([
                            'z' => 'string',
                        ], [Methods::DELETE])
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

    /**
     * @return void
     */
    public function testRuleManagerCanMergeBoundRulesWithMethodRules(): void
    {
        $rules = $this->rulesManagerInstance(Methods::DELETE)
                      ->any([
                          'x' => 'numeric|digits:11',
                          'y' => 'string|max:255',
                      ])
                      ->post([
                          'z' => 'string',
                      ], [Methods::DELETE])
                      ->delete([
                          'x' => 'boolean',
                          'z' => 'numeric',
                          's' => 'string',
                      ])
                      ->rules();

        $this->assertEquals([
            'x' => ['numeric', 'digits:11', 'boolean'],
            'y' => ['string', 'max:255'],
            'z' => ['string', 'numeric'],
            's' => ['string'],
        ], $rules);
    }

    public function testBindWillReturnAnyRulesIfBindIsNotAValidMethod(): void
    {
        $bind = 'not-valid-bind';

        $method = Methods::POST;

        $anyRules = [
            'x' => 'numeric|digits:11',
            'y' => 'string|max:255',
        ];

        $methodRules = [
            'z' => 'string',
        ];

        $this->healthyAssert($method, $anyRules, $methodRules);

        $this->rulesBagMock->shouldReceive('post')->once()->andReturnSelf();
        $this->rulesBagMock->shouldReceive('get')->once()->andReturnSelf();
        $this->rulesBagMock->shouldReceive('isOverride')->with(strtoupper($bind))->once()->andReturnFalse();
        $this->rulesBagMock->shouldReceive('isRuleAllowed')->with(strtoupper($bind))->once()->andReturnFalse();

        $builder = $this->rulesManagerInstance($method, false)
                        ->any($anyRules)
                        ->post($methodRules, [$bind])
                        ->get([
                            's' => 'string',
                        ]);

        $this->assertEquals([
            'x' => 'numeric|digits:11',
            'y' => 'string|max:255',
        ], $builder->rules($bind));

        $this->assertEquals([], $builder->rules($bind, true));
    }

    /**
     * @param  string|null  $method
     * @param  bool  $isAllowed
     * @return RulesManager
     */
    protected function rulesManagerInstance(string $method = null, bool $isAllowed = true): RulesManager
    {
        if (!is_null($method)) {
            $this->mockRequestMethod($method);
            $this->mockRuleAllowed($method, $isAllowed);
        }

        return new RulesManager($this->requestMock, $this->rulesBagMock, $this->cacheMock);
    }

    protected function mockRequestMethod(string $method): void
    {
        $this->requestMock->shouldReceive('method')
                          ->andReturn($method);
    }

    protected function mockRuleAllowed(string $method, bool $isAllowed): void
    {
        $this->rulesBagMock->shouldReceive('isRuleAllowed')
                           ->with($method)
                           ->andReturn($isAllowed);
    }

    protected function healthyAssert(string $method, array $anyRules, array $methodRules): void
    {
        $this->rulesBagMock->shouldReceive('any')->once()->andReturnSelf();
        $this->rulesBagMock->shouldReceive('isOverride')->with($method)->once()->andReturnFalse();
        $this->cacheMock->shouldReceive('get')->once()->andReturnNull();
        $this->rulesBagMock->shouldReceive('getRule')->once()->with(Methods::ANY)->andReturn($anyRules);
        $this->rulesBagMock->shouldReceive('getRule')->once()->with($method)->andReturn($methodRules);
        $this->cacheMock->shouldReceive('put')->once()->andReturnTrue();
    }

    protected function healthyAssertWithOverride(string $method, array $anyRules, array $methodRules): void
    {
        $this->rulesBagMock->shouldReceive('any')->once()->andReturnSelf();
        $this->rulesBagMock->shouldReceive('isOverride')->with($method)->once()->andReturnTrue();
        $this->cacheMock->shouldReceive('get')->once()->andReturnNull();
        $this->rulesBagMock->shouldReceive('getRule')->once()->with(Methods::ANY)->andReturn($anyRules);
        $this->rulesBagMock->shouldReceive('getRule')->once()->with($method)->andReturn($methodRules);
        $this->cacheMock->shouldReceive('put')->once()->andReturnTrue();
    }
}
