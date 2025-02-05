<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $__env->make('partials.meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('partials.configuration', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body class="bg-gray-900 text-white min-h-screen flex">
<?php echo $__env->make('partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- Main Content -->
<main class="flex-1 p-6 mt-16">
    <h1 class="text-4xl font-bold mb-6 text-center text-blue-400">What We Do</h1>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">Who We Are</h2>
        <p class="text-gray-400 text-lg mt-2">
            AvinerTech is a leading provider of <strong>cloud storage, AI-powered image processing, database management</strong>,
            and <strong>custom software development solutions</strong>. Our mission is to deliver <strong>scalable, secure, and AI-driven</strong>
            technology solutions tailored to modern businesses.
        </p>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">🌐 Cloud Storage & Secure File Sharing</h2>
        <p class="text-gray-400 text-lg mt-2">
            We provide <strong>high-speed, secure, and scalable cloud storage</strong> that allows businesses and individuals
            to <strong>store, access, and share files effortlessly</strong>. Our platform ensures <strong>end-to-end encryption</strong>,
            <strong>automated backups</strong>, and <strong>seamless integration</strong> with existing systems.
        </p>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li>High-speed and scalable cloud storage</li>
            <li>Secure file sharing with <strong>encryption & access controls<strong></li>
            <li>Real-time synchronization across devices</li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">🖼️ AI-Powered Image Processing & Upscaling</h2>
        <p class="text-gray-400 text-lg mt-2">
            Our <strong>AI-driven image processing solutions</strong> allow users to <strong>enhance, upscale, and optimize images</strong>
            for better clarity and performance.
        </p>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li>Image <strong>upscaling</strong> without losing quality</li>
            <li>AI-based <strong>image restoration & enhancement</strong></li>
            <li>Cloud-based processing for <strong>instant access</strong></li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">💾 Enterprise Database Management</h2>
        <p class="text-gray-400 text-lg mt-2">
            Our <strong>database management solutions</strong> ensure <strong>high-performance, secure, and optimized</strong> database
            architectures for businesses handling <strong>large-scale data</strong>.
        </p>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li>SQL & NoSQL database management</li>
            <li>Automated <strong>backups & disaster recovery</strong></li>
            <li>Performance optimization for <strong>high-speed querying</strong></li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">🛠️ Custom Software Development</h2>
        <p class="text-gray-400 text-lg mt-2">
            Whether you need a <strong>SaaS platform, AI-powered automation tools, or enterprise software</strong>, we specialize
            in <strong>building tailored solutions</strong> to <strong>streamline operations and improve efficiency</strong>.
        </p>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li>Custom <strong>SaaS application development</strong></li>
            <li>Enterprise <strong>ERP & automation software</strong></li>
            <li>AI-driven <strong>business automation tools</strong></li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">🚀 Why Choose AvinerTech?</h2>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li><strong>Security & Compliance</strong> – Adhering to the highest industry standards.</li>
            <li><strong>Scalable & Future-Ready</strong> – Cloud and AI-driven technologies.</li>
            <li><strong>Enterprise-Level Support</strong> – Dedicated team for consultation & development.</li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-white">📩 Get In Touch</h2>
        <p class="text-gray-400 text-lg mt-2">
            Looking for a <strong>custom-built solution</strong> for your business?
            Reach out to <strong>AvinerTech</strong> and let’s build something amazing together.
        </p>
        <a href="mailto:sales@avinertech.com" class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg text-lg mt-4">
            Contact Us
        </a>
    </section>
</main>

</body>
</html>
<?php /**PATH E:\xampp-8\htdocs\Project\core.avinertech.com\Services\developers.avinertech.com\resources\views/what-we-do.blade.php ENDPATH**/ ?>