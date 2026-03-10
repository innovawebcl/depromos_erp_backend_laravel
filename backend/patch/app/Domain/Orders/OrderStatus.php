<?php

namespace App\Domain\Orders;

/**
 * Enum que define los estados válidos de un pedido.
 * Centraliza la lógica de transiciones para evitar estados inválidos.
 */
enum OrderStatus: string
{
    case Pending = 'pending';
    case Picking = 'picking';
    case Ready = 'ready';
    case EnRoute = 'en_route';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    /**
     * Retorna los estados a los que se puede transicionar desde el estado actual.
     *
     * @return OrderStatus[]
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Picking, self::Cancelled],
            self::Picking => [self::Ready, self::Cancelled],
            self::Ready => [self::EnRoute, self::Cancelled],
            self::EnRoute => [self::Delivered, self::Cancelled],
            self::Delivered => [],
            self::Cancelled => [],
        };
    }

    /**
     * Verifica si se puede transicionar al estado destino.
     */
    public function canTransitionTo(OrderStatus $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * Retorna una etiqueta legible para el estado.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Picking => 'En Picking',
            self::Ready => 'Listo para Despacho',
            self::EnRoute => 'En Ruta',
            self::Delivered => 'Entregado',
            self::Cancelled => 'Cancelado',
        };
    }

    /**
     * Retorna todos los valores como array (útil para validaciones).
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
