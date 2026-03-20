<?php

namespace App\Services;

use Carbon\Carbon;

class RentalPaymentCalculator
{
    /**
     * Calcula la cantidad de cuotas y el monto por cuota según modalidad y fechas.
     * @param string $type semanal|quincenal|mensual
     * @param float $amount
     * @param string $from formato Y-m-d
     * @param string $to formato Y-m-d
     * @return array [cuotas, monto_por_cuota]
     */
    public static function calcularCuotas(string $type, float $amount, string $from, string $to): array
    {
        $start = Carbon::parse($from);
        $end = Carbon::parse($to);
        $diff = $start->diffInDays($end) + 1;
        
        switch ($type) {
            case 'semanal':
                $periodos = ceil($diff / 7);
                break;
            case 'quincenal':
                $periodos = ceil($diff / 15);
                break;
            case 'mensual':
                $periodos = ceil($diff / 30);
                break;
            default:
                $periodos = 1;
        }
        $montoPorCuota = $amount / $periodos;
        return [
            'cuotas' => $periodos,
            'monto_por_cuota' => round($montoPorCuota, 2),
        ];
    }
}
