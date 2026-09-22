<template>
    <!-- 1. Background Laptop (Abu-abu gelap/terang biar kontras) -->
    <div class="flex min-h-screen justify-center overflow-y-auto bg-gray-100">
        <!-- 2. Mobile Container (Ini Layar HP-nya) -->
        <div
            class="relative flex min-h-screen w-full max-w-[480px] flex-col bg-white font-sans shadow-2xl"
        >
            <!-- Slot untuk Konten Halaman -->
            <main class="flex-1 pb-24">
                <!-- pb-24 biar konten paling bawah ga ketutup navbar -->
                <slot />
            </main>

            <!-- 3. Bottom Navigation (Sticky di Bawah Container HP) -->
            <nav
                class="fixed bottom-0 z-50 flex w-full max-w-[480px] items-center justify-around border-t border-gray-100 bg-white px-2 py-3 shadow-[0_-5px_10px_rgba(0,0,0,0.02)]"
            >
                <button
                    class="flex w-16 flex-col items-center gap-1 text-green-500"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z"
                        />
                        <path
                            d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z"
                        />
                    </svg>
                    <span class="text-[10px] font-medium">Home</span>
                </button>

                <button
                    class="flex w-16 flex-col items-center gap-1 text-gray-400 transition hover:text-green-500"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                        />
                    </svg>
                    <span class="text-[10px] font-medium">Jadwal</span>
                </button>

                <button
                    class="flex w-16 flex-col items-center gap-1 text-gray-400 transition hover:text-green-500"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                        />
                    </svg>
                    <span class="text-[10px] font-medium">Reservasi</span>
                </button>

                <button
                    class="flex w-16 flex-col items-center gap-1 text-gray-400 transition hover:text-green-500"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        />
                    </svg>
                    <span class="text-[10px] font-medium">Profil</span>
                </button>
            </nav>
        </div>
    </div>
</template>
