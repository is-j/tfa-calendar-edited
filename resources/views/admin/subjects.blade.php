@extends('layouts.app')

@section('content')
<div class="relative flex w-full flex-wrap items-stretch mb-3">
    <span class="z-10 h-full leading-snug font-normal absolute text-center text-gray-400 absolute bg-transparent rounded text-base items-center justify-center w-8 pl-3" style="padding-top:14px;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
        </svg>
    </span>
    <input type="text" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded-lg text-base border border-gray-400 outline-none w-full h-12" id="searchInput" style="padding-left:2.7rem;" placeholder="Search subjects..." autocomplete="off" autofocus>
</div>
<div class="flex flex-col container-table">
    <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
            <div class="shadow border-b border-gray-200 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Id
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subject
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tableBody">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="flex w-full my-3" id="createSubject">
        <div class="w-full mr-3">
            <input type="text" class="w-full px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded-lg text-base border border-gray-400 outline-none h-12" style="padding-right:2.7rem;" placeholder="Create subject...">
            <div class="text-red-700 text-base ml-3 mt-3 opacity-0 hidden transition-all duration-500 ease-in-out" id="subjectError">
                <span>The subject already exists.</span>
            </div>
        </div>
        <button type="button" class="text-white bg-gray-700 hover:bg-gray-600 hover:text-gray-200 rounded-lg focus:outline-none select-none w-12 h-12">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ mix('js/admin/config.js') }}"></script>
<script src="{{ mix('js/admin/subjects.js') }}"></script>
@endsection