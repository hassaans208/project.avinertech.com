<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Title & Description -->
    <title>Cloud Storage Service | AvinerTech</title>
    <meta name="description" content="Secure and scalable cloud storage service by AvinerTech, offering file encryption, SSH authentication, and secure data transfers.">

    <!-- Keywords -->
    <meta name="keywords" content="Cloud Storage, Secure Storage, File Upload, SSH Authentication, Secure Data Transfer, AvinerTech">

    <!-- Open Graph -->
    <meta property="og:title" content="Cloud Storage Service | AvinerTech">
    <meta property="og:description" content="Explore AvinerTech’s cloud storage service with top-tier security and tech-driven architecture.">
    <meta property="og:url" content="https://avinertech.com/cloud-storage">
    <meta property="og:image" content="https://avinertech.com/storage-preview.png">

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
<header class="px-8 py-4 fixed top-0 w-full bg-gray-800 shadow-lg flex items-center justify-between z-50">
    <div class="flex items-center gap-3">
        <img src="<?php echo e(asset('/images/logo.png')); ?>" class="w-10 h-10">
        <p class="text-2xl font-semibold text-white">AvinerTech</p>
    </div>
    <nav class="flex gap-6 text-gray-400 text-lg">
        <a href="/" class="hover:text-white transition">Home</a>
        <a href="<?php echo e(url('/what-we-do')); ?>" class="hover:text-white transition">What We Do?</a>
        <a href="<?php echo e(url('https://developers.avinertech.com/')); ?>" class="hover:text-white transition">API References</a>
        <a href="<?php echo e(url('/service-references')); ?>" class="hover:text-white transition text-blue-400">Cloud Storage</a>
    </nav>
</header>

<!-- Main Content -->
<main class="flex-1 p-10 mt-20">
    <div class="bg-gray-800 p-10 rounded-xl shadow-xl max-w-5xl mx-auto">
        <h1 class="text-4xl font-bold text-center text-blue-400 mb-6">Cloud Storage Service</h1>

        <!-- General Public Section -->
        <section class="mb-10">
            <h2 class="text-2xl font-semibold text-white mb-3">🌍 For Everyone</h2>
            <p class="text-lg text-gray-300 leading-relaxed">
                AvinerTech's <strong>Cloud Storage</strong> offers a <strong>secure, fast, and reliable</strong> way to store,
                access, and share your files from anywhere. With <strong>end-to-end encryption</strong> and
                <strong>real-time synchronization</strong>, your data stays protected while remaining accessible across all your devices.
            </p>
            <ul class="list-disc ml-6 mt-3 text-gray-300">
                <li><strong>Secure file storage</strong> with advanced encryption</li>
                <li><strong>Easy file sharing</strong> via public & private links</li>
                <li><strong>Instant access</strong> from any device</li>
                <li><strong>Automatic backups</strong> to prevent data loss</li>
            </ul>
        </section>

        <!-- Tech People Section -->
        <section>
            <h2 class="text-2xl font-semibold text-white mb-3">⚙️ Technical Overview</h2>
            <p class="text-lg text-gray-300 leading-relaxed">
                AvinerTech’s Cloud Storage uses a <strong>secure SSH-based authentication system</strong> to ensure that
                only authorized users can upload files. Our architecture is designed with <strong>multi-layer security checks</strong>
                and <strong>tunnel-based file transfers</strong> between dedicated servers.
            </p>

            <!-- Authentication Process -->
            <div class="mt-6">
                <h3 class="text-xl font-semibold text-blue-400">🔒 SSH Authentication & Security Checks</h3>
                <p class="text-gray-300 text-lg mt-2">
                    Every file upload is authenticated through an <strong>SSH-secured connection</strong>. This ensures
                    that unauthorized access is prevented before any data is written to the storage server.
                </p>
                <ul class="list-disc ml-6 mt-3 text-gray-300">
                    <li>Clients authenticate via <strong>SSH keys</strong> before initiating uploads</li>
                    <li>Each file undergoes <strong>integrity checks</strong> to ensure security compliance</li>
                    <li>Unauthorized files are rejected before reaching the main server</li>
                </ul>
            </div>

            <!-- File Transfer Architecture -->
            <div class="mt-6">
                <h3 class="text-xl font-semibold text-blue-400">📡 Secure Tunneling Between Servers</h3>
                <p class="text-gray-300 text-lg mt-2">
                    To prevent direct exposure of our storage infrastructure, file uploads are processed through a
                    <strong>secure tunnel</strong> between multiple servers before reaching final storage.
                </p>
                <ul class="list-disc ml-6 mt-3 text-gray-300">
                    <li>Files are initially uploaded to an <strong>intermediate server</strong></li>
                    <li>The server performs <strong>security scans</strong> before forwarding the file</li>
                    <li>Once verified, the file is <strong>transferred via a secure tunnel</strong> to the storage server</li>
                    <li>This ensures <strong>zero direct exposure</strong> of the main storage server</li>
                </ul>
            </div>
            <!-- Code Sample for Tech Users -->












        </section>
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

<script>
    function copyToClipboard(elementId) {
        let text = document.getElementById(elementId).innerText.trim();
        navigator.clipboard.writeText(text).then(() => {
            alert("Copied to clipboard!");
        }).catch(err => {
            console.error("Error copying text: ", err);
        });
    }
</script>

</body>
</html>
<?php /**PATH E:\xampp-8\htdocs\Project\core.avinertech.com\MicroServices\storage.avinertech.com\resources\views/service-refs.blade.php ENDPATH**/ ?>