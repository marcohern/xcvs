<?php

namespace Marcohern\Xcvs;

use Marcohern\Xcvs\Exceptions\CsvOpenException;
use Marcohern\Xcvs\Exceptions\CsvCloseException;

class XcvsFileReader {
  private $handle;
  public $columns;

  public function open(string $filepath): void
  {
    $this->columns = null;
    $this->handle = fopen($filepath, "r");
    if ($this->handle === false) throw new CsvOpenException("Unable to open file '$filepath'.");
  }

  public function close(): void {
    if ($this->handle === null) throw new CsvCloseException("Handle is null, cannot be closed.");
    $closeSuccess = fclose($this->handle);
    if ($closeSuccess === false) throw new CsvCloseException("Unable to close file.");
  }

  public function read(): array|bool {
    $record = fgetcsv($this->handle, 0, ',', '"', '\\');
    if (!$record) return false;
    if (is_null($this->columns)) {
      $this->columns = $record;
      return $record;
    }
    $item = [];
    foreach ($this->columns as $i => $name) {
      $item[$name] = $record[$i];
    }
    return $item;
  }

  public function columns() {
    if (is_null($this->columns)) $this->columns = $this->read();
    return $this->columns;
  }

  public function load(string $filepath): array|bool {
    $this->open($filepath);
    $this->columns = $this->read();
    $data = [];
    while ($record = $this->read()) {
      $data[] = $record;
    }    
    $this->close();
    return $data;
  }
}
