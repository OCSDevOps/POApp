<?php

namespace Tests\Feature\Supplier;

use Tests\TestCase;

class QuoteSubmissionTest extends TestCase
{
    /** @test */
    public function placeholder_until_rfq_schema_is_seeded()
    {
        $this->markTestSkipped('RFQ tables rely on legacy schema; enable when migrations and seeds for items/suppliers/projects exist.');
    }
}
