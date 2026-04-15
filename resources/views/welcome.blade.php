<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */
                @layer theme{
                    :root,:host{
                        --font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
                        --font-serif:ui-serif,Georgia,Cambria,"Times New Roman",Times,serif;
                        --font-mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
                        
                        /* Sage Green Color Palette */
                        --color-sage-50:#f4f7f4;
                        --color-sage-100:#e8ede8;
                        --color-sage-200:#d1dbd1;
                        --color-sage-300:#b2c4b2;
                        --color-sage-400:#9caf88;
                        --color-sage-500:#8a9a8b;
                        --color-sage-600:#6b7f6c;
                        --color-sage-700:#556655;
                        --color-sage-800:#424d42;
                        --color-sage-900:#363d36;
                        --color-sage-950:#242824;
                        
                        /* Neutral & Base */
                        --color-white:#fff;
                        --color-black:#000;
                        --color-gray-50:#f9fafb;
                        --color-gray-100:#f3f4f6;
                        --color-gray-600:#4b5563;
                        --color-gray-700:#374151;
                        --color-gray-900:#111827;
                        
                        /* Spacing & Layout */
                        --spacing:.25rem;
                        --breakpoint-sm:40rem;
                        --breakpoint-md:48rem;
                        --breakpoint-lg:64rem;
                        --breakpoint-xl:80rem;
                        --breakpoint-2xl:96rem;
                        
                        /* Typography */
                        --text-sm:.875rem;
                        --text-sm--line-height:calc(1.25/.875);
                        --text-base:1rem;
                        --text-base--line-height:1.5;
                        --text-lg:1.125rem;
                        --text-lg--line-height:calc(1.75/1.125);
                        
                        /* Font Weight */
                        --font-weight-medium:500;
                        --font-weight-semibold:600;
                        --font-weight-bold:700;
                        
                        /* Border Radius */
                        --radius-sm:.25rem;
                        --radius-md:.375rem;
                        --radius-lg:.5rem;
                        --radius-xl:.75rem;
                        
                        /* Shadows */
                        --shadow-sm:0 1px 3px 0 #0000001a,0 1px 2px -1px #0000001a;
                        --shadow-md:0 4px 6px -1px #0000001a,0 2px 4px -2px #0000001a;
                        --shadow-lg:0 10px 15px -3px #0000001a,0 4px 6px -4px #0000001a;
                        
                        /* Transitions */
                        --default-transition-duration:.15s;
                        --default-transition-timing-function:cubic-bezier(.4,0,.2,1);
                    }
                }
                @layer base{
                    *,::after,::before{box-sizing:border-box;border:0 solid;margin:0;padding:0}
                    html,:host{-webkit-text-size-adjust:100%;tab-size:4;line-height:1.5;font-family:var(--font-sans)}
                    body{line-height:inherit;background-color:var(--color-white);color:var(--color-gray-900)}
                    a{color:inherit;text-decoration:none}
                    img,svg,video{display:block;max-width:100%;height:auto}
                    button,input,select,textarea{font:inherit;color:inherit}
                }
                @layer utilities{
                    .flex{display:flex}
                    .hidden{display:none}
                    .items-center{align-items:center}
                    .justify-center{justify-content:center}
                    .justify-end{justify-content:flex-end}
                    .flex-col{flex-direction:column}
                    .gap-3{gap:calc(var(--spacing)*3)}
                    .gap-4{gap:calc(var(--spacing)*4)}
                    .min-h-screen{min-height:100vh}
                    .w-full{width:100%}
                    .max-w-\[335px\]{max-width:335px}
                    .max-w-4xl{max-width:var(--container-4xl,56rem)}
                    .p-6{padding:calc(var(--spacing)*6)}
                    .px-5{padding-inline:calc(var(--spacing)*5)}
                    .py-1\.5{padding-block:calc(var(--spacing)*1.5)}
                    .py-2{padding-block:calc(var(--spacing)*2)}
                    .pb-12{padding-bottom:calc(var(--spacing)*12)}
                    .mb-1{margin-bottom:calc(var(--spacing)*1)}
                    .mb-2{margin-bottom:calc(var(--spacing)*2)}
                    .mb-4{margin-bottom:calc(var(--spacing)*4)}
                    .mb-6{margin-bottom:calc(var(--spacing)*6)}
                    .rounded-sm{border-radius:var(--radius-sm)}
                    .rounded-lg{border-radius:var(--radius-lg)}
                    .rounded-t-lg{border-top-left-radius:var(--radius-lg);border-top-right-radius:var(--radius-lg)}
                    .rounded-bl-lg{border-bottom-left-radius:var(--radius-lg)}
                    .rounded-br-lg{border-bottom-right-radius:var(--radius-lg)}
                    .border{border-style:solid;border-width:1px}
                    .bg-white{background-color:var(--color-white)}
                    .bg-sage-50{background-color:var(--color-sage-50)}
                    .bg-sage-500{background-color:var(--color-sage-500)}
                    .bg-sage-600{background-color:var(--color-sage-600)}
                    .text-sage-50{color:var(--color-sage-50)}
                    .text-sage-600{color:var(--color-sage-600)}
                    .text-sage-700{color:var(--color-sage-700)}
                    .text-gray-600{color:var(--color-gray-600)}
                    .text-gray-700{color:var(--color-gray-700)}
                    .text-white{color:var(--color-white)}
                    .font-medium{font-weight:var(--font-weight-medium)}
                    .text-sm{font-size:var(--text-sm);line-height:var(--text-sm--line-height)}
                    .text-\[13px\]{font-size:13px}
                    .leading-\[20px\]{line-height:20px}
                    .shadow-sm{box-shadow:var(--shadow-sm)}
                    .shadow-md{box-shadow:var(--shadow-md)}
                    .shadow-inner{box-shadow:inset 0 2px 4px 0 #0000000d}
                    .transition-all{transition-property:all;transition-duration:var(--default-transition-duration);transition-timing-function:var(--default-transition-timing-function)}
                    .hover\:bg-sage-600:hover{background-color:var(--color-sage-600)}
                    .hover\:bg-sage-700:hover{background-color:var(--color-sage-700)}
                    .hover\:border-sage-600:hover{border-color:var(--color-sage-600)}
                    .hover\:text-sage-700:hover{color:var(--color-sage-700)}
                    .focus\:outline-none:focus{outline:2px solid transparent;outline-offset:2px}
                    .focus\:ring-2:focus{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}
                    .focus\:ring-sage-500:focus{--tw-ring-color:var(--color-sage-500)}
                    .underline{text-decoration-line:underline}
                    .underline-offset-4{text-underline-offset:4px}
                }
                @media (min-width: 64rem){
                    .lg\:flex-row{flex-direction:row}
                    .lg\:p-8{padding:calc(var(--spacing)*8)}
                    .lg\:p-20{padding:calc(var(--spacing)*20)}
                    .lg\:mb-0{margin-bottom:0}
                    .lg\:mb-6{margin-bottom:calc(var(--spacing)*6)}
                    .lg\:grow{flex-grow:1}
                    .lg\:w-\[438px\]{width:438px}
                    .lg\:max-w-4xl{max-width:var(--container-4xl,56rem)}
                    .lg\:rounded-tl-lg{border-top-left-radius:var(--radius-lg)}
                    .lg\:rounded-r-lg{border-top-right-radius:var(--radius-lg);border-bottom-right-radius:var(--radius-lg)}
                    .lg\:rounded-t-none{border-top-left-radius:0;border-top-right-radius:0}
                    .lg\:rounded-br-none{border-bottom-right-radius:0}
                    .lg\:block{display:block}
                    .lg\:ml-0{margin-left:0}
                    .lg\:-ml-px{margin-left:-1px}
                    .lg\:-mt-\[6\.6rem\]{margin-top:-6.6rem}
                    .lg\:justify-center{justify-content:center}
                }
                @media (prefers-color-scheme: dark){
                    .dark\:bg-gray-900{background-color:var(--color-gray-900)}
                    .dark\:bg-sage-800{background-color:var(--color-sage-800)}
                    .dark\:bg-sage-900{background-color:var(--color-sage-900)}
                    .dark\:text-sage-50{color:var(--color-sage-50)}
                    .dark\:text-gray-300{color:#d1d5db}
                    .dark\:border-sage-700{border-color:var(--color-sage-700)}
                    .dark\:shadow-inner{box-shadow:inset 0 2px 4px 0 #ffffff1a}
                    .dark\:hover\:bg-sage-700:hover{background-color:var(--color-sage-700)}
                    .dark\:hover\:border-sage-500:hover{border-color:var(--color-sage-500)}
                }
            </style>
        @endif
    </head>
    
    <!-- Body: White background, sage accents -->
    <body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-sage-50 flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        
        <!-- Header / Nav Bar dengan Sage Green -->
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" 
                           class="inline-block px-5 py-1.5 bg-sage-500 hover:bg-sage-600 text-white rounded-sm text-sm font-medium transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-500">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="inline-block px-5 py-1.5 border border-sage-500 text-sage-600 dark:text-sage-50 hover:bg-sage-50 dark:hover:bg-sage-900 rounded-sm text-sm font-medium transition-all focus:outline-none focus:ring-2 focus:ring-sage-500">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="inline-block px-5 py-1.5 bg-sage-500 hover:bg-sage-600 text-white rounded-sm text-sm font-medium transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-500">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>
        
        <!-- Main Content Area -->
        <div class="flex items-center justify-center w-full transition-all lg:grow">
            <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row">
                
                <!-- Content Panel: White Background -->
                <div class="text-[13px] leading-[20px] flex-1 p-6 pb-12 lg:p-20 bg-white dark:bg-gray-900 dark:text-sage-50 shadow-inner rounded-bl-lg rounded-br-lg lg:rounded-tl-lg lg:rounded-br-none border border-sage-100 dark:border-sage-800">
                    <h1 class="mb-1 font-medium text-gray-900 dark:text-sage-50">Let's get started</h1>
                    <p class="mb-2 text-gray-600 dark:text-gray-300">Laravel has an incredibly rich ecosystem. <br>We suggest starting with the following.</p>
                    
                    <ul class="flex flex-col mb-4 lg:mb-6">
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-sage-200 dark:before:border-sage-700 before:top-1/2 before:bottom-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-gray-900">
                                <span class="flex items-center justify-center rounded-full bg-sage-50 dark:bg-sage-900 shadow-sm w-3.5 h-3.5 border border-sage-200 dark:border-sage-700">
                                    <span class="rounded-full bg-sage-500 dark:bg-sage-400 w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Read the
                                <a href="https://laravel.com/docs" target="_blank" class="inline-flex items-center gap-1 font-medium underline underline-offset-4 text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 transition-colors ml-1">
                                    <span>Documentation</span>
                                    <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5">
                                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                                    </svg>
                                </a>
                            </span>
                        </li>
                        <li class="flex items-center gap-4 py-2 relative before:border-l before:border-sage-200 dark:before:border-sage-700 before:bottom-1/2 before:top-0 before:left-[0.4rem] before:absolute">
                            <span class="relative py-1 bg-white dark:bg-gray-900">
                                <span class="flex items-center justify-center rounded-full bg-sage-50 dark:bg-sage-900 shadow-sm w-3.5 h-3.5 border border-sage-200 dark:border-sage-700">
                                    <span class="rounded-full bg-sage-500 dark:bg-sage-400 w-1.5 h-1.5"></span>
                                </span>
                            </span>
                            <span>
                                Watch video tutorials at
                                <a href="https://laracasts.com" target="_blank" class="inline-flex items-center gap-1 font-medium underline underline-offset-4 text-sage-600 dark:text-sage-400 hover:text-sage-700 dark:hover:text-sage-300 transition-colors ml-1">
                                    <span>Laracasts</span>
                                    <svg width="10" height="11" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5">
                                        <path d="M7.70833 6.95834V2.79167H3.54167M2.5 8L7.5 3.00001" stroke="currentColor" stroke-linecap="square"/>
                                    </svg>
                                </a>
                            </span>
                        </li>
                    </ul>
                    
                    <!-- Primary Action Button: Sage Green -->
                    <ul class="flex gap-3 text-sm">
                        <li>
                            <a href="https://cloud.laravel.com" target="_blank" 
                               class="inline-block px-5 py-1.5 bg-sage-600 hover:bg-sage-700 dark:bg-sage-500 dark:hover:bg-sage-600 text-white rounded-sm font-medium transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-sage-500">
                                Deploy now
                            </a>
                        </li>
                    </ul>
                </div>
                
                <!-- Visual Panel: Sage Green Accent Background -->
                <div class="bg-sage-50 dark:bg-sage-900 relative lg:-ml-px -mb-px lg:mb-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg aspect-[335/376] lg:aspect-auto w-full lg:w-[438px] shrink-0 overflow-hidden border border-sage-100 dark:border-sage-800">
                    
                    {{-- Laravel Logo - Updated to Sage Green --}}
                    <svg class="w-full text-sage-600 dark:text-sage-400 transition-all max-w-none" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="currentColor"/>
                        <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z" fill="currentColor"/>
                        <path d="M242.805 41.6337C240.611 38.1275 237.494 35.3731 233.455 33.3681C229.416 31.3647 225.351 30.3618 221.262 30.3618C215.974 30.3618 211.138 31.3389 206.75 33.2923C202.36 35.2456 198.597 37.928 195.455 41.3333C192.314 44.7401 189.869 48.6726 188.125 53.1293C186.378 57.589 185.507 62.274 185.507 67.1813C185.507 72.1925 186.378 76.8995 188.125 81.3069C189.868 85.7173 192.313 89.6241 195.455 93.0293C198.597 96.4361 202.361 99.1155 206.75 101.069C211.138 103.022 215.974 103.999 221.262 103.999C225.351 103.999 229.416 102.997 233.455 100.994C237.494 98.9911 240.611 96.2359 242.805 92.7282V102.195H259.112V32.1642H242.805V41.6337ZM241.31 75.7472C240.312 78.4531 238.916 80.8078 237.122 82.8112C235.326 84.8161 233.156 86.4183 230.614 87.6206C228.07 88.823 225.251 89.4238 222.16 89.4238C219.068 89.4238 216.275 88.823 213.782 87.6206C211.289 86.4183 209.145 84.8161 207.35 82.8112C205.554 80.8078 204.182 78.4531 203.236 75.7472C202.286 73.0421 201.814 70.1868 201.814 67.1821C201.814 64.1758 202.286 61.3205 203.236 58.6154C204.182 55.9102 205.554 53.5571 207.35 51.5522C209.145 49.5495 211.288 47.9451 213.782 46.7427C216.275 45.5404 219.068 44.9396 222.16 44.9396C225.251 44.9396 228.07 45.5404 230.614 46.7427C233.156 47.9451 235.326 49.5487 237.122 51.5522C238.917 53.5571 240.312 55.9102 241.31 58.6154C242.306 61.3205 242.806 64.1758 242.806 67.1821C242.805 70.1868 242.305 73.0421 241.31 75.7472Z" fill="currentColor"/>
                        <path d="M438 -3H421.694V102.197H438V-3Z" fill="currentColor"/>
                        <path d="M139.43 102.197H155.735V48.2834H183.712V32.1665H139.43V102.197Z" fill="currentColor"/>
                        <path d="M324.49 32.1665L303.995 85.794L283.498 32.1665H266.983L293.748 102.197H314.242L341.006 32.1665H324.49Z" fill="currentColor"/>
                        <path d="M376.571 30.3656C356.603 30.3656 340.797 46.8497 340.797 67.1828C340.797 89.6597 356.094 104 378.661 104C391.29 104 399.354 99.1488 409.206 88.5848L398.189 80.0226C398.183 80.031 389.874 90.9895 377.468 90.9895C363.048 90.9895 356.977 79.3111 356.977 73.269H411.075C413.917 50.1328 398.775 30.3656 376.571 30.3656ZM357.02 61.0967C357.145 59.7487 359.023 43.3761 376.442 43.3761C393.861 43.3761 395.978 59.7464 396.099 61.0967H357.02Z" fill="currentColor"/>
                    </svg>

                    {{-- Decorative Sage Pattern (Simplified) --}}
                    <div class="absolute inset-0 flex items-center justify-center opacity-10 dark:opacity-20">
                        <svg viewBox="0 0 200 200" class="w-full h-full text-sage-500" fill="currentColor">
                            <pattern id="sage-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                                <circle cx="20" cy="20" r="3" fill="currentColor"/>
                            </pattern>
                            <rect width="100%" height="100%" fill="url(#sage-pattern)"/>
                        </svg>
                    </div>
                    
                    {{-- Inner Border --}}
                    <div class="absolute inset-0 rounded-t-lg lg:rounded-t-none lg:rounded-r-lg shadow-inner pointer-events-none"></div>
                </div>
            </main>
        </div>

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>