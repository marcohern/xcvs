<?php

namespace Marcohern\Xcvs\Tests;

use PHPUnit\Framework\TestCase;
use Marcohern\Xcvs\Xcvs;
use Marcohern\Xcvs\Exceptions\CsvOpenException;
use Marcohern\Xcvs\Exceptions\CsvCloseException;

class XcvsTest extends TestCase {

  protected $xcvs;

  protected function setUp(): void
  {
    parent::setUp();
    $this->xcvs = new Xcvs();
  }

  protected function tearDown(): void
  {
    parent::tearDown();
    $this->xcvs = null;
  }

  public function test_load() {
    $all = $this->xcvs->load("tests/Samples/file.csv");
    $this->assertEquals($all[0]['Name'], 'Marco Hernandez');
  }

  public function test_open_warning() {
    $this->expectWarning();
    $all = $this->xcvs->open("tests/Samples/nonexistent.csv");
  }

  public function test_close_exception() {
    $this->expectException(CsvCloseException::class);
    $all = $this->xcvs->close();
  }

  public function test_read() {
    $this->xcvs->open("tests/Samples/worldcities.csv");
    $cols = $this->xcvs->columns();
    $record = $this->xcvs->read();
    $this->assertEquals($cols[0], 'city');
    $this->assertEquals($record['city'], 'Tokyo');
    $this->xcvs->close();
  }

  public function test_read_w_cols() {
    $this->xcvs->open("tests/Samples/worldcities.csv");
    $this->xcvs->read();
    $cols = $this->xcvs->columns();
    $record = $this->xcvs->read();
    $this->assertEquals($cols[0], 'city');
    $this->assertEquals($record['city'], 'Tokyo');
    $this->xcvs->close();
  }
}