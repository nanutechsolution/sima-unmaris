@extends('frontend.layouts.public')

@section('title', $survey->title.' — '.$appSettings->getInstitutionName())
@section('meta_description', $survey->description ?: 'Survei fasilitas '.$appSettings->getInstitutionName().'.')

@section('content')
    <div class="relative overflow-hidden bg-brand-950 text-white">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute -left-24 top-0 h-64 w-64 rounded-full bg-brand-600/30 blur-3xl"></div>
            <div class="absolute -right-16 bottom-0 h-72 w-72 rounded-full bg-accent-500/10 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-3xl px-4 pb-16 pt-10 sm:px-6 sm:pb-20 sm:pt-14 lg:px-8">
            <nav class="flex items-center gap-2 text-xs font-semibold text-brand-300" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a>
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-white" aria-current="page">Survei Fasilitas</span>
            </nav>

            <span class="mt-5 inline-flex items-center gap-1.5 rounded-full border border-emerald-400/30 bg-emerald-400/10 px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.16em] text-emerald-300">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span>
                Survei aktif
            </span>

            <h1 class="mt-4 text-2xl font-extrabold leading-tight tracking-tight sm:text-3xl lg:text-4xl">{{ $survey->title }}</h1>
            @if($survey->description)
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-brand-100/80 sm:text-base">{{ $survey->description }}</p>
            @endif
            <p class="mt-4 inline-flex items-center gap-2 text-xs font-semibold text-brand-200/80">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Pendapat Anda membantu peningkatan fasilitas kampus
            </p>
        </div>
    </div>

    <div class="mx-auto -mt-10 max-w-3xl px-4 pb-12 sm:-mt-12 sm:px-6 lg:px-8">
        @include('frontend.partials.survey-form', ['survey' => $survey, 'alreadySubmitted' => $alreadySubmitted ?? false])
    </div>
@endsection
