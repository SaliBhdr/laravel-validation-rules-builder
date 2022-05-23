<?php

namespace SaliBhdr\ValidationRules\Tests\Unit;

use Mockery;
use Mockery\MockInterface;
use Illuminate\Http\Request;
use Mockery\LegacyMockInterface;
use SaliBhdr\ValidationRules\RulesManager;
use SaliBhdr\ValidationRules\Tests\TestCase;
use SaliBhdr\ValidationRules\Contracts\CacheContract;
use SaliBhdr\ValidationRules\Contracts\RulesBagContract;
use SaliBhdr\ValidationRules\Contracts\RulesManagerContract;

class RulesManagerTest extends TestCase
{
    /**
     * @var LegacyMockInterface|MockInterface|Request|null $requestMock
     */
    protected $requestMock;

    /**
     * @var LegacyMockInterface|MockInterface|RulesBagContract|null $cacheableRulesBagMock
     */
    protected $cacheableRulesBagMock;

    /**
     * @var LegacyMockInterface|MockInterface|RulesBagContract|null $unCacheableRulesBagMock
     */
    protected $unCacheableRulesBagMock;

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
        $this->requestMock             = Mockery::mock(Request::class);
        $this->cacheableRulesBagMock   = Mockery::mock(RulesBagContract::class);
        $this->unCacheableRulesBagMock = Mockery::mock(RulesBagContract::class);
        $this->cacheMock               = Mockery::mock(CacheContract::class);
    }

    public function rulesManagerInstance(): RulesManagerContract
    {
        return new RulesManager(
            $this->requestMock,
            $this->cacheableRulesBagMock,
            $this->unCacheableRulesBagMock,
            $this->cacheMock
        );
    }
}
