<?php

namespace Tests\Feature\Supplier;

use Tests\TestCase;

class ItemPricingTest extends TestCase
{
    /** @test */
    public function placeholder_until_legacy_tables_are_migrated()
    {
        $this->markTestSkipped('Item/Supplier legacy tables lack migrations; enable after schema is available.');
    }
}
