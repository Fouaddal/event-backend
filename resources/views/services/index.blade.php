@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('messages.pending_services') }}
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded shadow">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded shadow">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            @if ($services->isEmpty())
                <p class="text-gray-500 text-center">{{ __('messages.no_service_requests') }}</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('messages.name') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('messages.type') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('messages.price') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('messages.description') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                {{ __('messages.provider') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                {{ __('messages.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($services as $service)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $service->type }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">${{ number_format($service->price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $service->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $service->provider->name ?? 'N/A' }} (ID: {{ $service->provider_id }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end space-x-2">
                                        <form action="{{ route('admin.services.approve', $service->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded">
                                                ✅ {{ __('messages.approve') }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.services.reject', $service->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded">
                                                ❌ {{ __('messages.reject') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
