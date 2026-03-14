<x-filament-widgets::widget>
    <div class="srps-hero-widget">
        {{-- Background Glow Effects --}}
        <div class="srps-hero-glow srps-hero-glow--tl"></div>
        <div class="srps-hero-glow srps-hero-glow--br"></div>
        <div class="srps-hero-glow srps-hero-glow--center"></div>

        {{-- Main Content --}}
        <div class="srps-hero-content">
            {{-- Brain SVG with Glow --}}
            <div class="srps-hero-brain-container">
                <div class="srps-hero-brain-glow"></div>
                <svg class="srps-hero-brain" viewBox="0 0 120 140" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {{-- Brain outline with neural pathways --}}
                    <g opacity="0.9">
                        {{-- Left hemisphere --}}
                        <path
                            d="M60 15C45 15 35 22 30 30C25 38 22 48 22 58C20 62 18 68 18 75C18 82 22 88 28 92C30 98 35 105 42 110C48 114 54 116 60 116"
                            stroke="url(#brainGrad1)" stroke-width="2" fill="none" stroke-linecap="round" />
                        {{-- Right hemisphere --}}
                        <path
                            d="M60 15C75 15 85 22 90 30C95 38 98 48 98 58C100 62 102 68 102 75C102 82 98 88 92 92C90 98 85 105 78 110C72 114 66 116 60 116"
                            stroke="url(#brainGrad1)" stroke-width="2" fill="none" stroke-linecap="round" />
                        {{-- Central fissure --}}
                        <path d="M60 18 L60 113" stroke="rgba(255,255,255,0.2)" stroke-width="0.5"
                            stroke-dasharray="3 3" />
                        {{-- Gyri details left --}}
                        <path d="M35 35C40 32 48 28 55 30" stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"
                            stroke-linecap="round" />
                        <path d="M28 50C35 45 45 42 55 45" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"
                            fill="none" stroke-linecap="round" />
                        <path d="M25 68C32 62 42 58 55 60" stroke="rgba(255,255,255,0.3)" stroke-width="1.2" fill="none"
                            stroke-linecap="round" />
                        <path d="M30 85C38 78 48 75 55 78" stroke="rgba(255,255,255,0.25)" stroke-width="1.2"
                            fill="none" stroke-linecap="round" />
                        {{-- Gyri details right --}}
                        <path d="M85 35C80 32 72 28 65 30" stroke="rgba(255,255,255,0.4)" stroke-width="1.2" fill="none"
                            stroke-linecap="round" />
                        <path d="M92 50C85 45 75 42 65 45" stroke="rgba(255,255,255,0.35)" stroke-width="1.2"
                            fill="none" stroke-linecap="round" />
                        <path d="M95 68C88 62 78 58 65 60" stroke="rgba(255,255,255,0.3)" stroke-width="1.2" fill="none"
                            stroke-linecap="round" />
                        <path d="M90 85C82 78 72 75 65 78" stroke="rgba(255,255,255,0.25)" stroke-width="1.2"
                            fill="none" stroke-linecap="round" />
                        {{-- Brain stem --}}
                        <path d="M55 116 Q55 125 52 132 M65 116 Q65 125 68 132 M60 116 L60 135"
                            stroke="rgba(255,255,255,0.3)" stroke-width="1.5" fill="none" stroke-linecap="round" />
                    </g>
                    {{-- Neural sparkle dots --}}
                    <circle cx="40" cy="40" r="2" fill="rgba(34,211,238,0.8)" class="srps-sparkle srps-sparkle--1" />
                    <circle cx="80" cy="40" r="2" fill="rgba(129,140,248,0.8)" class="srps-sparkle srps-sparkle--2" />
                    <circle cx="35" cy="70" r="1.5" fill="rgba(52,211,153,0.7)" class="srps-sparkle srps-sparkle--3" />
                    <circle cx="85" cy="70" r="1.5" fill="rgba(251,191,36,0.7)" class="srps-sparkle srps-sparkle--4" />
                    <circle cx="60" cy="55" r="2.5" fill="rgba(255,255,255,0.9)" class="srps-sparkle srps-sparkle--5" />
                    <circle cx="45" cy="90" r="1.5" fill="rgba(34,211,238,0.6)" class="srps-sparkle srps-sparkle--6" />
                    <circle cx="75" cy="90" r="1.5" fill="rgba(129,140,248,0.6)" class="srps-sparkle srps-sparkle--7" />
                    <defs>
                        <linearGradient id="brainGrad1" x1="18" y1="15" x2="102" y2="135"
                            gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#22d3ee" />
                            <stop offset="50%" stop-color="#ffffff" />
                            <stop offset="100%" stop-color="#818cf8" />
                        </linearGradient>
                    </defs>
                </svg>
            </div>

            {{-- Title --}}
            <h1 class="srps-hero-title">
                قائمة انماط الاستجابة الحسية                
