<?php

namespace App;

trait CrudDb
{
    /**
     * Получить все записи (с пагинацией)
     */
    public static function getAll()
    {
        return self::query()->get(); // или ->paginate(15) для постраничного вывода
    }

    /**
     * Создать новую запись
     */
    public static function createItem(array $data)
    {
        return self::query()->create($data);
    }

    /**
     * Обновить запись
     */
    public static function updateItem($id, array $data)
    {
        $item = self::query()->findOrFail($id);
        $item->update($data);
        return $item;
    }

    /**
     * Удалить запись
     */
    public static function deleteItem($id)
    {
        $item = self::query()->findOrFail($id);
        return $item->delete();
    }
}

