<div class="fi-plan-expired-container relative">
    <div class="max-w-md w-full mx-auto bg-white dark:bg-gray-900 rounded-3xl shadow-xl border border-gray-100 dark:border-white/10 overflow-hidden relative">
        
        {{-- Top Accent Glow --}}
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-red-500 via-orange-500 to-red-500"></div>
        
        <div class="p-8 flex flex-col items-center text-center">
            
            {{-- Icon Container --}}
            <div class="relative mb-8 pt-4">
                <div class="absolute inset-0 bg-red-500/10 blur-2xl rounded-full"></div>
                <div class="relative flex items-center justify-center w-24 h-24 rounded-3xl bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 transform -rotate-3 hover:rotate-0 transition-transform duration-500 shadow-inner">
                    <svg class="w-12 h-12" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-3 tracking-tight font-outfit">
                {{ static::getTitle() }}
            </h1>

            {{-- Dynamic reason --}}
            <p class="text-gray-600 dark:text-gray-400 text-lg leading-relaxed mb-8 font-arabic">
                {{ $this->getReasonText() }}
            </p>

            {{-- Current plan details card --}}
            @if ($this->subscription)
                <div class="w-full mb-8 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-200/50 dark:border-white/10 p-5 text-sm text-right">
                    <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-200 dark:border-white/10">
                        <span class="text-gray-500 dark:text-gray-400 font-medium">الخطة الحالية</span>
                        <span class="px-3 py-1 rounded-full bg-red-100 dark:bg-red-400/10 text-red-600 dark:text-red-400 font-bold border border-red-200 dark:border-red-400/20">
                            {{ $this->subscription->plan->name }}
                        </span>
                    </div>
                    
                    <div class="space-y-3">
                        @if ($this->subscription->ends_at)
                            <div class="flex justify-between items-center text-gray-700 dark:text-gray-300">
                                <span>تاريخ الانتهاء</span>
                                <span class="font-mono font-medium tracking-tight">{{ $this->subscription->ends_at->format('Y-m-d') }}</span>
                            </div>
                        @endif
                        
                        @if ($this->subscription->quota_remaining !== null)
                            <div class="flex justify-between items-center text-gray-700 dark:text-gray-300">
                                <span>التقييمات المتبقية</span>
                                <span class="badge px-2 py-0.5 rounded-md bg-gray-200 dark:bg-gray-700 font-bold tracking-tighter">{{ $this->subscription->quota_remaining }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex flex-col w-full gap-4">
                <a
                    href="mailto:{{ config('app.admin_email', 'admin@sensory.app') }}"
                    class="w-full py-4 px-6 rounded-2xl bg-primary-600 text-white font-bold hover:bg-primary-700 transition-all duration-300 shadow-lg shadow-primary-600/20 active:scale-[0.98] flex items-center justify-center gap-3"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    تواصل مع المسؤول
                </a>

                <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="w-full py-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors font-medium flex items-center justify-center gap-2 group">
                        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
        
        {{-- Footer Branding --}}
        <div class="p-6 bg-gray-50/50 dark:bg-black/20 border-t border-gray-100 dark:border-white/5 text-center">
            <span class="text-xs text-gray-400 dark:text-gray-600 uppercase tracking-widest font-semibold">SRPS Clinical System</span>
        </div>
    </div>

    <style>
        .font-arabic { font-family: 'Noto Kufi Arabic', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        {{-- Force background match if needed --}}
        body.fi-simple-layout {
            background: linear-gradient(135deg, #f0f9ff 0%, #ede9fe 50%, #f0fdfa 100%) !important;
        }
    </style>
</div>
