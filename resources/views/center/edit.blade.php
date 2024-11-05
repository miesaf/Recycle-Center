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
                    <h1>Update Information</h1>
                    <br/>

                    <form method="POST" action="{{ route('center.update', $recyclingCenter->id) }}">
                        @csrf
                        @method("PUT")

                        <!-- Name -->
                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" value="{{ $recyclingCenter->name }}" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Services -->
                        <div class="mt-4">
                            <x-input-label for="services" :value="__('Services')" />
                            {{-- <x-text-input id="services" class="block mt-1 w-full" type="text" name="services" :value="old('services')" required /> --}}

                            <input type="checkbox" id="service1" name="services[]" value="Paper" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"  >
                            <label for="service1"> Paper</label><br>

                            <input type="checkbox" id="service2" name="services[]" value="Metal" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <label for="service2"> Metal</label><br>

                            <input type="checkbox" id="service3" name="services[]" value="Fabric" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <label for="service3"> Fabric</label><br>

                            <input type="checkbox" id="service4" name="services[]" value="Glass" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <label for="service3"> Glass</label><br>

                            <x-input-error :messages="$errors->get('services')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div class="mt-4">
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea id="address" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" rows="5" name="address" required >{{ $recyclingCenter->address }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- Premise Type -->
                        <div class="mt-4">
                            <x-input-label for="type" :value="__('Premise Type')" />

                            <select id="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" name="is_dropbox" required >
                                <option>Please select</option>
                                <option></option>
                                <option value="0" {{ $recyclingCenter->is_dropbox ? null : "selected" }} >Premise</option>
                                <option value="1" {{ $recyclingCenter->is_dropbox ? "selected" : null }} >Dropbox</option>
                            </select>

                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <!-- Operational Hour -->
                        <div class="mt-4">
                            <x-input-label for="operation_hour" :value="__('Operational Hour')" />
                            <x-text-input id="operation_hour" class="block mt-1 w-full" type="text" name="operation_hour" value="{{ $recyclingCenter->operation_hour }}" required />
                            <x-input-error :messages="$errors->get('operation_hour')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-4">
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
