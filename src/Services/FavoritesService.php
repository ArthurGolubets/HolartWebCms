<?php

namespace HolartWeb\HolartCMS\Services;

use Illuminate\Support\Facades\Session;

class FavoritesService
{
    private const SESSION_KEY = 'holart_favorites';

    /**
     * Добавить товар в избранное
     */
    public function addToFavorites(int $productId): bool
    {
        $favorites = $this->getFavorites();

        if (!in_array($productId, $favorites)) {
            $favorites[] = $productId;
            $this->saveFavorites($favorites);
            return true;
        }

        return false;
    }

    /**
     * Удалить товар из избранного
     */
    public function removeFromFavorites(int $productId): bool
    {
        $favorites = $this->getFavorites();
        $key = array_search($productId, $favorites);

        if ($key !== false) {
            unset($favorites[$key]);
            $favorites = array_values($favorites); // Переиндексация массива
            $this->saveFavorites($favorites);
            return true;
        }

        return false;
    }

    /**
     * Очистить избранное
     */
    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    /**
     * Получить список избранных товаров
     */
    public function getFavorites(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    /**
     * Получить количество товаров в избранном
     */
    public function getCount(): int
    {
        return count($this->getFavorites());
    }

    /**
     * Проверить, есть ли товар в избранном
     */
    public function hasFavorite(int $productId): bool
    {
        $favorites = $this->getFavorites();
        return in_array($productId, $favorites);
    }

    /**
     * Переключить статус избранного (добавить/удалить)
     */
    public function toggleFavorite(int $productId): bool
    {
        if ($this->hasFavorite($productId)) {
            $this->removeFromFavorites($productId);
            return false; // Удалено из избранного
        } else {
            $this->addToFavorites($productId);
            return true; // Добавлено в избранное
        }
    }

    /**
     * Сохранить избранное в сессию
     */
    private function saveFavorites(array $favorites): void
    {
        Session::put(self::SESSION_KEY, $favorites);
    }
}
