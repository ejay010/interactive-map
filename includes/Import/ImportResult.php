<?php

namespace Ekelly\InteractiveMap\Import;

class ImportResult
{
    private int $processed = 0;
    private int $imported = 0;
    private int $skipped = 0;
    private array $missing = [];

    public function processed(): void
    {
        $this->processed++;
    }

    public function imported(): void
    {
        $this->imported++;
    }

    public function skipped(): void
    {
        $this->skipped++;
    }

    public function missing(string $plant): void
    {
        $this->missing[] = $plant;
    }

    public function toArray(): array
    {
        return [
            'processed' => $this->processed,
            'imported' => $this->imported,
            'skipped' => $this->skipped,
            'missing' => $this->missing,
        ];
    }
}