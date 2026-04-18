<footer style="
    padding: 1.25rem 1.5rem;
    margin-top: auto;
    border-top: 1px solid #e5e7eb;
    background: rgba(255,255,255,0.6);
    backdrop-filter: blur(8px);
    font-family: 'Outfit', sans-serif;
">
    <div style="max-width: 80rem; margin: 0 auto; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">

        {{-- Copyright --}}
        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.875rem; color: #6b7280; font-weight: 500;">
            <span style="display: inline-flex; padding: 6px; background: #f9fafb; border: 1px solid #f3f4f6; border-radius: 9999px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </span>
            <span>
                جميع الحقوق محفوظة &copy; {{ date('Y') }}
                <strong style="color: #111827; font-weight: 700;">SRPS Clinical</strong>
            </span>
        </div>

        {{-- Support Channels --}}
        <div style="display: flex; align-items: center; gap: 1.25rem;">

            {{-- Admin WhatsApp --}}
            @if(config('app.admin_whatsapp'))
            <a href="https://wa.me/{{ config('app.admin_whatsapp') }}" target="_blank" rel="noopener"
               style="display: flex; align-items: center; gap: 10px; text-decoration: none; transition: transform 0.15s;"
               onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="text-align: end;">
                    <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; font-weight: 700; line-height: 1;">الإدارة</div>
                    <div id="footer-admin-label" style="font-size: 0.875rem; color: #374151; font-weight: 700; transition: color 0.15s;"
                         onmouseover="this.style.color='#0891b2'" onmouseout="this.style.color='#374151'">الدعم العام</div>
                    <div dir="ltr" style="font-size: 0.75rem; color: #6b7280; font-family: monospace; line-height: 1; margin-top: 2px; text-align: right;">{{ config('app.admin_whatsapp') }}</div>
                </div>
                <div style="padding: 8px; background: #ecfdf5; color: #059669; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,.07); transition: all 0.15s; display:inline-flex;"
                     onmouseover="this.style.background='#059669';this.style.color='#fff'" onmouseout="this.style.background='#ecfdf5';this.style.color='#059669'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </a>
            @endif

            {{-- Divider --}}
            <div style="width: 1px; height: 2.5rem; background: #e5e7eb;"></div>

            {{-- Developer WhatsApp --}}
            @if(config('app.developer_whatsapp'))
            <a href="https://wa.me/{{ config('app.developer_whatsapp') }}" target="_blank" rel="noopener"
               style="display: flex; align-items: center; gap: 10px; text-decoration: none; transition: transform 0.15s;"
               onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
                <div style="text-align: end;">
                    <div style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; font-weight: 700; line-height: 1;">المبرمج</div>
                    <div style="font-size: 0.875rem; color: #374151; font-weight: 700; transition: color 0.15s;"
                         onmouseover="this.style.color='#0891b2'" onmouseout="this.style.color='#374151'">الدعم التقني</div>
                    <div dir="ltr" style="font-size: 0.75rem; color: #6b7280; font-family: monospace; line-height: 1; margin-top: 2px; text-align: right;">{{ config('app.developer_whatsapp') }}</div>
                </div>
                <div style="padding: 8px; background: #ecfeff; color: #0891b2; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,.07); transition: all 0.15s; display:inline-flex;"
                     onmouseover="this.style.background='#0891b2';this.style.color='#fff'" onmouseout="this.style.background='#ecfeff';this.style.color='#0891b2'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
            </a>
            @endif

        </div>
    </div>
</footer>
