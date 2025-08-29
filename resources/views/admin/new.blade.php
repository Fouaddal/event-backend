@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
        {{ __('Admin Dashboard') }}
    </h2>
@endsection

@section('content')
<div class="py-10">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        <!-- Top Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-blue-500 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm text-gray-500">Total Providers</h2>
                    <i class="fas fa-user text-blue-500"></i>
                </div>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalProviders }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-green-500 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm text-gray-500">Total Companies</h2>
                    <i class="fas fa-building text-green-500"></i>
                </div>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalCompanies }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-purple-500 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm text-gray-500">Total Events</h2>
                    <i class="fas fa-calendar text-purple-500"></i>
                </div>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalEvents }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 border-t-4 border-yellow-500 hover:shadow-lg transition">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm text-gray-500">Total Offers</h2>
                    <i class="fas fa-gift text-yellow-500"></i>
                </div>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalOffers }}</p>
            </div>
        </div>

        <!-- Users & Providers Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Users -->
            <div class="bg-white shadow rounded-2xl p-6 hover:shadow-lg transition">
                <h3 class="text-gray-500 text-sm">All Users</h3>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
            </div>

            <!-- Providers vs Companies -->
            <div class="bg-white shadow rounded-2xl p-6 hover:shadow-lg transition">
                <h3 class="text-gray-500 text-sm">Providers Breakdown</h3>
                <div class="mt-4 space-y-4">

                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">Individuals</span>
                            <span class="font-medium">{{ $providerPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: '{{ $providerPercentage }}%'"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-700">Companies</span>
                            <span class="font-medium">{{ $companyPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full" style="width: '{{ $companyPercentage }}%'"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Events Section -->
        <div class="bg-white shadow rounded-2xl p-6 hover:shadow-lg transition mt-8">
            <h3 class="text-gray-500 text-sm mb-4">Recent Events</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">#</th>
                        <th class="p-2 border">Event Name</th>
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">Location</th>
                          <th class="p-2 border">Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 border">{{ $loop->iteration }}</td>
                            <td class="p-2 border">{{ $event->name }}</td>
                            <td class="p-2 border">{{ \Carbon\Carbon::parse($event->date)->format('d M, Y') }}</td>
                            <td class="p-2 border">{{ $event->location }}</td>
                             <td class="p-2 border">{{ $event->user ? $event->user->name : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-2 border text-center text-gray-500">No events found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Offers Section -->
        <div class="bg-white shadow rounded-2xl p-6 hover:shadow-lg transition mt-8">
            <h3 class="text-gray-500 text-sm mb-4">Recent Offers</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">#</th>
                        <th class="p-2 border">description</th>
                        <th class="p-2 border">price</th>
                        <th class="p-2 border">date</th>
                         <th class="p-2 border">Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($offers as $offer)
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 border">{{ $loop->iteration }}</td>
                            <td class="p-2 border">{{ $offer->description }}</td>
                            <td class="p-2 border">{{ $offer->price }}</td>
                            <td class="p-2 border">{{ \Carbon\Carbon::parse($offer->valid_until)->format('d M, Y') }}</td>
                             <td class="p-2 border">{{ $offer->user ? $offer->user->name : 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-2 border text-center text-gray-500">No offers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
