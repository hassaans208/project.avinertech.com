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

    <!-- Security Meta Tags -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://apis.google.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;">

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- Styles -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-900 text-white min-h-screen flex">

<!-- Header -->
<header class="px-6 fixed py-3 w-full bg-slate-800 flex items-center justify-between">
    <div class="flex items-center">
        <img src="{{ asset('/images/logo.png') }}" width="12%" />
        <p class="text-2xl text-white">AvinerTech</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="#" class="text-gray-400 underline">What we do?</a>
        <a href="/services" class="text-gray-400 underline">Service References</a>
        <a href="/api-reference" class="text-gray-400 underline">API References</a>
    </div>
</header>

<!-- Main Content -->
<main class="flex-1 p-6 mt-16">
    <h1 class="text-4xl font-bold mb-6 text-center text-blue-400">What We Do</h1>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">Who We Are</h2>
        <p class="text-gray-400 text-lg mt-2">
            AvinerTech is a leading provider of **cloud storage, AI-powered image processing, database management**,
            and **custom software development solutions**. Our mission is to deliver **scalable, secure, and AI-driven**
            technology solutions tailored to modern businesses.
        </p>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">🌐 Cloud Storage & Secure File Sharing</h2>
        <p class="text-gray-400 text-lg mt-2">
            We provide **high-speed, secure, and scalable cloud storage** that allows businesses and individuals
            to **store, access, and share files effortlessly**. Our platform ensures **end-to-end encryption**,
            **automated backups**, and **seamless integration** with existing systems.
        </p>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li>High-speed and scalable cloud storage</li>
            <li>Secure file sharing with **encryption & access controls**</li>
            <li>Real-time synchronization across devices</li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">🖼️ AI-Powered Image Processing & Upscaling</h2>
        <p class="text-gray-400 text-lg mt-2">
            Our **AI-driven image processing solutions** allow users to **enhance, upscale, and optimize images**
            for better clarity and performance.
        </p>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li>Image **upscaling** without losing quality</li>
            <li>AI-based **image restoration & enhancement**</li>
            <li>Cloud-based processing for **instant access**</li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">💾 Enterprise Database Management</h2>
        <p class="text-gray-400 text-lg mt-2">
            Our **database management solutions** ensure **high-performance, secure, and optimized** database
            architectures for businesses handling **large-scale data**.
        </p>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li>SQL & NoSQL database management</li>
            <li>Automated **backups & disaster recovery**</li>
            <li>Performance optimization for **high-speed querying**</li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">🛠️ Custom Software Development</h2>
        <p class="text-gray-400 text-lg mt-2">
            Whether you need a **SaaS platform, AI-powered automation tools, or enterprise software**, we specialize
            in **building tailored solutions** to **streamline operations and improve efficiency**.
        </p>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li>Custom **SaaS application development**</li>
            <li>Enterprise **ERP & automation software**</li>
            <li>AI-driven **business automation tools**</li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg mb-8">
        <h2 class="text-2xl font-semibold text-white">🚀 Why Choose AvinerTech?</h2>
        <ul class="list-disc ml-6 mt-3 text-gray-300">
            <li>**Security & Compliance** – Adhering to the highest industry standards.</li>
            <li>**Scalable & Future-Ready** – Cloud and AI-driven technologies.</li>
            <li>**Enterprise-Level Support** – Dedicated team for consultation & development.</li>
        </ul>
    </section>

    <section class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-white">📩 Get In Touch</h2>
        <p class="text-gray-400 text-lg mt-2">
            Looking for a **custom-built solution** for your business?
            Reach out to **AvinerTech** and let’s build something amazing together.
        </p>
    </section>
</main>

</body>
</html>
