<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Title & Description -->
    <title>What We Do | AvinerTech</title>
    <meta name="description" content="AvinerTech provides cutting-edge cloud storage, AI-powered image processing, database management, and custom software development solutions.">

    <!-- Keywords -->
    <meta name="keywords" content="AvinerTech, Cloud Storage, Image Processing, Database Management, Custom Software Development, AI Solutions">

    <!-- Author -->
    <meta name="author" content="AvinerTech">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://avinertech.com/what-we-do">

    <!-- Open Graph (Facebook & LinkedIn) -->
    <meta property="og:title" content="What We Do | AvinerTech">
    <meta property="og:description" content="Explore AvinerTech’s range of services including cloud storage, AI-powered image processing, and enterprise software solutions.">
    <meta property="og:url" content="https://avinertech.com/what-we-do">
    <meta property="og:image" content="https://avinertech.com/preview.png">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" href="https://avinertech.com/favicon.ico" type="image/x-icon">

    <!-- Styles -->
    <?php if(file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'))): ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php endif; ?>

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col">

<!-- Header -->
<header class="px-8 py-4  top-0 w-full bg-gray-800 shadow-lg flex items-center justify-between z-50">
    <div class="flex items-center gap-3">
        <img src="<?php echo e(asset('/images/logo.png')); ?>" class="w-10 h-10">
        <p class="text-2xl font-semibold text-white">AvinerTech</p>
    </div>
    <nav class="flex gap-6 text-gray-400 text-lg">
        <a href="<?php echo e(url('/what-we-do')); ?>" class="hover:text-white transition">What we do?</a>
        <a href="<?php echo e(url('/service-references')); ?>" class="hover:text-white transition">Service References</a>
        <a href="<?php echo e(url('https://developers.avinertech.com/')); ?>" class="hover:text-white transition">API References</a>
    </nav>
</header>

<!-- Main Content -->
<main class="flex-1 p-10 mt-20">
    <div class="bg-gray-800 p-10 rounded-xl shadow-xl max-w-5xl mx-auto">
        <h1 class="text-4xl font-bold text-center text-blue-400 mb-6">What We Do</h1>

        <p class="text-lg text-gray-300 mb-6 leading-relaxed">
            <strong>AvinerTech</strong> is a leading provider of <strong>cloud storage</strong>, <strong>AI-powered image processing</strong>,
            <strong>database management</strong>, and <strong>custom software development solutions</strong>. Our mission is to deliver
            <strong>scalable, secure, and AI-driven</strong> technology solutions tailored to modern businesses.
        </p>

        <div class="space-y-8">
            <div>
                <h2 class="text-2xl font-bold flex items-center text-white mb-2">
                    🌐 Cloud Storage & Secure File Sharing
                </h2>
                <p class="text-gray-400 text-lg leading-relaxed">
                    We provide <strong>high-speed, secure, and scalable cloud storage</strong> that allows businesses and individuals
                    to <strong>store, access, and share files effortlessly</strong>. Our platform ensures
                    <strong>end-to-end encryption, automated backups</strong>, and <strong>seamless integration</strong>.
                </p>
                <ul class="list-disc ml-6 mt-3 text-gray-300">
                    <li>High-speed and scalable cloud storage</li>
                    <li>Secure file sharing with <strong>encryption & access controls</strong></li>
                    <li>Real-time synchronization across devices</li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold flex items-center text-white mb-2">
                    🖼️ AI-Powered Image Processing & Upscaling
                </h2>
                <p class="text-gray-400 text-lg leading-relaxed">
                    Our <strong>AI-driven image processing solutions</strong> allow users to <strong>enhance, upscale, and optimize images</strong>
                    for better clarity and performance.
                </p>
                <ul class="list-disc ml-6 mt-3 text-gray-300">
                    <li>Image <strong>upscaling</strong> without losing quality</li>
                    <li>AI-based <strong>image restoration & enhancement</strong></li>
                    <li>Cloud-based processing for <strong>instant access</strong></li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold flex items-center text-white mb-2">
                    💾 Enterprise Database Management
                </h2>
                <p class="text-gray-400 text-lg leading-relaxed">
                    Our <strong>database management solutions</strong> ensure <strong>high-performance, secure, and optimized</strong>
                    database architectures for businesses handling <strong>large-scale data</strong>.
                </p>
                <ul class="list-disc ml-6 mt-3 text-gray-300">
                    <li>SQL & NoSQL database management</li>
                    <li>Automated <strong>backups & disaster recovery</strong></li>
                    <li>Performance optimization for <strong>high-speed querying</strong></li>
                </ul>
            </div>

            <div>
                <h2 class="text-2xl font-bold flex items-center text-white mb-2">
                    🛠️ Custom Software Development
                </h2>
                <p class="text-gray-400 text-lg leading-relaxed">
                    Whether you need a <strong>SaaS platform, AI-powered automation tools, or enterprise software</strong>, we specialize
                    in <strong>building tailored solutions</strong> to <strong>streamline operations and improve efficiency</strong>.
                </p>
                <ul class="list-disc ml-6 mt-3 text-gray-300">
                    <li>Custom <strong>SaaS application development</strong></li>
                    <li>Enterprise <strong>ERP & automation software</strong></li>
                    <li>AI-driven <strong>business automation tools</strong></li>
                </ul>
            </div>
        </div>

        <div class="mt-8">
            <h2 class="text-2xl font-bold text-white">📩 Get In Touch</h2>
            <p class="text-lg text-gray-400 leading-relaxed mt-2">
                Looking for a <strong>custom-built solution</strong> for your business?
                Reach out to <strong>AvinerTech</strong> and let’s build something amazing together.
            </p>
        </div>
    </div>
</main>

</body>
</html>
<?php /**PATH E:\xampp-8\htdocs\Project\core.avinertech.com\MicroServices\storage.avinertech.com\resources\views\what-we-do.blade.php ENDPATH**/ ?>