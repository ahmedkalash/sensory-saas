@extends('errors.layout')

@section('code', '403')
@section('title', 'غير مصرح بالوصول')
@section('message', 'ليس لديك الصلاحية اللازمة للوصول إلى هذه الصفحة. تواصل مع المسؤول إذا كنت تعتقد أن هذا خطأ.')

@section('gradient', 'linear-gradient(135deg, #f59e0b, #d97706)')
@section('icon-bg', 'rgba(245, 158, 11, 0.1)')
@section('icon-color', '#d97706')
@section('btn-bg', '#d97706')
@section('btn-shadow', 'rgba(217, 119, 6, 0.3)')

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/>
</svg>
@endsection
