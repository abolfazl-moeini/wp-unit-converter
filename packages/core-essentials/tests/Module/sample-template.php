<?php

/**
 * @var \PHPUnit\Framework\MockObject\MockObject $mock
 */
$mock = \BetterStudio\Core\get_template_variable( 'mock' );

isset( $mock ) && $mock->fired();