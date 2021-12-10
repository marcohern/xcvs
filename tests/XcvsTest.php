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

  public function test_extract_index() {
    $this->xcvs->setExtract([
      'Position' => [
        '/(\d+\.\d+)\s*,\s*(\d+\.\d+)/',
        1 => 'X', 2 => 'Y'
      ]
    ]);
    $this->xcvs->setFilePath('tests/Samples/extract.csv');
    $record = $this->xcvs->read();
    $this->assertEquals($record['Name'],'John');
    $this->assertEquals($record['Position'],'(123.456,789.012)');
    $this->assertEquals($record['X'],'123.456');
    $this->assertEquals($record['Y'],'789.012');
    $record = $this->xcvs->read();
    $this->assertEquals($record['Name'],'Mark');
    $this->assertEquals($record['Position'],' ( 98.765, 43.210 )');
    $this->assertEquals($record['X'],'98.765');
    $this->assertEquals($record['Y'],'43.210');
    $this->xcvs->close();
  }

  public function test_extract_named() {
    $this->xcvs->setExtract([
      'Position' => [
        '/(?<X>\d+\.\d+)\s*,\s*(?<Y>\d+\.\d+)/'
      ]
    ]);
    $this->xcvs->setFilePath('tests/Samples/extract.csv');
    $record = $this->xcvs->read();
    $this->assertEquals($record['Name'],'John');
    $this->assertEquals($record['Position'],'(123.456,789.012)');
    $this->assertEquals($record['X'],'123.456');
    $this->assertEquals($record['Y'],'789.012');
    $record = $this->xcvs->read();
    $this->assertEquals($record['Name'],'Mark');
    $this->assertEquals($record['Position'],' ( 98.765, 43.210 )');
    $this->assertEquals($record['X'],'98.765');
    $this->assertEquals($record['Y'],'43.210');
    $this->xcvs->close();
  }
}