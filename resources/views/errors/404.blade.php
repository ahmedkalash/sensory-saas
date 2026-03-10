@extends('errors.layout')

@section('code', '404')
@section('title', 'الصفحة غير موجودة')
@section('message', 'عذراً، الصفحة التي تبحث عنها غير موجودة أو تم نقلها إلى عنوان آخر.')

@section('gradient', 'linear-gradient(135deg, #06b6d4, #0891b2)')
@section('icon-bg', 'rgba(6, 182, 212, 0.1)')
@section('icon-color', '#0891b2')
@section('btn-bg', '#0891b2')
@section('btn-shadow', 'rgba(8, 145, 178, 0.3)')

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607ZM10.5 7.5v6m3-3h-6"/>
</svg>
@endsection
