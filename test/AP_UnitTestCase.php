<?php

// https://core.trac.wordpress.org/browser/trunk/tests/phpunit/includes/testcase.php

abstract class AP_UnitTestCase extends WP_UnitTestCase
{
    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    public function test_true_is_true() {
        $this->assertTrue(true);
    }
}
