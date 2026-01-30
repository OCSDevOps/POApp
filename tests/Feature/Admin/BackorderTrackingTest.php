<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class BackorderTrackingTest extends TestCase
{
    /** @test */
    public function placeholder_until_po_schema_migrations_available()
    {
        $this->markTestSkipped('Legacy purchase_order_details table lacks factory/migration; enable after schema seeds exist.');
    }
}
