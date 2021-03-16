@php
use App\Models\Slot;
use App\Models\User;
use App\Models\Subject;
@endphp

@extends('layouts.app')

@section('content')
<div class="flex items-end justify-center px-4 pb-20 text-center sm:block sm:p-0">
    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>
    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" x-data>
        <form id="cancelSlotForm" novalidate>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Cancel this session
                        </h3>
                        <div class="mt-2">
                            <div class="mb-3">
                                <input type="datetime-local" class="bg-white outline-none font-bold text-xl" name="start" value="{{ app('request')->input('start') }}" disabled>
                            </div>
                            @if (Auth::user()->role->name == 'tutor')
                            <div class="flex items-center mb-2">
                                <svg class="h-7 w-7 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span name="student_name">{{ User::find(Slot::find(app('request')->input('id'))->student_id)->name }}</span><span>&nbsp;is learning&nbsp;</span><span name="subject_name">{{ Subject::find(Slot::find(app('request')->input('id'))->subject_id)->name }}</span>
                            </div>
                            <div class="flex items-center mb-3">
                                <svg class="h-7 w-7 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                                <span name="student_email">{{ User::find(Slot::find(app('request')->input('id'))->student_id)->email }}</span>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-flat" name="info" disabled>{{ Slot::find(app('request')->input('id'))->info }}</textarea>
                                <label>What do they need help with?</label>
                            </div>
                            @elseif (Auth::user()->role->name == 'student')
                            <div class="flex items-center mb-2">
                                <svg class="h-7 w-7 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span name="tutor_name">{{ User::find(Slot::find(app('request')->input('id'))->tutor_id)->name }}</span><span>&nbsp;is tutoring&nbsp;</span><span name="subject_name">{{ Subject::find(Slot::find(app('request')->input('id'))->subject_id)->name }}</span>
                            </div>
                            <div class="flex items-center mb-3">
                                <svg class="h-7 w-7 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                                <span name="tutor_email">{{ User::find(Slot::find(app('request')->input('id'))->tutor_id)->email }}</span>
                            </div>
                            <div class="form-floating mb-3">
                                <textarea class="form-flat" name="tutor_bio" disabled>{{ User::find(Slot::find(app('request')->input('id'))->tutor_id)->tutor->bio }}</textarea>
                                <label>Tutor bio</label>
                            </div>
                            @endif
                            <div class="form-floating">
                                <textarea class="form-element" name="reason" required></textarea>
                                <label>Cancellation reason</label>
                            </div>
                            <div class="hidden" name="id" data-id="{{ app('request')->input('id') }}"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" class="btn-negative btn-modal sm:ml-3">
                    Cancel
                </button>
                <a class="mt-3 btn-neutral btn-modal sm:mt-0" href="{{ route('dashboard') }}">
                    Back to dashboard
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ mix('js/cancel.js') }}"></script>
@endsection