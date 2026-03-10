@extends('errors.layout')

@section('code', '419')
@section('title', 'انتهت صلاحية الصفحة')
@section('message', 'انتهت صلاحية الجلسة الحالية. يرجى العودة والمحاولة مرة أخرى.')

@section('gradient', 'linear-gradient(135deg, #8b5cf6, #7c3aed)')
@section('icon-bg', 'rgba(139, 92, 246, 0.1)')
@section('icon-color', '#7c3aed')
@section('btn-bg', '#7c3aed')
@section('btn-shadow', 'rgba(124, 58, 237, 0.3)')

@section('icon')
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
</svg>
@endsection
