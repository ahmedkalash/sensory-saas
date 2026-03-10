@extends('errors.layout')

@section('code', '500')
@section('title', 'خطأ في النظام')
@section('message', 'حدث خطأ غير متوقع في النظام. يرجى المحاولة مرة أخرى، وإذا استمرت المشكلة تواصل مع الدعم الفني.')

@section('gradient', 'linear-gradient(135deg, #ef4444, #dc2626)')
@section('icon-bg', 'rgba(239, 68, 68, 0.1)')
@section('icon-color', '#dc2626')
@section('btn-bg', '#dc2626')
@section('btn-shadow', 'rgba(220, 38, 38, 0.3)')

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
</svg>
@endsection
