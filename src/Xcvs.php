<?php

namespace Marcohern\Xcvs;

use Marcohern\Xcvs\Exceptions\CsvOpenException;
use Marcohern\Xcvs\Exceptions\CsvCloseException;

class Xcvs {
  private $reader = null;
  private $columns = null;
  private $filepath = null;
  private $extract = null;

  private $columnMap = null;

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

  public function setExtract(array $extract): void
  {
    $this->extract = $extract;
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

  private function extract(array &$record): void
  {
    foreach ($this->extract as $col => $extract)
    {
      $exp = $extract[0];
      $m = null;
      preg_match($exp, $record[$col], $m);
      foreach ($extract as $expi => $to) {
        if ($expi === 0) continue;
        $record[$to] = $m[$expi];
      }
      foreach ($m as $i => $v) {
        if (is_string($i)) $record[$i] = $v;
      }
    }
  }

  public function read(): array
  {
    if (is_null($this->columnMap))
    {
      $this->readFirst();
    }
    $mapped = $this->map($this->reader->read());
    if (!is_null($this->extract)) $this->extract($mapped);
    return $mapped;
  }

  public function close(): void
  {
    $this->reader->close();
  }
}
