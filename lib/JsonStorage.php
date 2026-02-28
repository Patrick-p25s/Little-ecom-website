<?php

declare(strict_types=1);

final class JsonStorage
{
    public function __construct(private string $filePath)
    {
        $directory = dirname($this->filePath);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        if (!file_exists($this->filePath)) {
            file_put_contents($this->filePath, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $raw = file_get_contents($this->filePath);

        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $data = json_decode($raw, true);

        return is_array($data) ? array_values($data) : [];
    }

    /**
     * @param array<int, array<string, mixed>> $data
     */
    public function saveAll(array $data): void
    {
        $handle = fopen($this->filePath, 'c+');

        if ($handle === false) {
            throw new RuntimeException('Impossible d\'ouvrir le fichier: ' . $this->filePath);
        }

        if (!flock($handle, LOCK_EX)) {
            fclose($handle);
            throw new RuntimeException('Impossible de verrouiller le fichier: ' . $this->filePath);
        }

        ftruncate($handle, 0);
        rewind($handle);
        fwrite($handle, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
    }

    /**
     * @param array<string, mixed> $record
     */
    public function create(array $record): array
    {
        $data = $this->all();

        $record['id'] = $this->nextId($data);
        $record['created_at'] = date('Y-m-d H:i:s');

        $data[] = $record;
        $this->saveAll($data);

        return $record;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function find(int $id): ?array
    {
        foreach ($this->all() as $item) {
            if ((int)($item['id'] ?? 0) === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $record
     */
    public function update(int $id, array $record): bool
    {
        $data = $this->all();

        foreach ($data as $index => $item) {
            if ((int)($item['id'] ?? 0) === $id) {
                $record['id'] = $id;
                $record['created_at'] = $item['created_at'] ?? date('Y-m-d H:i:s');
                $record['updated_at'] = date('Y-m-d H:i:s');

                $data[$index] = $record;
                $this->saveAll($data);

                return true;
            }
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $filtered = array_values(array_filter(
            $this->all(),
            static fn(array $item): bool => (int)($item['id'] ?? 0) !== $id
        ));

        if (count($filtered) === count($this->all())) {
            return false;
        }

        $this->saveAll($filtered);

        return true;
    }

    /**
     * @param array<int, array<string, mixed>> $data
     */
    private function nextId(array $data): int
    {
        if ($data === []) {
            return 1;
        }

        $ids = array_map(static fn(array $item): int => (int)($item['id'] ?? 0), $data);

        return max($ids) + 1;
    }
}
