<?php

namespace Marcohern\Xcvs;

use Marcohern\Xcvs\Exceptions\CsvOpenException;
use Marcohern\Xcvs\Exceptions\CsvCloseException;

class Xcvs {
  private $reader = null;
  private $columns = null;
  private $filepath = null;

  private $columnMap;

  public function __construct()
  {
    $this->reader = new XcvsFileReader();
  }

  public function setColumns(array $columns): void
  {
    $this->columns = $columns;
  }

  public function getColumns(): ?array
  {
    return $this->columns;
  }

  public function setFilePath(string $filepath): void
  {
    $this->filepath = $filepath;
  }

  public function getFilePath(): ?string
  {
    return $this->filepath;
  }

  private function defaultSourceColumns(array &$sourceColumns): array
  {
    $columnMap = [];
    foreach ($sourceColumns as $column) {
      $columnMap[$column] = $column;
    }
    return $columnMap;
  }

  private function generateColumnMap(array &$sourceColumns): array
  {
    $columnMap = [];
    foreach ($this->columns as $col => $exp) {
      foreach ($sourceColumns as $scol) {
        if (preg_match($exp, $scol) === 1) {
          $columnMap[$col] = $scol;
          break;
        }
      }
      //$columnMap[$col] = null;
    }
    return $columnMap;
  }

  private function readFirst(): void
  {
    $this->reader->open($this->filepath);
    $sourceColumns = $this->reader->columns();
    $this->columnMap = (is_null($this->columns))
      ? $this->defaultSourceColumns($sourceColumns)
      : $this->generateColumnMap($sourceColumns);
  }

  private function map(array $record): array
  {
    $map = [];
    foreach ($this->columnMap as $target => $source) {
      if (!is_null($source)) {
        $map[$target] = $record[$source];
      }
    }
    
    return $map;
  }

  public function read(): array
  {
    $this->reader = new XcvsFileReader();
    
    if (is_null($this->columnMap))
    {
      $this->readFirst();
    }
    return $this->map($this->reader->read());
  }

  public function close(): void
  {
    $this->reader->close();
  }
}
