@extends('layout.layout')

@section('content')
    <x-page-header homeUrl="#" title="Reports" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <form action="{{ route('view.report.monthly') }}" method="GET" class="flex gap-4">
                            <select name="month" class="rounded-md border-gray-300">
                                @foreach ($months as $availableMonth)
                                    <option value="{{ $availableMonth }}"
                                        {{ $availableMonth == $currentMonth ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::parse($availableMonth)->format('F Y') }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600">
                                Generate Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
