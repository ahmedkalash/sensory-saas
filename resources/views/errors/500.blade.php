@extends('errors.layout')

@section('code', '500')
@section('title', 'خطأ في النظام')
@section('message', 'حدث خطأ غير متوقع في النظام. يرجى المحاولة مرة أخرى، وإذا استمرت المشكلة تواصل مع الدعم الفني.')

@section('gradient', 'linear-gradient(135deg, #ef4444, #dc2626)')
@section('icon-bg', 'rgba(239, 68, 68, 0.1)')
@section('icon-color', '#dc2626')
@section('btn-bg', '#dc2626')
@section('btn-shadow', 'rgba(220, 38, 38, 0.3)')

@section('tips')
<strong>لديك مشكلة في الصلاحيات؟</strong> هذه المشكلة طبيعية أحياناً عند أول تشغيل للنظام بسبب عمليات الحماية في الويندوز. <br>
سحر بسيط: <strong>جرب تحديث الصفحة (Reload)</strong>، وغالباً سيقوم النظام بتجاوز المشكلة والعمل بشكل صحيح.
@endsection

@section('extra-buttons')
<form action="/clear-cache-recovery" method="POST" style="display: contents;">
    @csrf
    <button type="submit" class="btn btn-outline" style="background: #fff7ed; border-color: #fed7aa; color: #ea580c;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
        </svg>
        مسح التخزين المؤقت
    </button>
</form>

<a href="javascript:location.reload()" class="btn btn-outline" style="background: #fef2f2; border-color: #fca5a5; color: #dc2626;">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
    </svg>
    تحديث الصفحة
</a>
@endsection

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
</svg>
@endsection
