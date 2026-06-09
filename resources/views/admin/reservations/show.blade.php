@extends('layouts.admin')

@section('header', 'Reservation Details')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <a href="{{ route('admin.reservations.index') }}" class="text-salon-textLight hover:text-salon-text flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to list
    </a>
    <div class="flex space-x-3">
        <a href="{{ route('admin.reservations.pdf', $reservation) }}" class="bg-gray-800 hover:bg-gray-700 text-white py-2 px-4 rounded shadow-sm text-sm font-medium inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Download PDF
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-salon-gold mb-1">{{ $reservation->reservation_code }}</h2>
                    <p class="text-sm text-gray-500">Created: {{ $reservation->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    @php
                            $status = $reservation->status->value ?? $reservation->status;

    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
    ];
                    @endphp
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full uppercase {{ $statusColors[$reservation->status] }}">
                        {{ $reservation->status }}
                    </span>
                </div>
            </div>

            <div class="border-t border-salon-beige pt-6">
                <h3 class="text-lg font-bold text-salon-text mb-4">Services Booked</h3>
                <div class="border border-gray-200 rounded-md overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Duration</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $totalPrice = 0;
                                $totalDuration = 0;
                            @endphp
                            @foreach($reservation->reservationItems as $item)
                                @php
                                    $totalPrice += $item->service_price;
                                    $totalDuration += $item->service_duration;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-salon-text">{{ $item->service_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ $item->service_duration }} mins</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-salon-text text-right">Rp {{ number_format($item->service_price, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <th scope="row" class="px-6 py-4 text-right text-sm font-bold text-salon-text">Total</th>
                                <td class="px-6 py-4 text-center text-sm font-bold text-salon-text">{{ $totalDuration }} mins</td>
                                <td class="px-6 py-4 text-right text-lg font-bold text-salon-goldHover">Rp {{ number_format($totalPrice, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($reservation->notes)
            <div class="border-t border-salon-beige pt-6 mt-6">
                <h3 class="text-sm font-bold text-salon-text uppercase tracking-wider mb-2">Customer Notes</h3>
                <div class="bg-yellow-50 p-4 rounded text-gray-700 italic border border-yellow-100">
                    {{ $reservation->notes }}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <!-- Update Status Box -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-salon-text uppercase tracking-wider mb-4 border-b pb-2">Update Status</h3>
            <form action="{{ route('admin.reservations.updateStatus', $reservation) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-salon-gold focus:ring focus:ring-salon-beige" required>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
<option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
<option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>Completed</option>
<option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-salon-gold hover:bg-salon-goldHover text-white font-medium py-2 px-4 rounded shadow-sm">
                    Save Status
                </button>
            </form>
        </div>

        <!-- Schedule Box -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-salon-text uppercase tracking-wider mb-4 border-b pb-2">Schedule</h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Date</p>
                        <p class="font-medium text-salon-text">{{ $reservation->booking_date->format('l, d M Y') }}</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Time</p>
                        <p class="font-medium text-salon-text">{{ \Carbon\Carbon::parse($reservation->booking_time)->format('H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Box -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-bold text-salon-text uppercase tracking-wider mb-4 border-b pb-2">Customer Info</h3>
            <div class="space-y-3">
                <p><strong>Name:</strong> <br> {{ $reservation->customer_name }}</p>
                <p><strong>Email:</strong> <br> <a href="mailto:{{ $reservation->customer_email }}" class="text-blue-600 hover:underline">{{ $reservation->customer_email }}</a></p>
                <p><strong>Phone:</strong> <br>
                    @if($reservation->customer_phone)
                        <a href="tel:{{ $reservation->customer_phone }}" class="text-blue-600 hover:underline">{{ $reservation->customer_phone }}</a>
                    @else
                        <span class="text-gray-500">Not provided</span>
                    @endif
                </p>
            </div>
            <div class="mt-4 pt-4 border-t border-salon-beige">
                <a href="{{ route('admin.customers.show', $reservation->user_id) }}" class="text-salon-gold hover:text-salon-goldHover text-sm font-medium">View Customer Profile &rarr;</a>
            </div>
        </div>
    </div>
</div>
@endsection
