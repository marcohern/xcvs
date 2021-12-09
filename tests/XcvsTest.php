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

  public function test_setColumns() {
    $this->xcvs->setColumns([
      'Id' => '/^id$/',
      'Name' => '/^city$/',
      'Country' => '/^country$/'
    ]);
    $cols = $this->xcvs->getColumns();
    $this->assertEquals($cols, [
      'Id' => '/^id$/',
      'Name' => '/^city$/',
      'Country' => '/^country$/'
    ]);
  }

  public function test_setFilePath() {
    $this->xcvs->setFilePath('filepath.csv');
    $this->assertEquals('filepath.csv', $this->xcvs->getFilePath());
  }

  public function test_read() {
    $this->xcvs->setColumns([
      'Id' => '/^id/',
      'Name' => '/^city/',
      'Country' => '/^country/'
    ]);
    $this->xcvs->setFilePath('tests/Samples/worldcities.csv');
    $record = $this->xcvs->read();
    $this->assertEquals($record['Id'],'1392685764');
    $this->assertEquals($record['Name'],'Tokyo');
    $this->assertEquals($record['Country'],'Japan');
    
  }
}