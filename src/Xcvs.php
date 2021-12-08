<?php

namespace Marcohern\Xcvs;

class Xcvs {
  private $handle;

  public $columns = null;

  public function open(string $filepath): void
  {
    $this->columns = null;
    $this->handle = fopen($filepath, "r");
  }

  public function close(): void {
    fclose($this->handle);
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
