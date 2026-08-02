<?php

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\Admin;

final class AdminPolicyPaginationTest extends CIUnitTestCase
{
    public function testPolicyPerPageNormalizationAllowsLargeLoads(): void
    {
        $controller = new class extends Admin {
            public function __construct()
            {
            }

            public function exposeNormalizePolicyPerPage($perPage): int
            {
                return $this->normalizePolicyPerPage($perPage);
            }
        };

        $this->assertSame(25, $controller->exposeNormalizePolicyPerPage(0));
        $this->assertSame(10000, $controller->exposeNormalizePolicyPerPage(15000));
        $this->assertSame(250, $controller->exposeNormalizePolicyPerPage(250));
    }
}
