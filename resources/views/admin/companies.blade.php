@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('messages.pending_companies') }}
    </h2>
@endsection

@section('content')
<div class="py-12" @if(app()->getLocale() == 'ar') dir="rtl" @endif>
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
            @if ($requests->isEmpty())
                <p class="text-gray-500 text-center">{{ __('messages.no_company_requests') }}</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.name') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.email') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.type') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.event_types') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($requests as $request)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $request->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ ucfirst($request->provider_type) }}
                                    </span>
                                </td>
                              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
    @php
        $specializations = is_string($request->specializations) ? json_decode($request->specializations, true) : $request->specializations;
    @endphp

    @if(is_array($specializations))
        {{ implode(', ', $specializations) }}
    @else
        {{ $request->specializations }}
    @endif
</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                    <div class="flex justify-end space-x-2">
                                        <form action="{{ route('admin.approve', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to approve this request?');">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded">
                                                ✅ {{ __('messages.approve') }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.reject', $request->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this request?');">
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
