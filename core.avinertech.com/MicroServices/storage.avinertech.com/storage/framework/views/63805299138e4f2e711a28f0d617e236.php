<header class="px-6 py-4 fixed top-0 w-full bg-gray-800 shadow-lg flex items-center justify-between z-50">
    <!-- Logo & Brand -->
    <div class="flex items-center gap-3">
        <img src="<?php echo e(asset('/images/logo.png')); ?>" class="w-10 h-10">
        <p class="text-2xl font-semibold text-white">AvinerTech</p>
    </div>

    <!-- Desktop Navigation -->
    <nav class="hidden md:flex gap-6 text-gray-400 text-lg">
        <a href="/" class="hover:text-white transition">Home</a>
        <a href="<?php echo e(url('/what-we-do')); ?>" class="hover:text-white transition">What We Do?</a>
        <a href="<?php echo e(url('https://developers.avinertech.com/')); ?>" target="_blank" class="hover:text-white transition">API References</a>
        <a href="<?php echo e(url('/service-references')); ?>" class="hover:text-white transition text-blue-400">Cloud Storage</a>
    </nav>

    <!-- Mobile Menu Button -->
    <button id="menu-toggle" class="md:hidden text-white focus:outline-none">
        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path id="menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
    </button>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden absolute top-16 left-0 w-full bg-gray-800 shadow-md p-4 flex flex-col gap-4 text-lg md:hidden">
        <a href="/" class="text-gray-400 hover:text-white transition">Home</a>
        <a href="<?php echo e(url('/what-we-do')); ?>" class="text-gray-400 hover:text-white transition">What We Do?</a>
        <a href="<?php echo e(url('https://developers.avinertech.com/')); ?>" class="text-gray-400 hover:text-white transition">API References</a>
        <a href="<?php echo e(url('/service-references')); ?>" class="text-blue-400 hover:text-white transition">Cloud Storage</a>
    </div>
</header>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const menuToggle = document.getElementById("menu-toggle");
        const mobileMenu = document.getElementById("mobile-menu");

        menuToggle.addEventListener("click", function () {
            if (mobileMenu.classList.contains("hidden")) {
                mobileMenu.classList.remove("hidden");
                mobileMenu.classList.add("flex");
            } else {
                mobileMenu.classList.add("hidden");
                mobileMenu.classList.remove("flex");
            }
        });
    });
</script>

<?php /**PATH E:\xampp-8\htdocs\Project\core.avinertech.com\MicroServices\storage.avinertech.com\resources\views/partials/header.blade.php ENDPATH**/ ?>