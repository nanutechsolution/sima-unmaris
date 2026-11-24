@php
    $settings = app(\App\Services\ApplicationSettings::class);
    $brandName = $settings->getAppName();
    $logoUrl = $dark ? $settings->getLogoDarkUrl() : $settings->getLogoUrl();
@endphp

{{-- Filament hanya merender brandLogo ATAU brandName. Partial ini menggabungkan
     keduanya agar logo dan nama aplikasi tampil berdampingan di panel admin. --}}
<span
    class="fi-admin-brand"
    style="display: flex; align-items: center; gap: 0.625rem; height: 100%; min-width: 0;"
>
    <img
        src="{{ $logoUrl }}"
        alt="Logo {{ $brandName }}"
        style="height: 100%; width: auto; max-width: 2.5rem; flex: none; object-fit: contain;"
    >

    <span
        class="fi-admin-brand-name"
        style="min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
    >
        {{ $brandName }}
    </span>
</span>
