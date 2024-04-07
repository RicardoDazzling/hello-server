<?php

namespace DazzRick\HelloServer\Services;

class EventsService
{
    protected static function getData(): array
    {
        $dir = dirname(__DIR__, 2) . '/static/json/events.json';
        $content = file_get_contents($dir);
        return json_decode($content, true);
    }

    protected static function setData(array $data): void
    {
        $dir = dirname(__DIR__, 2) . '/static/json/events.json';
        $new_content = json_encode($data);
        file_put_contents($dir, $new_content);
    }

    /**
     * @param string $uuid
     * @return void
     */
    public static function create(string $uuid): void
    {
        $data = self::getData();
        $data[$uuid] = [
            'writing' => false,
            'message' => false,
            'file' => false,
            'call' => false,
            'lost' => false
        ];
        self::setData($data);
    }

    /**
     * @param string $uuid
     * @param 'writing'|'message'|'file'|'call'|'lost' $mode
     * @return void
     */
    public static function update(string $uuid, string $mode='writing'): void
    {
        $data = self::getData();
        if (!in_array($uuid, $data)) $data[$uuid] = [];
        $data[$uuid][$mode] = true;
        self::setData($data);
    }

    /**
     * @param string $uuid
     * @param 'writing'|'message'|'file'|'call'|'lost' $mode
     * @return void
     */
    public static function delete(string $uuid, string $mode='writing'): void
    {
        $data = self::getData();
        if (!in_array($uuid, $data)) return;
        unset($data[$uuid][$mode]);
        self::setData($data);
    }
}