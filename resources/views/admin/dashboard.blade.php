@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('messages.pending_providers') }}
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

        @php
            $isArabic = app()->getLocale() == 'ar';
            $align = $isArabic ? 'text-right' : 'text-left';
            $paddingX = $isArabic ? 'px-2' : 'px-6';
        @endphp

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
            @if ($requests->isEmpty())
                <p class="text-gray-500 text-center">{{ __('messages.no_provider_requests') }}</p>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="{{ $paddingX }} py-3 {{ $align }} text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.name') }}
                            </th>
                            <th class="{{ $paddingX }} py-3 {{ $align }} text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.email') }}
                            </th>
                            <th class="{{ $paddingX }} py-3 {{ $align }} text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.type') }}
                            </th>
                            <th class="{{ $paddingX }} py-3 {{ $align }} text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.services') }}
                            </th>
                            <th class="{{ $paddingX }} py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('messages.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($requests as $request)
                            <tr>
                                <td class="{{ $paddingX }} py-4 whitespace-nowrap text-sm text-gray-900 {{ $align }}">
                                    {{ $request->name }}
                                </td>
                                <td class="{{ $paddingX }} py-4 whitespace-nowrap text-sm text-gray-600 {{ $align }}">
                                    {{ $request->email }}
                                </td>
                                <td class="{{ $paddingX }} py-4 whitespace-nowrap text-sm {{ $align }}">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ ucfirst($request->provider_type) }}
                                    </span>
                                </td>
                                <td class="{{ $paddingX }} py-4 whitespace-nowrap text-sm text-gray-700 {{ $align }}">
                                    @php
                                        $services = is_string($request->services) ? json_decode($request->services, true) : $request->services;
                                    @endphp

                                    @if(is_array($services))
                                        {{ implode(', ', $services) }}
                                    @else
                                        {{ $request->services }}
                                    @endif
                                </td>
                               <td class="{{ $paddingX }} py-4 whitespace-nowrap text-sm text-left">
    <div class="flex gap-1 justify-start" dir="ltr">
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
