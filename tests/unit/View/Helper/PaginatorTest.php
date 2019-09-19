<?php

use Bone\View\Helper\Exception\PaginatorException;
use Bone\View\Helper\Paginator;
use Codeception\TestCase\Test;

class PaginatorTest extends Test
{
    /**
     * @throws Exception
     */
    public function testCreatePager()
    {
        $pager = new Paginator();
        $pager->setCurrentPage(2);
        $pager->setPageCount(5);
        $pager->setPagerSize(4);
        $pager->setUrl('https://bottlesofrum.com/cannons/:page');
        $pager->setUrlPart(':page');
        $this->assertEquals('', $pager->render());
    }
    /**
     * @throws Exception
     */
    public function testCreatePagerThrowsException()
    {
        $this->expectException(PaginatorException::class);
        $pager = new Paginator();
        $pager->render();
    }
}