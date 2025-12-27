<?php

declare(strict_types=1);

$errors = [];
$successMessage = null;

if (isset($_GET['success'])) {
    $successMessage = 'Thank you! Your message has been saved.';
}

if (isset($_POST['name'], $_POST['email'], $_POST['message'])) {
    $name = trim((string) $_POST['name']);
    $email = trim((string) $_POST['email']);
    $message = trim((string) $_POST['message']);

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if ($email === '') {
        $errors[] = 'Email is required.';
    } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Invalid email address.';
    }

    if ($message === '') {
        $errors[] = 'Message is required.';
    }

    if ($errors === []) {
        $entry = [
            'created_at' => date('c'),
            'name' => $name,
            'email' => $email,
            'message' => $message,
        ];

        $filePath = __DIR__ . '/storage/messages.jsonl';
        $line = json_encode($entry, JSON_UNESCAPED_UNICODE) . PHP_EOL;

        file_put_contents($filePath, $line, FILE_APPEND | LOCK_EX);

        header('Location: contact.php?success=1');
        exit;
    }
}

require __DIR__ . '/src/header.php'; ?>

<main class="sayHello">
    <section class="contact">
        <div class="contactBox">
            <h1 class="contact-title">CONTACT US</h1>

            <form class="contact-form" method="post" action="">
                <label class="contact-label">
                    Your name
                    <input
                        class="contact-input"
                        type="text"
                        name="name"
                        required>
                </label>

                <label class="contact-label">
                    Your email
                    <input
                        class="contact-input"
                        type="email"
                        name="email"
                        required>
                </label>

                <p class="contact-hint">
                    Looking for a specific feature?
                    Please include the feature name and your dates in January 2026.
                </p>

                <label class="contact-label">
                    Message
                    <textarea
                        class="contact-textarea"
                        name="message"
                        required></textarea>
                </label>

                <?php if ($successMessage !== null): ?>
                    <p class="success"><?= htmlspecialchars($successMessage) ?></p>
                <?php endif; ?>

                <button class="contact-button" type="submit">
                    Send request
                </button>
            </form>

        </div>
    </section>
</main>

<?php require __DIR__ . '/src/footer.php'; ?>