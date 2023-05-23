<?php

namespace SaliBhdr\ValidationRules\Tests\Unit;

use Illuminate\Http\Request;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use SaliBhdr\ValidationRules\Contracts\CacheContract;
use SaliBhdr\ValidationRules\Contracts\RulesBagContract;
use SaliBhdr\ValidationRules\Contracts\RulesManagerContract;
use SaliBhdr\ValidationRules\RulesManager;
use SaliBhdr\ValidationRules\Tests\TestCase;

class RulesManagerTest extends TestCase
{
    /**
     * @var LegacyMockInterface|MockInterface|Request|null
     */
    protected $requestMock;

    /**
     * @var LegacyMockInterface|MockInterface|RulesBagContract|null
     */
    protected $cacheableRulesBagMock;

    /**
     * @var LegacyMockInterface|MockInterface|RulesBagContract|null
     */
    protected $unCacheableRulesBagMock;

    /**
     * @var LegacyMockInterface|MockInterface|CacheContract|null
     */
    protected $cacheMock;

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
