<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Recycle Center') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-start mb-4">
                        <a class="me-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150" href="{{ route('center.create') }}" >
                            {{ __('Add Recycle Center') }}
                        </a>
                    </div>

                    <table class="border-collapse border border-slate-500 w-full">
                        <thead>
                            <tr>
                                <th class="border border-slate-500 p-2">Name</th>
                                <th class="border border-slate-500 p-2">Services</th>
                                <th class="border border-slate-500 p-2">Address</th>
                                <th class="border border-slate-500 p-2">Type</th>
                                <th class="border border-slate-500 p-2">Operational Hour</th>
                                <th class="border border-slate-500 p-2">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($recyclingCenters as $recyclingCenter)
                            <tr>
                                <td class="border border-slate-500 p-2">{{ $recyclingCenter->name }}</td>
                                <td class="border border-slate-500 p-2">
                                    <ul style="list-style-type: disc; padding-left: 20px;">
                                        @php
                                            $services = json_decode($recyclingCenter->services)->services;
                                        @endphp
                                        @foreach ($services as $service)
                                        <li>{{ $service }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="border border-slate-500 p-2">{{ $recyclingCenter->address }}</td>
                                <td class="border border-slate-500 p-2">{{ $recyclingCenter->is_dropbox ? "Dropbox" : "Premise" }}</td>
                                <td class="border border-slate-500 p-2">{{ $recyclingCenter->operation_hour }}</td>
                                <td class="border border-slate-500 p-2">
                                    {{-- <x-nav-link class="bg-blue-500 text-white px-4 py-1 rounded" :href="route('center.show', 1)">View</x-nav-link> --}}

                                    <x-nav-link class="bg-orange-500 text-white px-4 py-1 rounded" :href="route('center.edit', $recyclingCenter->id)">Edit</x-nav-link>

                                    <form id="del_{{ $recyclingCenter->id }}" method="POST" action="{{ route('center.destroy', $recyclingCenter->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 text-white px-2 py-0.5 rounded" form="del_{{ $recyclingCenter->id }}">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
