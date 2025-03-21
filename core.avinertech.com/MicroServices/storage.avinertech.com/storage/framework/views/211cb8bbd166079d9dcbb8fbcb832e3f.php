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

<?php echo $__env->make('partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

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

        <hr class="mt-7">

        <!-- Get In Touch -->
        <div class="mt-10 text-center">
            <h2 class="text-2xl font-semibold text-white">📩 Get In Touch</h2>
            <p class="text-lg text-gray-300 leading-relaxed mt-2">
                Need a custom cloud storage solution? Our team is ready to help.
                Contact us to discuss your storage requirements and security needs.
            </p>
            <a href="mailto:sales@avinertech.com" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg text-lg mt-4">
                Contact Us
            </a>
        </div>
    </div>
</main>
</body>
</html>
<?php /**PATH E:\xampp-8\htdocs\Project\core.avinertech.com\MicroServices\storage.avinertech.com\resources\views/what-we-do.blade.php ENDPATH**/ ?>