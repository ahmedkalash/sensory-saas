<div style="margin-top: 40px; margin-bottom: 24px; font-family: sans-serif;">
    <!-- Separator -->
    <div style="display: flex; align-items: center; margin-bottom: 24px; position: relative;">
        <div style="flex-grow: 1; height: 1px; background: linear-gradient(to left, rgba(226, 232, 240, 0), rgba(226, 232, 240, 1));" class="dark:from-transparent dark:to-gray-700"></div>
        <span style="margin: 0 20px; color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;" class="dark:text-gray-400">أو سجل دخول بواسطة</span>
        <div style="flex-grow: 1; height: 1px; background: linear-gradient(to right, rgba(226, 232, 240, 0), rgba(226, 232, 240, 1));" class="dark:from-transparent dark:to-gray-700"></div>
    </div>

    <!-- Buttons Container -->
    <div style="display: flex; gap: 16px; width: 100%;">
        <!-- Google Button -->
        <a href="{{ route('auth.social.redirect', 'google') }}" 
           style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 12px; padding: 12px 16px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); text-decoration: none; color: #334155; font-size: 14px; font-weight: 600; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;"
           onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)'; this.style.borderColor='#cbd5e1';"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)'; this.style.borderColor='#e2e8f0';"
           class="dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.34-4.53z" fill="#EA4335"/>
            </svg>
            <span>Google</span>
        </a>

        <!-- Facebook Button -->
        <a href="{{ route('auth.social.redirect', 'facebook') }}" 
           style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 12px; padding: 12px 16px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); text-decoration: none; color: #334155; font-size: 14px; font-weight: 600; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer;"
           onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)'; this.style.borderColor='#cbd5e1';"
           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)'; this.style.borderColor='#e2e8f0';"
           class="dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
            <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="#1877F2" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            <span>Facebook</span>
        </a>
    </div>
</div>
