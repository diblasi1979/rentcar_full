<div style="font-family: Arial, sans-serif;">
    <h2 style="color: #2d3748;">Comprobante de Pago</h2>
    <p><strong>Fecha:</strong> {{ $payment->payment_date }}</p>
    <p><strong>Conductor:</strong> {{ $driver->name }}</p>
    <p><strong>Vehículo:</strong> {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->plate }})</p>
    <p><strong>Monto:</strong> ${{ number_format($payment->amount, 2, ',', '.') }}</p>
    <p><strong>KM Reportado:</strong> {{ $payment->km_reported }}</p>
    <hr>
    <p style="font-size: 0.9em; color: #718096;">Gracias por su pago. RentCar</p>
</div>