<span class="srps-hero-title-eng">(SRP)</span>
            </h1>
            <p class="srps-hero-subtitle">اللوحة الرئيسية للأداة الإكلينيكية</p>

            {{-- Sensory Category Pills — All 7 Scales --}}
            <div class="srps-hero-pills">
                {{-- 1. Visual بصري --}}
                <div class="srps-hero-pill" style="--pill-color: #22d3ee;">
                    <svg class="srps-hero-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>بصري</span>
                </div>
                {{-- 2. Auditory سمعي --}}
                <div class="srps-hero-pill" style="--pill-color: #818cf8;">
                    <svg class="srps-hero-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <path d="M9 18V5l12-2v13" stroke-linecap="round" stroke-linejoin="round" />
                        <circle cx="6" cy="18" r="3" />
                        <circle cx="18" cy="16" r="3" />
                    </svg>
                    <span>سمعي</span>
                </div>
                {{-- 3. Tactile لمسي --}}
                <div class="srps-hero-pill" style="--pill-color: #34d399;">
                    <svg class="srps-hero-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <path
                            d="M18 11V6a2 2 0 00-2-2 2 2 0 00-2 2v0M14 10V4a2 2 0 00-2-2 2 2 0 00-2 2v2M10 10.5V6a2 2 0 00-2-2 2 2 0 00-2 2v8"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M18 8a2 2 0 012 2v7.5a5 5 0 01-5 5h-2.6a5 5 0 01-3.54-1.46L5 17" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span>لمسي</span>
                </div>
                {{-- 4. Vestibular دهليزي --}}
                <div class="srps-hero-pill" style="--pill-color: #f472b6;">
                    <svg class="srps-hero-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <circle cx="12" cy="12" r="10" stroke-linecap="round" />
                        <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M2 12h20" stroke-linecap="round" />
                    </svg>
                    <span>دهليزي</span>
                </div>
                {{-- 5. Proprioceptive حس عضلي --}}
                <div class="srps-hero-pill" style="--pill-color: #fb923c;">
                    <svg class="srps-hero-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <path d="M18 20V6.5a2.5 2.5 0 00-5 0V6a2 2 0 00-4 0v0.5a2.5 2.5 0 00-5 0V20"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4 15h16" stroke-linecap="round" />
                        <circle cx="8" cy="10" r="1" fill="currentColor" />
                        <circle cx="16" cy="10" r="1" fill="currentColor" />
                        <circle cx="12" cy="7" r="1" fill="currentColor" />
                    </svg>
                    <span>حس عضلي</span>
                </div>
                {{-- 6. Olfactory شمي --}}
                <div class="srps-hero-pill" style="--pill-color: #a78bfa;">
                    <svg class="srps-hero-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <path d="M8 18c0 2 1 4 4 4s4-2 4-4" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M16 14c2-1 4-3.5 4-6a8 8 0 00-16 0c0 2.5 2 5 4 6" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M10 14h4" stroke-linecap="round" />
                        <path d="M9 3c.5 2 1 3 3 3s2.5-1 3-3" stroke-linecap="round" stroke-linejoin="round"
                            opacity="0.5" />
                    </svg>
                    <span>شمي</span>
                </div>
                {{-- 7. Gustatory تذوقي --}}
                <div class="srps-hero-pill" style="--pill-color: #fbbf24;">
                    <svg class="srps-hero-pill-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.5">
                        <path d="M12 22c-4 0-7-2-7-5 0-2 1-3 3-4 1-.5 2-1.5 2-3V2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M12 22c4 0 7-2 7-5 0-2-1-3-3-4-1-.5-2-1.5-2-3V2" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M8 2h8" stroke-linecap="round" />
                        <path d="M9 7h6" stroke-linecap="round" opacity="0.5" />
                    </svg>
                    <span>تذوقي</span>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>