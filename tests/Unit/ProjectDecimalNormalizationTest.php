<?php

namespace Tests\Unit;

use App\Models\Project;
use Tests\TestCase;

class ProjectDecimalNormalizationTest extends TestCase
{
    public function test_normalize_decimal_value_accepts_dot_decimal_input(): void
    {
        $this->assertSame(20.11, Project::normalizeDecimalValue('20.11'));
    }

    public function test_normalize_decimal_value_accepts_comma_decimal_input(): void
    {
        $this->assertSame(20.11, Project::normalizeDecimalValue('20,11'));
    }
}
