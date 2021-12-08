<?php

namespace Marcohern\Xcvs\Tests;

use PHPUnit\Framework\TestCase;
use Marcohern\Xcvs\Xcvs;

class XcvsTest extends TestCase {

  protected function setUp(): void
  {
    parent::setUp();
  }

  protected function tearDown(): void
  {
    parent::tearDown();
  }

  public function test_load() {
    $xcvs = new Xcvs();
    $all = $xcvs->load("tests/Samples/file.csv");
    $this->assertEquals($all[0]['Name'], 'Marco Hernandez');
  }

  public function test_read() {
    $xcvs = new Xcvs();
    $xcvs->open("tests/Samples/file.csv");
    $cols = $xcvs->columns();
    $record = $xcvs->read();
    $this->assertEquals($cols[0], 'Name');
    $this->assertEquals($record['Name'], 'Marco Hernandez');
    $xcvs->close();
  }

  public function test_read_w_cols() {
    $xcvs = new Xcvs();
    $xcvs->open("tests/Samples/file.csv");
    $xcvs->read();
    $cols = $xcvs->columns();
    $record = $xcvs->read();
    $this->assertEquals($cols[0], 'Name');
    $this->assertEquals($record['Name'], 'Marco Hernandez');
    $xcvs->close();
  }
}